<?php
    class Recipes {
        function __construct() {
            add_action('init', array($this, 'init'));
        }
    
        public function init() {
            register_post_type('Recipes', array(
                'label' => 'Recipes',
                'labels' => array(
                    'name' => 'Recipes',
                    'singular_name' => 'Recipe'
                ),
                'public' => true,
                'supports' => array('title', 'revisions'),
                'has_archive' => true,
                // 'rewrite' => array(
                //     'slug' => 'events',
                //     'with_front' => false
                // )
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
    }

    new Recipes();
?>