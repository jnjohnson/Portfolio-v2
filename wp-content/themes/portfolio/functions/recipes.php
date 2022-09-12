<?php
    class Recipes {
        function __construct() {
            add_action('init', array($this, 'init'));
            add_action('acf/save_post', array($this, 'recipe_image_OCR'));
        }
    
        public function init() {
            register_post_type('Recipes', array(
                'label' => 'Recipes',
                'labels' => array(
                    'name' => 'Recipes',
                    'singular_name' => 'Recipe'
                ),
                'public' => true,
                'supports' => array('title', 'editor', 'revisions'),
                'has_archive' => true,
                'rewrite' => array(
                    'slug' => 'recipes',
                    'with_front' => false
                )
            ));
    
            register_taxonomy('meals', 'recipes',
                array(
                    'labels' => array(
                        'name' => 'Meals',
                        'singular_name' => 'Meal'
                    ),
                    'show_ui' => true,
                    'public' => true,
                    'hierarchical' => true,
                    'show_admin_column' => true
                )
            );
            register_taxonomy('cuisine', 'recipes',
                array(
                    'labels' => array(
                        'name' => 'Cuisine'
                    ),
                    'show_ui' => true,
                    'public' => true,
                    'hierarchical' => true,
                    'show_admin_column' => true
                )
            );
        }
        public function recipe_image_OCR($post_id) {
            $saved_post = get_post($post_id);
            if ($saved_post->post_type != 'recipes') {
                return;
            }
            $img_url = get_fields($post_id)['recipe_image'];

            // Checks if the image was updated when the post was saved
            if (!empty($img_url)) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://freeocrapi.com/api',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($img_url))));
                $response = json_decode(curl_exec($curl), true);
                curl_close($curl);
                wp_update_post(['post_content' => strtolower($response['text'])]);
            }
        }
    }

    new Recipes();
?>