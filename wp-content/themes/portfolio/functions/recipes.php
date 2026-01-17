<?php
    class Recipes {
        function __construct() {
            add_action('init', array($this, 'init'));
            add_action('acf/save_post', array($this, 'recipe_image_OCR'));
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
                )
            ));
            
            register_taxonomy('source', 'recipes', 
                array(
                    'labels'            => array(
                        'name'          => 'Source',
                    ),
                    'show_ui'           => true,
                    'public'            => true,
                    'hierarchical'      => true,
                    'show_admin_column' => true
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
                    'show_admin_column' => true
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
                    'show_admin_column' => true
                )
            );
        }

        // Detects if the image has been updated.
        // If yes, use OCR API to get all of the text in the image
        public function recipe_image_OCR($post_id) {
            $log = fopen("log.txt", "w") or die("unable to open file");
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

                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://apis-freeocr-ai.p.rapidapi.com/extract?fields=%5B%22title%22%2C%20%22page%20number%22%2C%20%22servings%22%2C%22cook%20time%22%2C%22ingredients%22%5D&format=json",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "-----011000010111000001101001
                        Content-Disposition: form-data; name='image'"
                        .$img_url.
                        "-----011000010111000001101001--",
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: multipart/form-data; boundary=---011000010111000001101001",
                        "x-rapidapi-host: apis-freeocr-ai.p.rapidapi.com",
                        "x-rapidapi-key:" . get_option('FREE_OCR_API_KEY')
                    ],
                ]);

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                    fwrite($log, "cURL Error #:" . $err);
                    echo "cURL Error #:" . $err;
                } else {
                    fwrite($log, "response: " . $response);
                    echo $response;
                }
                // $this->save_recipe_parts_to_acf_fields($response['text'], $post_id);
            }
        }

        // Saves the recipe name, slug, page number, servings, recipe content, and total time to the post
        // There's too much variation in how recipes are formatted (even within a book) to be able to 
        // come up with rules to parse things out perfectly without using ML.
        // Maybe I'll try to learn TensorFlow for the next part of this...
        public function save_recipe_parts_to_acf_fields($recipe, $post_id) {
            $lines = preg_split("/\r\n|\n|\r/", $recipe);
            array_pop($lines); // Removes the last line, which has an undefined character in it
            $line_count = sizeof($lines);

            // Gets the page number, which should be either the first or last index of the array.
            // Removing that index normalizes the array so future operations don't depend on which 
            // orientation the array is in.
            // In the instance that the page number is in the last index, the OCR software puts the
            // name of the section and the page number on the same line, with the page number being last
            if (intval($lines[0])) {
                $page = array_shift($lines);
            } else {
                $page_arr = explode(' ', array_pop($lines));
                $page = $page_arr[sizeof($page_arr) - 1];

                if (!intval($page)) {
                    $page = '';
                }
            }
            foreach ($lines as $ln => $line) {
                if (strpos($line, 'SERVES') !== false) {
                    break;
                }
            }
            $title = ucwords(strtolower(implode(' ', array_slice($lines, 0, $ln))));
            // Get the servings and total time
            $split = explode('TOTAL TIME:', $lines[$ln]);
            $servings = $split[0];
            $time = $split[1];
            wp_update_post([
                'ID'            => $post_id,
                'post_title'    => $title,
                'post_content'  => $recipe,
                'post_name'     => str_replace(' ', '-', strtolower($title))
            ]);
            
            update_field('page', $page, $post_id);
            update_field('servings', $servings, $post_id);
            update_field('time', $time, $post_id);
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