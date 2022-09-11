<?php

// function wpb_mce_buttons_2($buttons) {
//     array_unshift($buttons, 'styleselect');
//     return $buttons;
// }
// add_filter('mce_buttons_2', 'wpb_mce_buttons_2');

// function my_mce_before_init_insert_formats( $init_array ) {  
// 	$style_formats = array( 
// 		array( 	
// 			'title' => 'Button',
// 			'classes' => 'btn',
// 			'selector' => 'a'
// 		),
// 		array( 	
// 			'title' => 'Blue',
// 			'classes' => 'blue',
// 			'selector' => 'p'
// 		)
// 	);

// 	$init_array['style_formats'] = json_encode( $style_formats );  
     
//     return $init_array;  
// }
// add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' ); 

add_editor_style();