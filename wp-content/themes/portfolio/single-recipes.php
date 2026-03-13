<?php
    global $post;
	get_header();
?>
<div class="recipe">
    <?php
        $title        = get_the_title();
        $servings     = get_field('servings');
        $time         = get_field('time');
        $page         = get_field('page');
        $source       = Recipes::build_source_string(get_the_terms($post, 'source'), true);
        $img_id       = get_field('recipe_image');
        $food_img_id  = get_field('food_image');
        $ingredients  = get_field('ingredients');
        $instructions = get_field('cooking_instructions');
    ?>

    <section class="recipe-header">
        <div class="recipe-header__inner">
            <a class="recipe-header__back" href="/recipes">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> All Recipes
            </a>
            <?php if ($food_img_id) : ?>
            <div class="recipe-header__image-wrap">
                <?php echo wp_get_attachment_image($food_img_id, 'largest_image', false, [
                    'class' => 'recipe-header__image',
                    'alt'   => $title,
                ]); ?>
            </div>
            <?php endif; ?>
            <h1 class="recipe-header__title"><?php echo $title; ?></h1>
        </div>
    </section>

    <section class="recipe-meta">
        <div class="recipe-meta__inner">
            <?php if ($source) : ?>
            <div class="recipe-meta__item">
                <i class="fa fa-book" aria-hidden="true"></i>
                <span><?php echo $source; ?>, p.<?php echo $page; ?></span>
            </div>
            <?php endif; ?>
            <?php if ($time) : ?>
            <div class="recipe-meta__item">
                <i class="fa fa-clock-o" aria-hidden="true"></i>
                <span><?php echo $time; ?></span>
            </div>
            <?php endif; ?>
            <?php if ($servings) : ?>
            <div class="recipe-meta__item">
                <i class="fa fa-users" aria-hidden="true"></i>
                <span>Serves <?php echo $servings; ?></span>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="recipe-body">
        <div class="recipe-body__inner">
            <div class="recipe-body__ingredients">
                <h2 class="recipe-section-heading">Ingredients</h2>
                <?php
                    $recipe_img_url = $img_id
                        ? wp_get_attachment_image_src($img_id, 'full')[0]
                        : null;
                ?>
                <?php if ($recipe_img_url) : ?>
                <a class="recipe-img-link" href="<?php echo esc_url($recipe_img_url); ?>" target="_blank">
                    <i class="fa fa-file-image-o" aria-hidden="true"></i> View recipe page
                </a>
                <?php endif; ?>
                <div class="ingredients-list">
                    <?php echo $ingredients; ?>
                </div>
            </div>
            <div class="recipe-body__directions">
                <h2 class="recipe-section-heading">Instructions</h2>
                <?php echo $instructions; ?>
            </div>
        </div>
    </section>

</div>
<?php
	get_footer();
