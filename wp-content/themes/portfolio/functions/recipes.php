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
                
                $this->save_recipe_parts_to_acf_fields($response['text'], $post_id);
            }
        }
        public function save_recipe_parts_to_acf_fields($recipe, $post_id) {
            $description_end = $ingredients_end = 0;
            $in_ingredients = $in_instructions = false;
            $verbs = ['add', 'adjust', 'bring', 'remove', 'whisk'];
            $recipe = get_the_content();
            $lines = preg_split("/\r\n|\n|\r/", $recipe);
            $line_count = sizeof($lines);

            // Gets the page number, which should be either the first or last index of the array.
            // Removing that index normalizes the array so future operations don't depend on which 
            // orientation the array is in.
            if (intval($lines[0])) {
                $page = array_shift($lines);
            } else if (intval($lines[$line_count - 1])) {
                $page = array_pop($lines);
            }
            $title = array_shift($lines);
            
            // Get the servings and total time
            $split = explode('total time:', array_shift($lines));
            $servings = $split[0];
            $time = $split[1];
            
            foreach ($lines as $i => $line) {
                $split = explode(' ', $line);

                // Checks to see if the first word in the line is a number, which will mean the line is either 
                // an ingredient or the first line of an instruction.
                // Checks the second word in the line to see if it is among the list of verbs. 
                // If it is, the line is the first line of an instruction.
                // Obviously this is going to be prone to bugs, but it's the best idea I have right now 
                // that doesn't use machine learning

                if (intval($split[0]) && !array_search($split[1], $verbs)) {
                    if (!$in_ingredients) {
                        $in_ingredients = true;
                        $description_end = $i;
                    }
                } else if (intval($split[0]) && array_search($split[1], $verbs)) {
                    if (!$in_instructions) {
                        $in_instructions = true;
                        $ingredients_end = $i;
                    }
                }
            }
            $description = implode(' ', array_slice($lines, 0, $description_end));
            $ingredients = implode("\r\n", array_slice($lines, $description_end, $ingredients_end  - $description_end));
            $instructions = implode("\r\n", array_slice($lines, $ingredients_end));
            wp_update_post(['post_title' => $title]);
            update_field('page', $page, $post_id);
            update_field('servings', $servings, $post_id);
            update_field('time', $time, $post_id);
            update_field('description', $description, $post_id);
            update_field('ingredients', $ingredients, $post_id);
            update_field('instructions', $instructions, $post_id);
        }
    }

    new Recipes();
?>