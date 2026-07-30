<?php
/**
 * Header template.
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<nav class="bsnl-topbar">
  <a class="bsnl-brand" href="<?php echo esc_url(home_url('/')); ?>">
    <img class="bsnl-logo" src="<?php echo esc_url(bsnl_light_asset('images/bsnl-logo-main-web-trimmed.png')); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
  </a>
  <button class="bsnl-menu-toggle" type="button" aria-expanded="false" aria-controls="bsnl-primary-menu" aria-label="<?php esc_attr_e('Open menu', 'bsnl-light'); ?>">
    <span></span>
    <span></span>
    <span></span>
  </button>
  <div class="bsnl-menu">
    <?php
    wp_nav_menu([
        'theme_location' => 'primary',
        'container' => false,
        'fallback_cb' => 'bsnl_light_fallback_menu',
        'items_wrap' => '<ul id="bsnl-primary-menu" class="bsnl-menu-list">%3$s</ul>',
        'depth' => 2,
    ]);
    ?>
  </div>
  <div class="bsnl-search-wrap">
    <button class="bsnl-search-button" type="button" aria-expanded="false" aria-controls="bsnl-site-search" aria-label="<?php esc_attr_e('Search', 'bsnl-light'); ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m16.5 16.5 4 4"></path></svg>
    </button>
    <form id="bsnl-site-search" class="bsnl-site-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <label class="screen-reader-text" for="bsnl-search-field"><?php esc_html_e('Search BSNL', 'bsnl-light'); ?></label>
      <input id="bsnl-search-field" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php esc_attr_e('Search', 'bsnl-light'); ?>">
    </form>
  </div>
</nav>

<?php if ((is_page() || is_singular('post')) && !is_front_page()) : ?>
  <div class="bsnl-global-back">
    <div class="bsnl-global-back-inner">
      <a class="bsnl-page-back-link" href="<?php echo esc_url(bsnl_light_back_fallback_url()); ?>" data-bsnl-back><span class="bsnl-back-arrow" aria-hidden="true">&larr;</span><?php esc_html_e('Go back', 'bsnl-light'); ?></a>
    </div>
  </div>
<?php endif; ?>
