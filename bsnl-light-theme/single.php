<?php
/**
 * Single post template.
 */

get_header();
?>

<main class="bsnl-page">
  <?php while (have_posts()) : the_post(); ?>
    <?php $breadcrumb_items = bsnl_light_breadcrumb_items_for_post(get_post()); ?>
    <header class="bsnl-page-hero bsnl-post-hero">
      <div class="bsnl-page-shell">
        <div class="bsnl-page-heading">
          <div class="bsnl-page-path">
            <span class="bsnl-page-dots" aria-hidden="true"><span></span><span></span><span></span></span>
            <?php bsnl_light_breadcrumb_nav($breadcrumb_items); ?>
          </div>
          <h1><?php the_title(); ?></h1>
          <div class="bsnl-eyebrow"><?php echo esc_html(get_the_date()); ?> &middot; <?php echo esc_html__('By', 'bsnl-light'); ?> <?php echo esc_html(bsnl_light_display_author(get_the_ID())); ?></div>
          <div class="bsnl-page-title-rule" aria-hidden="true"></div>
        </div>
      </div>
    </header>
    <article class="bsnl-page-content">
      <?php the_content(); ?>
    </article>
  <?php endwhile; ?>
</main>

<?php
get_footer();
