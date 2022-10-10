<?php
    /*
        Template Name: Recipes Archive
    */
    global $wp_query;
    ftheme::add_script('recipes.js');
	get_header();

    $filters = [
        'source'    => get_terms(['taxonomy' => 'source']),
        'meals'     => get_terms(['taxonomy' => 'meals']),
        'cuisine'   => get_terms(['taxonomy' => 'cuisine']),
    ];
?>
<div class="archive-recipes">
    <div class="hero">

    </div>
    <div class="main-content">
        <div class="wrap">
            <form id="search" name="search">
                <div>
                    <input type="search" name="s" placeholder="Search">
                    <div class="filters">
                    <?php
                        foreach ($filters as $key => $filter) {
                            echo '<div class="filter '.$key.'"><p>'.$key.'</p><div><div>';
                            
                            foreach ($filter as $term) {
                                $class = '';
                                if ($term->parent != 0) {
                                    $class = 'child';
                                }
                                echo <<<EOS
                                <label class="$class">
                                    <input name="$key" type="checkbox" value="{$term->slug}">
                                    <span>{$term->name}</span>
                                </label>
                                EOS;
                            }

                            echo '</div></div></div>'; // filter
                        }
                    ?>
                    </div>
                </div>
            </form>
            <?php
                echo '<div class="recipes recipes-start">';
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        $link = get_the_permalink();
                        $title = get_the_title();
                        $servings = get_field('servings');
                        $time = get_field('time');
                        $source = Recipes::build_source_string(get_the_terms($post, 'source'));
                        $img_id = get_field('food_image');
                        $image = '';
                        if (empty($img_id)) {
                            $image = '/wp-content/themes/portfolio/images/drumstick2-879-1080.jpg';
                        } else {
                            $image = wp_get_attachment_image_src($img_id, 'full')[0];
                        }
                        echo '<a href="'.$link.'" class="recipe">';
                            echo '<div class="image" style="background-image: url('.$image.')"></div>';
                            echo '<div class="meta"><h4>'.$title.'</h4>';
                            if (!empty($source)) {
                                echo '<h5>'.$source.'</h5>';
                            }
                            if (!empty($servings)) {
                                echo '<p class="servings">'.$servings.'</p>';
                            }
                            if (!empty($time)) {
                                echo '<p class="time">Total Time: '.$time.'</p>';
                            }
                            echo '</div>'; // meta
                        echo '</a>'; // recipe
                    }
                    $next_page_url = get_next_posts_page_link($wp_query->max_num_pages);
                    
                    if (!empty($next_page_url)) {
                        echo '<a class="next-page" href="'.$next_page_url.'">Next Page</a>';
                    }
                } else {
                    echo <<<EOS
                    <div class="error-wrap">
                        <h2 class="error">Sorry, there aren't any recipes. Try using a different filter</h2>
                        <a class="reset-btn" href="/recipes">Reset Filters</a>
                    </div>
                    EOS;
                }
                echo '</div><div class="recipes-end"></div>';
            ?>
        </div>
    </div>
</div>
<?php
	get_footer();