<?php
    global $post;
	get_header();
?>
<div class="recipe">
    <?php
        $title = get_the_title();
        $servings = get_field('servings');
        $time = get_field('time');
        $page = get_field('page');
        $source = Recipes::build_source_string(get_the_terms($post, 'source'), true);
        $img_url = wp_get_attachment_image_src(get_field('recipe_image'), 'full')[0];
        $ingredients = get_field('ingredients');
        $instructions = get_field('cooking_instructions');
    ?>
    <section class="main-content">
        <div class="wrap">
            <a class="back" href="/recipes">< view all recipes</a>
            <a class="recipe-img" href="<?php echo $img_url; ?>">See a picture of the recipe</a>
            <h1><?php echo $title; ?></h1>
            <div class="meta">
                <p class="source"><?php echo $source; ?>, Page <?php echo $page; ?></p>
                <p class="time">Time: <?php echo $time; ?></p>
                <p class="servings">Serves <?php echo $servings; ?></p>
            </div>
            <div class="ingredients">
                <h2>Ingredients</h2>
                <?php echo $ingredients; ?>
            </div>
            <div class="directions">
                <h2>Cooking Instructions</h2>
                <?php echo $instructions; ?>
            </div>
        </div>
    </section>
</div>
<?php
	get_footer();