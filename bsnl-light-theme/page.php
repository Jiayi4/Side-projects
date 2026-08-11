<?php
/**
 * Generic editable page template.
 */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>
  <?php
  $has_local_nav = false !== strpos((string) get_post_field('post_content', get_the_ID()), 'bsnl-page-with-nav');
  $content_class = $has_local_nav ? 'bsnl-page-content has-local-nav' : 'bsnl-page-content';
  $main_class = $has_local_nav ? 'bsnl-page has-local-nav' : 'bsnl-page';
  $breadcrumb_items = bsnl_light_breadcrumb_items_for_page(get_post());
  ?>
  <main class="<?php echo esc_attr($main_class); ?>">
    <header class="bsnl-page-hero">
      <div class="bsnl-page-shell">
        <div class="bsnl-page-heading">
          <?php bsnl_light_page_kicker(); ?>
          <?php bsnl_light_breadcrumb_nav($breadcrumb_items); ?>
          <h1><?php the_title(); ?></h1>
          <div class="bsnl-page-title-rule" aria-hidden="true"></div>
        </div>
      </div>
    </header>

    <article class="<?php echo esc_attr($content_class); ?>">
      <?php the_content(); ?>
    </article>
  </main>
<?php endwhile; ?>

<?php
get_footer();
