<?php
    class Recipes {
        function __construct() {
            add_action('init', array($this, 'init'));
            add_action('rest_after_insert_recipes', array($this, 'recipe_image_OCR'));
            add_action('pre_get_posts', array($this, 'filter_posts'));
        }
        
        public function init() {
            register_post_type('Recipes', array(
                'label'             => 'Recipes',
                'labels'            => array(
                    'name'          => 'Recipes',
                    'singular_name' => 'Recipe'
                ),
                'public'            => true,
                'supports'          => array('title', 'editor', 'revisions'),
                'has_archive'       => true,
                'rewrite'           => array(
                    'slug'          => 'recipes',
                    'with_front'    => false
                ),
                'show_in_rest'      => true
            ));
            
            register_taxonomy('source', 'recipes', 
                array(
                    'labels'            => array(
                        'name'          => 'Source',
                    ),
                    'show_ui'           => true,
                    'public'            => true,
                    'hierarchical'      => true,
                    'show_admin_column' => true,
                    'show_in_rest'      => true
                )
            );
            register_taxonomy('meals', 'recipes',
                array(
                    'labels'            => array(
                        'name'          => 'Meals',
                        'singular_name' => 'Meal'
                    ),
                    'show_ui'           => true,
                    'public'            => true,
                    'hierarchical'      => true,
                    'show_admin_column' => true,
                    'show_in_rest'      => true
                )
            );
            register_taxonomy('cuisine', 'recipes',
                array(
                    'labels'            => array(
                        'name'          => 'Cuisine'
                    ),
                    'show_ui'           => true,
                    'public'            => true,
                    'hierarchical'      => true,
                    'show_admin_column' => true,
                    'show_in_rest'      => true
                )
            );
        }

        // Detects if the image has been updated.
        // If yes, use OCR API to get all of the text in the image
        public function recipe_image_OCR($post_id) {
            remove_action('rest_after_insert_recipes', array($this, 'recipe_image_OCR'));
            $saved_post = get_post($post_id);
            if ($saved_post->post_type != 'recipes') {
                return;
            } else if (get_field('editing', $post_id)) {
		        return;
            }

            // Checks if the image was updated when the post was saved
            $img_id = get_fields($post_id)['recipe_image'];
            // $new_img_id = $_POST['acf']['field_631e6695786ff'];
            // if ($img_id == $new_img_id) return;

            $img_url = wp_get_attachment_image_src($img_id, 'full')[0];
            
            if (!empty($img_url)) {
                $curl = curl_init();
                $params = ['image'=> curl_file_create($img_url, 'image/jpg')];
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://apis-freeocr-ai.p.rapidapi.com/extract?fields=%5B%22title%22%2C%20%22page%20number%22%2C%20%22servings%22%2C%20%22cook%20time%22%2C%20%22ingredients%22%2C%20%22recipe%20steps%22%5D&format=json",
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $params,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: multipart/form-data",
                        "x-rapidapi-host: apis-freeocr-ai.p.rapidapi.com",
                        "x-rapidapi-key:" . FREE_OCR_API_KEY
                    ],
                ]);
            
                $response = json_decode(curl_exec($curl), true);
                $err = curl_error($curl);
                
                curl_close($curl);
                
                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    $this->save_recipe_parts_to_acf_fields($response, $post_id);
                }
            }
            add_action('rest_after_insert_recipes', array($this, 'recipe_image_OCR'));
        }

        // Saves the recipe name, slug, page number, servings,
        // recipe content, and total time to the post
        public function save_recipe_parts_to_acf_fields($response, $post_id) {
            foreach ($response as $index) {
                switch ($index['key']) {
                    case 'title':
                        $title = ucwords(strtolower(implode(' ', $index['values'])));
                        $id = wp_update_post([
                            'ID' => $post_id->ID,
                            'post_title' => $title,
                            'post_name' => str_replace(' ', '-', strtolower($title))
                        ]);
                        break;
                    case 'page number':
                        update_field('page', $index['values'][0], $post_id);
                        break;
                    case 'servings':
                        update_field('servings', strtolower($index['values'][0]), $post_id);
                        break;
                    case 'cook time':
                        update_field('time', strtolower($index['values'][0]), $post_id);
                        break;
                    case 'ingredients':
                        $ingredients = implode("\r\n", $index['values']);
                        update_field('ingredients', $ingredients, $post_id);
                        break;
                    case 'recipe steps':
                        $steps = implode("\r\n", $index['values']);
                        update_field('cooking_instructions', $steps, $post_id);
                        break;
                    default:
                        echo $index['key'];
                        break;
                }
            }
        }

        public function filter_posts($query) {
            if (!is_admin() && $query->is_main_query()) {
                $tax_query = array('relation' => 'AND');
                foreach ($_GET as $key => $val) {
                    array_push($tax_query, array(
                        'taxonomy'  => $key,
                        'field'     => 'slug',
                        'terms'     => explode(',', $val),
                    ));
                }
                $query->set('tax_query', $tax_query);
                $query->set('posts_per_page', 9);
            }
        }

        public static function build_source_string($terms) {
            $string = '';
            if (!empty($terms)) {
                foreach ($terms as $i => $term) {
                    if ($i == 0) {
                        $string = $term->name . ':</br>';
                    } else {
                        $string .= $term->name;
                    }
                }
            }
            return $string;
        }
    }

    new Recipes();
?>
