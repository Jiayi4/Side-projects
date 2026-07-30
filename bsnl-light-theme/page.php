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
  $breadcrumb_pages = [];
  $parent_id = wp_get_post_parent_id(get_the_ID());
  while ($parent_id) {
      $parent = get_post($parent_id);
      if (!$parent instanceof WP_Post) {
          break;
      }
      $breadcrumb_pages[] = $parent;
      $parent_id = wp_get_post_parent_id($parent_id);
  }
  $breadcrumb_pages = array_reverse($breadcrumb_pages);
  $events_parent = null;
  foreach ($breadcrumb_pages as $breadcrumb_page) {
      if ('events' === $breadcrumb_page->post_name || 'Events' === get_the_title($breadcrumb_page)) {
          $events_parent = $breadcrumb_page;
          break;
      }
  }
  $page_slug = (string) get_post_field('post_name', get_the_ID());
  $page_title = strtolower(trim((string) get_the_title()));
  $page_source = (string) get_post_field('post_content', get_the_ID());
  $is_alumni_directory = in_array($page_slug, ['alumni', 'alumni-directory'], true)
      || in_array($page_title, ['alumni', 'alumni directory'], true)
      || false !== strpos($page_source, 'bsnl_alumni_directory');
  ?>
  <main class="<?php echo esc_attr($main_class); ?>">
    <header class="bsnl-page-hero">
      <div class="bsnl-page-shell">
        <div class="bsnl-page-heading">
          <?php if (!($events_parent instanceof WP_Post) && !$is_alumni_directory) : ?>
            <a class="bsnl-page-kicker" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Go to homepage', 'bsnl-light'); ?>"><span class="bsnl-page-dots" aria-hidden="true"><span></span><span></span><span></span></span>BioScience Network Lausanne</a>
          <?php endif; ?>
          <?php if ($breadcrumb_pages && !$events_parent && !$is_alumni_directory) : ?>
            <nav class="bsnl-page-breadcrumb" aria-label="<?php esc_attr_e('Page path', 'bsnl-light'); ?>">
              <?php foreach ($breadcrumb_pages as $page_index => $parent_page) : ?>
                <?php if ($page_index > 0) : ?><span aria-hidden="true">/</span><?php endif; ?>
                <a href="<?php echo esc_url(get_permalink($parent_page)); ?>"><?php echo esc_html(get_the_title($parent_page)); ?></a>
              <?php endforeach; ?>
              <span aria-hidden="true">/</span>
              <span><?php the_title(); ?></span>
            </nav>
          <?php endif; ?>
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
