<?php
/**
 * BSNL Light Theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

function bsnl_light_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 96,
        'width'       => 260,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor.css');

    register_nav_menus([
        'primary' => __('Primary menu', 'bsnl-light'),
        'footer_organization' => __('Footer organization menu', 'bsnl-light'),
        'footer_resources' => __('Footer resources menu', 'bsnl-light'),
    ]);
}
add_action('after_setup_theme', 'bsnl_light_setup');

function bsnl_light_assets(): void
{
    wp_enqueue_style('bsnl-light-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap', [], null);
    wp_enqueue_style('bsnl-light-style', get_template_directory_uri() . '/assets/css/theme.css', [], '0.4.6');
    wp_enqueue_script('bsnl-light-script', get_template_directory_uri() . '/assets/js/theme.js', [], '0.4.6', true);
    wp_localize_script('bsnl-light-script', 'bsnlLight', [
        'eventsUrl' => bsnl_light_page_url('Events'),
    ]);
}
add_action('wp_enqueue_scripts', 'bsnl_light_assets');

function bsnl_light_page_slug_body_class(array $classes): array
{
    if (is_page()) {
        $post = get_post();
        if ($post instanceof WP_Post && $post->post_name) {
            $classes[] = 'bsnl-page-slug-' . sanitize_html_class((string) $post->post_name);
        }
        if ($post instanceof WP_Post && false !== strpos((string) $post->post_content, 'bsnl-page-with-nav')) {
            $classes[] = 'bsnl-page-has-local-nav';
        }
    }

    return $classes;
}
add_filter('body_class', 'bsnl_light_page_slug_body_class');

function bsnl_light_home_text_defaults(): array
{
    return [
        'hero_kicker' => 'BioScience Network Lausanne',
        'primary_button_text' => 'Register for an event',
        'secondary_button_text' => 'Subscribe our newsletter',
        'discover_eyebrow' => 'Discover',
        'discover_title' => 'Explore with BSNL',
        'pillar_1_number' => '01',
        'pillar_1_title' => 'Career opportunities',
        'pillar_1_text' => 'Explore career paths across industry, academia, communication, and consulting.',
        'pillar_2_number' => '02',
        'pillar_2_title' => 'New skills',
        'pillar_2_text' => 'Build practical skills through workshops and guided sessions.',
        'pillar_3_number' => '03',
        'pillar_3_title' => 'Professional network',
        'pillar_3_text' => 'Meet peers, alumni, speakers, and life science companies.',
        'pillar_4_number' => '04',
        'pillar_4_title' => 'Industry insight',
        'pillar_4_text' => 'Get a closer look at pharma, biotech, and research careers.',
        'calendar_eyebrow' => 'Calendar',
        'calendar_title' => 'Upcoming events',
        'next_event_label' => 'Next BSNL session',
        'view_event_text' => 'View event',
        'more_events_title' => 'More upcoming events',
        'full_calendar_text' => 'Full calendar',
        'news_eyebrow' => 'Recent highlights',
        'news_title' => 'News',
        'news_view_all_text' => 'View all',
    ];
}

function bsnl_light_home_text(string $key): string
{
    $defaults = bsnl_light_home_text_defaults();
    $value = (string) get_theme_mod('bsnl_home_' . $key, $defaults[$key] ?? '');

    if ('news_eyebrow' === $key && in_array(strtolower(trim($value)), ['', 'news', 'latest updates'], true)) {
        return $defaults[$key] ?? '';
    }

    return $value;
}

function bsnl_light_customize_register(WP_Customize_Manager $wp_customize): void
{
    $wp_customize->add_section('bsnl_settings', [
        'title' => __('BSNL Settings', 'bsnl-light'),
        'description' => __('Edit global BSNL links and contact settings used across the theme.', 'bsnl-light'),
        'priority' => 34,
    ]);

    $wp_customize->add_setting('bsnl_contact_email', [
        'default' => 'info@bsnl.ch',
        'sanitize_callback' => 'sanitize_email',
    ]);
    $wp_customize->add_control('bsnl_contact_email', [
        'label' => __('Contact form recipient email', 'bsnl-light'),
        'description' => __('Contact and alumni update form submissions are sent to this address.', 'bsnl-light'),
        'section' => 'bsnl_settings',
        'type' => 'email',
    ]);

}
add_action('customize_register', 'bsnl_light_customize_register');

function bsnl_light_register_block_patterns(): void
{
    if (!function_exists('register_block_pattern')) {
        return;
    }

    if (function_exists('register_block_pattern_category')) {
        register_block_pattern_category('bsnl', ['label' => __('BSNL Layouts', 'bsnl-light')]);
    }

    register_block_pattern('bsnl/section-navigation-page', [
        'title' => __('BSNL page with section navigation', 'bsnl-light'),
        'description' => __('Editable BSNL page structure with navigation and two styled sections.', 'bsnl-light'),
        'categories' => ['bsnl'],
        'content' => '<!-- wp:group {"className":"bsnl-page-with-nav","layout":{"type":"default"}} --><div class="wp-block-group bsnl-page-with-nav"><!-- wp:html --><nav class="bsnl-page-nav" aria-label="Page sections"><a href="#overview">Overview</a><a href="#details">Details</a></nav><!-- /wp:html --><!-- wp:group {"className":"bsnl-page-sections","layout":{"type":"default"}} --><div class="wp-block-group bsnl-page-sections"><!-- wp:group {"anchor":"overview","className":"bsnl-page-section","layout":{"type":"default"}} --><div id="overview" class="wp-block-group bsnl-page-section"><!-- wp:heading --><h2 class="wp-block-heading">Overview</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Add the overview text here.</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:group {"anchor":"details","className":"bsnl-page-section","layout":{"type":"default"}} --><div id="details" class="wp-block-group bsnl-page-section"><!-- wp:heading --><h2 class="wp-block-heading">Details</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Add the section text here.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group --></div><!-- /wp:group -->',
    ]);

    register_block_pattern('bsnl/standard-section', [
        'title' => __('BSNL standard section', 'bsnl-light'),
        'description' => __('A styled editable section with an anchor, heading, and paragraph.', 'bsnl-light'),
        'categories' => ['bsnl'],
        'content' => '<!-- wp:group {"anchor":"section-name","className":"bsnl-page-section","layout":{"type":"default"}} --><div id="section-name" class="wp-block-group bsnl-page-section"><!-- wp:heading --><h2 class="wp-block-heading">Section title</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Add the section text here.</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
    ]);

    register_block_pattern('bsnl/recruitment-section', [
        'title' => __('BSNL recruitment section', 'bsnl-light'),
        'categories' => ['bsnl'],
        'content' => '<!-- wp:group {"anchor":"recruitment","className":"bsnl-page-section","layout":{"type":"default"}} --><div id="recruitment" class="wp-block-group bsnl-page-section"><!-- wp:heading {"level":3,"className":"bsnl-team-subheading"} --><h3 class="wp-block-heading bsnl-team-subheading">Recruitment</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Recruitment is currently closed. When BSNL opens recruitment, the call will be published through the <a href="#">News page</a>, LinkedIn, Instagram, and the newsletter.</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
    ]);

    register_block_pattern('bsnl/alumni-section', [
        'title' => __('BSNL alumni section', 'bsnl-light'),
        'categories' => ['bsnl'],
        'content' => '<!-- wp:group {"anchor":"alumni","className":"bsnl-page-section bsnl-alumni-overview","layout":{"type":"default"}} --><div id="alumni" class="wp-block-group bsnl-page-section bsnl-alumni-overview"><!-- wp:heading {"level":3,"className":"bsnl-team-subheading"} --><h3 class="wp-block-heading bsnl-team-subheading">Alumni</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Add the alumni introduction here.</p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"bsnl-alumni-links"} --><p class="bsnl-alumni-links"><a class="bsnl-section-link" href="#">Open full alumni directory -&gt;</a></p><!-- /wp:paragraph --><!-- wp:paragraph {"className":"bsnl-alumni-update"} --><p class="bsnl-alumni-update"><span class="bsnl-alumni-update-label">Need an alumni update?</span> Add the update instructions here and <a href="#">contact BSNL</a>.</p><!-- /wp:paragraph --></div><!-- /wp:group -->',
    ]);
}
add_action('init', 'bsnl_light_register_block_patterns', 30);

function bsnl_light_asset(string $path): string
{
    return esc_url(get_template_directory_uri() . '/assets/' . ltrim($path, '/'));
}

function bsnl_light_contact_page_content(): string
{
    return <<<'HTML'
<!-- bsnl-contact-static -->
<div class="bsnl-contact-stack bsnl-contact-static">
  <section class="bsnl-contact-info-block">
    <h2>Stay connected</h2>
    <p class="bsnl-contact-copy">Follow BSNL on <a href="https://www.linkedin.com/company/biosciencenetworklausanne">LinkedIn</a> and <a href="https://www.instagram.com/bsnllausanne/">Instagram</a>, or <a href="https://docs.google.com/forms/d/e/1FAIpQLSdMHpfzkqa98xUBsCOebCsQZl1vL1S8fKCZnWZPm8cM2PgSmQ/viewform">subscribe to our newsletter</a> for the latest events, opportunities, and updates from Lausanne's life science community and beyond.</p>
    <p class="bsnl-contact-copy">For questions about our events, partnership opportunities, speaker proposals, alumni updates, or the BSNL community, contact us at <a class="bsnl-contact-email-link" href="mailto:info@bsnl.ch">info@bsnl.ch</a>.</p>
  </section>
  <section class="bsnl-contact-info-block">
    <h2>Location</h2>
    <p class="bsnl-contact-location-name">Lausanne, Switzerland</p>
    <p class="bsnl-contact-copy">BSNL operates across Lausanne's life science ecosystem. We do not maintain a public office, and event venues are listed on the relevant event page.</p>
  </section>
</div>
HTML;
}

function bsnl_light_home_page_content(): string
{
    return 'BSNL connects researchers across Lausanne\'s life science ecosystem with career opportunities, practical skills, and a community of alumni and professionals.';
}

function bsnl_light_is_legacy_home_copy(string $text): bool
{
    $normalized = strtolower(trim(wp_strip_all_tags($text)));

    return false !== strpos($normalized, 'epfl and unil')
        || false !== strpos($normalized, 'phd students')
        || false !== strpos($normalized, 'postdocs working in life sciences')
        || false !== strpos($normalized, 'ideal career path');
}

function bsnl_light_default_pages(): array
{
    return [
        'Home' => bsnl_light_home_page_content(),
        'About Us' => 'BSNL mission, team, alumni, gallery, and governance.',
        'Events' => 'Upcoming events and BSNL event formats.',
        'News' => 'BSNL news, highlights, and announcements.',
        'Partnership' => 'Collaboration and partnership information.',
        'Contact' => bsnl_light_contact_page_content(),
    ];
}

function bsnl_light_primary_menu_pages(): array
{
    return ['Home', 'About Us', 'Events', 'News', 'Partnership', 'Contact'];
}

function bsnl_light_create_page_if_missing(string $title, string $content): int
{
    $existing = get_page_by_title($title);
    if ($existing instanceof WP_Post) {
        return (int) $existing->ID;
    }

    return (int) wp_insert_post([
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ]);
}

function bsnl_light_activate(): void
{
    $page_ids = [];
    foreach (bsnl_light_default_pages() as $title => $content) {
        $page_ids[$title] = bsnl_light_create_page_if_missing($title, $content);
    }

    if (!empty($page_ids['Home'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_ids['Home']);
    }

    $menu_name = 'BSNL Primary Menu';
    $menu = wp_get_nav_menu_object($menu_name);
    $menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu($menu_name);

    if ($menu_id && !wp_get_nav_menu_items($menu_id)) {
        foreach (bsnl_light_primary_menu_pages() as $title) {
            if (!empty($page_ids[$title])) {
                wp_update_nav_menu_item($menu_id, 0, [
                    'menu-item-title' => $title,
                    'menu-item-object-id' => $page_ids[$title],
                    'menu-item-object' => 'page',
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish',
                ]);
            }
        }
    }

    $locations = get_theme_mod('nav_menu_locations', []);
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);

    bsnl_light_ensure_upcoming_events_category();
}
add_action('after_switch_theme', 'bsnl_light_activate');

function bsnl_light_fallback_menu(): void
{
    $items = bsnl_light_primary_menu_pages();
    echo '<ul class="bsnl-menu-list">';
    foreach ($items as $item) {
        echo '<li><a href="' . esc_url(home_url('/' . sanitize_title($item) . '/')) . '">' . esc_html($item) . '</a></li>';
    }
    echo '</ul>';
}

function bsnl_light_contact_email(): string
{
    $email = sanitize_email((string) get_theme_mod('bsnl_contact_email', 'info@bsnl.ch'));

    return (string) apply_filters('bsnl_light_contact_email', $email ?: 'info@bsnl.ch');
}

function bsnl_light_pmi_logo_image_markup(): string
{
    $src = esc_url(get_template_directory_uri() . '/assets/images/project-management-institute-logo.jpg');

    return '<img src="' . $src . '" alt="Project Management Institute logo" loading="lazy" decoding="async">';
}

function bsnl_light_pmi_logo_markup(): string
{
    return '<span class="bsnl-partner-logo bsnl-partner-logo-pmi">' . bsnl_light_pmi_logo_image_markup() . '</span>';
}

function bsnl_light_markup_mentions_pmi(string $markup): bool
{
    return false !== stripos($markup, 'pmi')
        || false !== stripos($markup, 'project management institute');
}

function bsnl_light_normalize_pmi_text(string $text): string
{
    $patterns = [
        '/\bProject\s+Management\s+Institute\s+(?:Switzerland|Swiss\s+Chapter)\b/i',
        '/\bPMI\s+(?:Switzerland|Swiss\s+Chapter)\b/i',
        '/\bPMI\b/i',
    ];

    foreach ($patterns as $pattern) {
        $text = (string) preg_replace($pattern, 'Project Management Institute', $text);
    }

    return $text;
}

function bsnl_light_normalize_pmi_text_nodes(string $markup): string
{
    $parts = preg_split('/(<[^>]+>)/', $markup, -1, PREG_SPLIT_DELIM_CAPTURE);
    if (!is_array($parts)) {
        return $markup;
    }

    foreach ($parts as $index => $part) {
        if ('' === $part || '<' === $part[0]) {
            continue;
        }
        if (preg_match('/(?:https?:\/\/|www\.|pmi\.[a-z]{2,})/i', $part)) {
            continue;
        }
        $parts[$index] = bsnl_light_normalize_pmi_text($part);
    }

    return implode('', $parts);
}

function bsnl_light_normalize_pmi_href(string $markup): string
{
    if (preg_match('/\bhref\s*=\s*(["\']).*?\1/i', $markup)) {
        return (string) preg_replace('/\bhref\s*=\s*(["\']).*?\1/i', 'href="https://www.pmi.org/"', $markup, 1);
    }

    return (string) preg_replace('/^<a\b/i', '<a href="https://www.pmi.org/"', $markup, 1);
}

function bsnl_light_normalize_pmi_partner_card(string $card): string
{
    if (!bsnl_light_markup_mentions_pmi($card)) {
        return $card;
    }

    if (preg_match('/^<a\b/i', $card)) {
        $card = bsnl_light_normalize_pmi_href($card);
    }

    $card = preg_replace('/<strong\b[^>]*>.*?<\/strong>/is', '<strong>Project Management Institute</strong>', $card, 1) ?: $card;
    $card = preg_replace_callback('/(<[^>]+\bclass=(["\'])[^"\']*bsnl-partner-url[^"\']*\2[^>]*>).*?(<\/[^>]+>)/is', static function (array $match): string {
        return $match[1] . 'pmi.org' . $match[3];
    }, $card, 1) ?: $card;

    if (preg_match('/<span\b[^>]*class=(["\'])[^"\']*bsnl-partner-logo[^"\']*\1[^>]*>.*?<\/span>/is', $card)) {
        $card = preg_replace('/<span\b[^>]*class=(["\'])[^"\']*bsnl-partner-logo[^"\']*\1[^>]*>.*?<\/span>/is', bsnl_light_pmi_logo_markup(), $card, 1) ?: $card;
    } elseif (preg_match('/<img\b[^>]*>/i', $card)) {
        $card = preg_replace('/<img\b[^>]*>/i', bsnl_light_pmi_logo_image_markup(), $card, 1) ?: $card;
    }

    return bsnl_light_normalize_pmi_text_nodes($card);
}

function bsnl_light_normalize_partnership_pmi_content(string $content): string
{
    $content = preg_replace_callback('/<a\b(?=[^>]*class=(["\'])[^"\']*bsnl-partner-card[^"\']*\1)[\s\S]*?<\/a>/i', static function (array $match): string {
        return bsnl_light_normalize_pmi_partner_card($match[0]);
    }, $content) ?: $content;

    $content = preg_replace_callback('/<div\b(?=[^>]*class=(["\'])[^"\']*bsnl-partner-card[^"\']*\1)[\s\S]*?<\/div>/i', static function (array $match): string {
        return bsnl_light_normalize_pmi_partner_card($match[0]);
    }, $content) ?: $content;

    $content = preg_replace_callback('/<a\b[^>]*>.*?<\/a>/is', static function (array $match): string {
        $link = $match[0];
        if (!bsnl_light_markup_mentions_pmi($link)) {
            return $link;
        }

        return bsnl_light_normalize_pmi_text_nodes(bsnl_light_normalize_pmi_href($link));
    }, $content) ?: $content;

    return bsnl_light_normalize_pmi_text_nodes($content);
}

function bsnl_light_newsletter_url(): string
{
    return (string) apply_filters('bsnl_light_newsletter_url', 'https://docs.google.com/forms/d/e/1FAIpQLSdMHpfzkqa98xUBsCOebCsQZl1vL1S8fKCZnWZPm8cM2PgSmQ/viewform');
}

function bsnl_light_page_url(string $title, string $anchor = ''): string
{
    $page = get_page_by_title($title);
    $url = $page instanceof WP_Post ? get_permalink($page) : home_url('/' . sanitize_title($title) . '/');

    if ('' !== $anchor) {
        $url .= '#' . sanitize_title($anchor);
    }

    return $url;
}

function bsnl_light_back_fallback_url(): string
{
    $post = get_post();

    if (is_page() && $post instanceof WP_Post && $post->post_parent) {
        $parent_url = get_permalink($post->post_parent);
        if ($parent_url) {
            return $parent_url;
        }
    }

    if (is_singular('post')) {
        return bsnl_light_page_url('News');
    }

    return home_url('/');
}

function bsnl_light_home_editor_content(WP_Post $page): string
{
    $hero_title = get_the_title($page) ?: 'Network, get inspired and find your ideal career path.';
    $hero_title = 'HOME' === strtoupper($hero_title) ? 'Network, get inspired and find your ideal career path.' : $hero_title;
    $hero_text = trim(wp_strip_all_tags((string) $page->post_excerpt));
    if ('' === $hero_text) {
        $hero_text = trim(wp_strip_all_tags((string) $page->post_content));
    }
    if ('' === $hero_text || bsnl_light_is_legacy_home_copy($hero_text)) {
        $hero_text = bsnl_light_home_page_content();
    }

    $about_url = bsnl_light_page_url('About Us');
    $events_url = bsnl_light_page_url('Events', 'upcoming');
    $news_url = bsnl_light_page_url('News');
    $newsletter_url = bsnl_light_newsletter_url();

    ob_start();
    ?>
<!-- bsnl-home-editor-layout -->
<section class="bsnl-hero">
  <div class="bsnl-hero-copy">
    <div class="bsnl-kicker"><?php echo esc_html(bsnl_light_home_text('hero_kicker')); ?></div>
    <h1><?php if ('Network, get inspired and find your ideal career path.' === trim($hero_title)) : ?>Network, get inspired<br><span class="bsnl-hero-title-line">and find your ideal career path.</span><?php else : ?><?php echo esc_html($hero_title); ?><?php endif; ?></h1>
    <p><?php echo esc_html($hero_text); ?></p>
    <nav class="bsnl-actions" aria-label="Homepage actions">
      <a class="bsnl-action-link" href="<?php echo esc_url($about_url); ?>">About Us <span>-&gt;</span></a>
      <a class="bsnl-action-link" href="<?php echo esc_url($events_url); ?>">Check out recent events <span>-&gt;</span></a>
      <a class="bsnl-action-link" href="<?php echo esc_url($newsletter_url); ?>"><?php echo esc_html(bsnl_light_home_text('secondary_button_text')); ?> <span>-&gt;</span></a>
    </nav>
  </div>
</section>

<section class="bsnl-section-intro">
  <div class="bsnl-eyebrow"><?php echo esc_html(bsnl_light_home_text('discover_eyebrow')); ?></div>
  <h2><?php echo esc_html(bsnl_light_home_text('discover_title')); ?></h2>
</section>

<section class="bsnl-value-grid">
  <article class="bsnl-value-card"><span><?php echo esc_html(bsnl_light_home_text('pillar_1_number')); ?></span><h3><?php echo esc_html(bsnl_light_home_text('pillar_1_title')); ?></h3><p><?php echo esc_html(bsnl_light_home_text('pillar_1_text')); ?></p></article>
  <article class="bsnl-value-card"><span><?php echo esc_html(bsnl_light_home_text('pillar_2_number')); ?></span><h3><?php echo esc_html(bsnl_light_home_text('pillar_2_title')); ?></h3><p><?php echo esc_html(bsnl_light_home_text('pillar_2_text')); ?></p></article>
  <article class="bsnl-value-card"><span><?php echo esc_html(bsnl_light_home_text('pillar_3_number')); ?></span><h3><?php echo esc_html(bsnl_light_home_text('pillar_3_title')); ?></h3><p><?php echo esc_html(bsnl_light_home_text('pillar_3_text')); ?></p></article>
  <article class="bsnl-value-card"><span><?php echo esc_html(bsnl_light_home_text('pillar_4_number')); ?></span><h3><?php echo esc_html(bsnl_light_home_text('pillar_4_title')); ?></h3><p><?php echo esc_html(bsnl_light_home_text('pillar_4_text')); ?></p></article>
</section>

<section class="bsnl-section-intro" id="calendar">
  <div class="bsnl-eyebrow"><?php echo esc_html(bsnl_light_home_text('calendar_eyebrow')); ?></div>
  <h2><?php echo esc_html(bsnl_light_home_text('calendar_title')); ?></h2>
</section>

[bsnl_home_events next_event_label="<?php echo esc_attr(bsnl_light_home_text('next_event_label')); ?>" view_event_text="<?php echo esc_attr(bsnl_light_home_text('view_event_text')); ?>" more_events_title="<?php echo esc_attr(bsnl_light_home_text('more_events_title')); ?>" full_calendar_text="<?php echo esc_attr(bsnl_light_home_text('full_calendar_text')); ?>"]

<section class="bsnl-news-section">
  <div class="bsnl-section-head">
    <div><div class="bsnl-eyebrow"><?php echo esc_html(bsnl_light_home_text('news_eyebrow')); ?></div><h2><?php echo esc_html(bsnl_light_home_text('news_title')); ?></h2></div>
    <a class="bsnl-section-link" href="<?php echo esc_url($news_url); ?>"><?php echo esc_html(bsnl_light_home_text('news_view_all_text')); ?> -&gt;</a>
  </div>
  [bsnl_home_news]
</section>
    <?php
    return trim((string) ob_get_clean());
}

function bsnl_light_home_page_for_editor_migration(): ?WP_Post
{
    $front_page_id = (int) get_option('page_on_front');
    $page = $front_page_id ? get_post($front_page_id) : null;
    if (!$page instanceof WP_Post) {
        $page = get_page_by_path('home', OBJECT, 'page');
    }

    return $page instanceof WP_Post ? $page : null;
}

function bsnl_light_migrate_home_to_editor(array &$conflicts): void
{
    $page = bsnl_light_home_page_for_editor_migration();
    if (!$page instanceof WP_Post) {
        $conflicts[] = 'Home page was not found; the editor-owned homepage layout was not seeded.';
        return;
    }

    $content = (string) $page->post_content;
    if (false !== strpos($content, 'bsnl-home-editor-layout')) {
        return;
    }

    $has_structured_content = false !== stripos($content, '<section')
        || false !== strpos($content, '<!-- wp:')
        || false !== strpos($content, 'bsnl-page-with-nav');
    if ($has_structured_content) {
        $conflicts[] = 'Home already contains a custom structured layout, so it was not replaced automatically.';
        return;
    }

    wp_update_post([
        'ID' => (int) $page->ID,
        'post_content' => bsnl_light_home_editor_content($page),
    ]);
}

function bsnl_light_is_event_page_post(WP_Post $page): bool
{
    $event_slugs = [
        'events',
        'life-science-career-day',
        'life-science-career-day-lscd',
        'faces-of-industrial-research',
        'faces-of-industrial-research-fir',
        'famelab',
        'famelab-switzerland',
        'biotech-chats',
        'workshops',
        'company-visits',
    ];
    if (in_array((string) $page->post_name, $event_slugs, true)) {
        return true;
    }

    $parent_id = (int) $page->post_parent;
    while ($parent_id) {
        $parent = get_post($parent_id);
        if (!$parent instanceof WP_Post) {
            break;
        }
        if ('events' === (string) $parent->post_name || 'events' === strtolower(get_the_title($parent))) {
            return true;
        }
        $parent_id = (int) $parent->post_parent;
    }

    return false !== stripos((string) $page->post_content, 'bsnl-event-calendar')
        || false !== stripos((string) $page->post_content, 'bsnl-event-format-card');
}

function bsnl_light_persist_current_editor_content(): array
{
    $conflicts = [];
    $partnership = get_page_by_title('Partnership');
    if ($partnership instanceof WP_Post) {
        $updated = bsnl_light_normalize_partnership_pmi_content((string) $partnership->post_content);
        if ($updated !== (string) $partnership->post_content) {
            wp_update_post(['ID' => (int) $partnership->ID, 'post_content' => $updated]);
        }
    }

    $pages = get_pages(['post_status' => ['publish', 'draft', 'private'], 'number' => 0]);
    foreach ($pages as $page) {
        if (!$page instanceof WP_Post) {
            continue;
        }

        $updated = (string) $page->post_content;
        if (bsnl_light_is_event_page_post($page)) {
            $updated = bsnl_light_normalize_event_display_text($updated);
        }
        if (false !== stripos($updated, 'gallery')) {
            $updated = bsnl_light_normalize_gallery_display_text($updated);
        }
        if ($updated !== (string) $page->post_content) {
            wp_update_post(['ID' => (int) $page->ID, 'post_content' => $updated]);
        }
    }

    bsnl_light_migrate_home_to_editor($conflicts);

    return $conflicts;
}

function bsnl_light_enable_editor_owned_content(): void
{
    $target_version = '0.4.0';
    if ($target_version === (string) get_option('bsnl_light_editor_ownership_version', '')) {
        return;
    }

    $conflicts = bsnl_light_persist_current_editor_content();
    update_option('bsnl_light_editor_ownership_conflicts', $conflicts);
    update_option('bsnl_light_editor_ownership_version', $target_version);
}
add_action('init', 'bsnl_light_enable_editor_owned_content', 20);

function bsnl_light_editor_ownership_admin_notice(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $conflicts = get_option('bsnl_light_editor_ownership_conflicts', []);
    if (!is_array($conflicts) || [] === $conflicts) {
        return;
    }

    echo '<div class="notice notice-warning"><p><strong>' . esc_html__('BSNL editor migration needs review', 'bsnl-light') . '</strong></p><ul style="list-style:disc;padding-left:20px;">';
    foreach ($conflicts as $conflict) {
        echo '<li>' . esc_html((string) $conflict) . '</li>';
    }
    echo '</ul></div>';
}
add_action('admin_notices', 'bsnl_light_editor_ownership_admin_notice');

function bsnl_light_migrate_contact_page_041(): void
{
    $target_version = '0.4.1';
    if ($target_version === (string) get_option('bsnl_light_contact_content_version', '')) {
        return;
    }

    set_theme_mod('bsnl_contact_email', 'info@bsnl.ch');

    $conflicts = get_option('bsnl_light_editor_ownership_conflicts', []);
    $conflicts = is_array($conflicts) ? $conflicts : [];
    $page = get_page_by_path('contact', OBJECT, 'page');
    if (!$page instanceof WP_Post) {
        $page = get_page_by_title('Contact');
    }

    if (!$page instanceof WP_Post) {
        $conflicts[] = 'Contact page was not found; the enquiry-free contact layout was not installed.';
    } else {
        $content = (string) $page->post_content;
        $legacy_markers = [
            'bsnl-contact-enquiry',
            '[bsnl_contact_form',
            '[bsnl_contact_details',
            'Send enquiry',
            'EPFL &amp; UNIL life science community',
        ];
        $is_legacy_contact = '' === trim($content);
        foreach ($legacy_markers as $marker) {
            if (false !== stripos($content, $marker)) {
                $is_legacy_contact = true;
                break;
            }
        }

        if (false !== strpos($content, 'bsnl-contact-static')) {
            $is_legacy_contact = false;
        } elseif ($is_legacy_contact) {
            $result = wp_update_post([
                'ID' => (int) $page->ID,
                'post_content' => bsnl_light_contact_page_content(),
            ], true);
            if (is_wp_error($result)) {
                $conflicts[] = 'Contact page could not be updated: ' . $result->get_error_message();
            }
        } else {
            $conflicts[] = 'Contact contains a custom layout, so the 0.4.1 enquiry-free layout was not applied automatically.';
        }
    }

    update_option('bsnl_light_editor_ownership_conflicts', array_values(array_unique($conflicts)));
    update_option('bsnl_light_contact_content_version', $target_version);
}
add_action('init', 'bsnl_light_migrate_contact_page_041', 21);

function bsnl_light_migrate_contact_heading_042(): void
{
    $target_version = '0.4.2';
    if ($target_version === (string) get_option('bsnl_light_contact_heading_version', '')) {
        return;
    }

    $page = get_page_by_path('contact', OBJECT, 'page');
    if (!$page instanceof WP_Post) {
        $page = get_page_by_title('Contact');
    }

    if ($page instanceof WP_Post) {
        $content = (string) $page->post_content;
        if (false !== strpos($content, 'bsnl-contact-static') && false !== strpos($content, '<h2>Contact BSNL</h2>')) {
            wp_update_post([
                'ID' => (int) $page->ID,
                'post_content' => str_replace('<h2>Contact BSNL</h2>', '<h2>Get in touch</h2>', $content),
            ]);
        }
    }

    update_option('bsnl_light_contact_heading_version', $target_version);
}
add_action('init', 'bsnl_light_migrate_contact_heading_042', 22);

function bsnl_light_migrate_contact_links_043(): void
{
    $target_version = '0.4.3';
    if ($target_version === (string) get_option('bsnl_light_contact_links_version', '')) {
        return;
    }

    $page = get_page_by_path('contact', OBJECT, 'page');
    if (!$page instanceof WP_Post) {
        $page = get_page_by_title('Contact');
    }

    if ($page instanceof WP_Post) {
        $content = (string) $page->post_content;
        if (false !== strpos($content, 'bsnl-contact-static') && false !== strpos($content, '<h2>Get in touch</h2>')) {
            $replacement = <<<'HTML'
<section class="bsnl-contact-info-block">
    <h2>Stay connected</h2>
    <p class="bsnl-contact-copy">Follow BSNL on <a href="https://www.linkedin.com/company/biosciencenetworklausanne">LinkedIn</a> and <a href="https://www.instagram.com/bsnllausanne/">Instagram</a>, or <a href="https://docs.google.com/forms/d/e/1FAIpQLSdMHpfzkqa98xUBsCOebCsQZl1vL1S8fKCZnWZPm8cM2PgSmQ/viewform">subscribe to our newsletter</a> for the latest events, opportunities, and updates from Lausanne's life science community and beyond.</p>
    <p class="bsnl-contact-copy">For questions about our events, partnership opportunities, speaker proposals, alumni updates, or the BSNL community, contact us at <a class="bsnl-contact-email-link" href="mailto:info@bsnl.ch">info@bsnl.ch</a>.</p>
  </section>
HTML;
            $updated = preg_replace('/<section class="bsnl-contact-info-block">\s*<h2>Get in touch<\/h2>[\s\S]*?<\/section>/', $replacement, $content, 1) ?: $content;
            if ($updated !== $content) {
                wp_update_post([
                    'ID' => (int) $page->ID,
                    'post_content' => $updated,
                ]);
            }
        }
    }

    update_option('bsnl_light_contact_links_version', $target_version);
}
add_action('init', 'bsnl_light_migrate_contact_links_043', 23);

function bsnl_light_migrate_contact_sentence_044(): void
{
    $target_version = '0.4.4';
    if ($target_version === (string) get_option('bsnl_light_contact_sentence_version', '')) {
        return;
    }

    $page = get_page_by_path('contact', OBJECT, 'page');
    if (!$page instanceof WP_Post) {
        $page = get_page_by_title('Contact');
    }

    if ($page instanceof WP_Post) {
        $content = (string) $page->post_content;
        if (false !== strpos($content, 'bsnl-contact-static') && false !== strpos($content, 'bsnl-contact-channels')) {
            $replacement = <<<'HTML'
<section class="bsnl-contact-info-block">
    <h2>Stay connected</h2>
    <p class="bsnl-contact-copy">Follow BSNL on <a href="https://www.linkedin.com/company/biosciencenetworklausanne">LinkedIn</a> and <a href="https://www.instagram.com/bsnllausanne/">Instagram</a>, or <a href="https://docs.google.com/forms/d/e/1FAIpQLSdMHpfzkqa98xUBsCOebCsQZl1vL1S8fKCZnWZPm8cM2PgSmQ/viewform">subscribe to our newsletter</a> for the latest events, opportunities, and updates from Lausanne's life science community and beyond.</p>
    <p class="bsnl-contact-copy">For questions about our events, partnership opportunities, speaker proposals, alumni updates, or the BSNL community, contact us at <a class="bsnl-contact-email-link" href="mailto:info@bsnl.ch">info@bsnl.ch</a>.</p>
  </section>
HTML;
            $updated = preg_replace('/<section class="bsnl-contact-info-block">\s*<h2>Stay connected<\/h2>[\s\S]*?<\/section>/', $replacement, $content, 1) ?: $content;
            if ($updated !== $content) {
                wp_update_post([
                    'ID' => (int) $page->ID,
                    'post_content' => $updated,
                ]);
            }
        }
    }

    update_option('bsnl_light_contact_sentence_version', $target_version);
}
add_action('init', 'bsnl_light_migrate_contact_sentence_044', 24);

function bsnl_light_contact_page_candidates_045(): array
{
    $page_ids = [];
    foreach (['contact', 'contact-us'] as $path) {
        $page = get_page_by_path($path, OBJECT, 'page');
        if ($page instanceof WP_Post) {
            $page_ids[(int) $page->ID] = true;
        }
    }

    foreach (['Contact', 'Contact Us'] as $title) {
        $page = get_page_by_title($title);
        if ($page instanceof WP_Post) {
            $page_ids[(int) $page->ID] = true;
        }
    }

    $locations = get_nav_menu_locations();
    $primary_menu_id = (int) ($locations['primary'] ?? 0);
    if ($primary_menu_id) {
        $menu_items = wp_get_nav_menu_items($primary_menu_id) ?: [];
        foreach ($menu_items as $item) {
            if (!($item instanceof WP_Post) || 'page' !== (string) $item->object) {
                continue;
            }
            $menu_title = strtolower(trim(wp_strip_all_tags((string) $item->title)));
            if (in_array($menu_title, ['contact', 'contact us'], true)) {
                $page_ids[(int) $item->object_id] = true;
            }
        }
    }

    $markers = [
        'bsnl-contact-static',
        'bsnl-contact-enquiry',
        'bsnl-contact-info-block',
        '[bsnl_contact_form',
        '[bsnl_contact_details',
        'bsnl.lausanne@gmail.com',
    ];
    $pages = get_pages(['post_status' => ['publish', 'draft', 'private'], 'number' => 0]);
    foreach ($pages as $page) {
        if (!$page instanceof WP_Post) {
            continue;
        }
        foreach ($markers as $marker) {
            if (false !== stripos((string) $page->post_content, $marker)) {
                $page_ids[(int) $page->ID] = true;
                break;
            }
        }
    }

    $candidates = [];
    foreach (array_keys($page_ids) as $page_id) {
        $page = get_post((int) $page_id);
        if ($page instanceof WP_Post && 'page' === $page->post_type) {
            $candidates[] = $page;
        }
    }

    return $candidates;
}

function bsnl_light_migrate_contact_page_045(): void
{
    $target_version = '0.4.5';
    if ($target_version === (string) get_option('bsnl_light_contact_page_version', '')) {
        return;
    }

    set_theme_mod('bsnl_contact_email', 'info@bsnl.ch');
    $conflicts = get_option('bsnl_light_editor_ownership_conflicts', []);
    $conflicts = is_array($conflicts) ? $conflicts : [];
    $pages = bsnl_light_contact_page_candidates_045();

    if ([] === $pages) {
        $conflicts[] = 'No Contact or Contact Us page could be found for the 0.4.5 content update.';
    }

    foreach ($pages as $page) {
        $result = wp_update_post([
            'ID' => (int) $page->ID,
            'post_content' => bsnl_light_contact_page_content(),
        ], true);
        if (is_wp_error($result)) {
            $conflicts[] = sprintf('Contact page "%s" could not be updated: %s', get_the_title($page), $result->get_error_message());
        }
    }

    update_option('bsnl_light_editor_ownership_conflicts', array_values(array_unique($conflicts)));
    update_option('bsnl_light_contact_page_version', $target_version);
}
add_action('init', 'bsnl_light_migrate_contact_page_045', 25);

function bsnl_light_event_anchor_map(): array
{
    return [
        'upcoming' => 'upcoming',
        'upcoming-events' => 'upcoming',
        'calendar' => 'upcoming',
        'how-it-works' => 'events-introduction',
        'events-introduction' => 'events-introduction',
        'event-introduction' => 'events-introduction',
        'main-event-formats' => 'events-introduction',
        'event-formats' => 'events-introduction',
        'life-science-career-day' => 'life-science-career-day-lscd',
        'life-science-career-day-lscd' => 'life-science-career-day-lscd',
        'lscd' => 'life-science-career-day-lscd',
        'faces-of-industrial-research' => 'faces-of-industrial-research-fir',
        'faces-of-industrial-research-fir' => 'faces-of-industrial-research-fir',
        'fir' => 'faces-of-industrial-research-fir',
        'famelab' => 'famelab',
        'famelab-switzerland' => 'famelab',
        'biotech-chat' => 'biotech-chats',
        'biotech-chats' => 'biotech-chats',
        'workshop' => 'workshops',
        'workshops' => 'workshops',
        'company-visit' => 'company-visits',
        'company-visits' => 'company-visits',
    ];
}

function bsnl_light_event_anchor_from_string(string $value): string
{
    $slug = sanitize_title($value);
    $map = bsnl_light_event_anchor_map();

    return $map[$slug] ?? '';
}

function bsnl_light_normalize_event_menu_anchor(array $atts, WP_Post $item, stdClass $args, int $depth): array
{
    if ('primary' !== ($args->theme_location ?? '') || $depth < 1 || empty($atts['href'])) {
        return $atts;
    }

    $href = html_entity_decode((string) $atts['href']);
    $fragment = (string) parse_url($href, PHP_URL_FRAGMENT);
    $anchor = $fragment ? bsnl_light_event_anchor_from_string($fragment) : bsnl_light_event_anchor_from_string((string) $item->title);

    if ('' === $anchor) {
        return $atts;
    }

    $href_host = (string) parse_url($href, PHP_URL_HOST);
    $site_host = (string) parse_url(home_url('/'), PHP_URL_HOST);
    $is_external_link = '' !== $href_host && $site_host !== $href_host;
    $looks_like_section_link = !$is_external_link;

    if ($looks_like_section_link) {
        $atts['href'] = bsnl_light_page_url('Events', $anchor);
    }

    return $atts;
}
add_filter('nav_menu_link_attributes', 'bsnl_light_normalize_event_menu_anchor', 10, 4);

function bsnl_light_normalize_event_menu_label(string $title, WP_Post $item, stdClass $args, int $depth): string
{
    if ('primary' !== ($args->theme_location ?? '') || $depth < 1) {
        return $title;
    }

    $plain_title = trim(wp_strip_all_tags($title));
    if (preg_match('/^(main event formats|events formats|event formats|formats|format)$/i', $plain_title)) {
        return preg_replace('/' . preg_quote($plain_title, '/') . '/i', 'How it works', $title, 1) ?: 'How it works';
    }

    return $title;
}
add_filter('nav_menu_item_title', 'bsnl_light_normalize_event_menu_label', 10, 4);

function bsnl_light_normalize_event_display_text(string $content): string
{
    $replacements = [
        '/Recent examples/i' => 'Recent news',
        '/Recent example/i' => 'Recent news',
        '/Main event formats/i' => 'How it works',
        '/Events formats/i' => 'How it works',
        '/Event formats/i' => 'How it works',
        '/>(\s*)Formats(\s*)</i' => '>$1How it works$2<',
        '/>(\s*)Format(\s*)</i' => '>$1How it works$2<',
    ];

    $content = (string) preg_replace(array_keys($replacements), array_values($replacements), $content);

    return preg_replace_callback('/<article\b(?=[^>]*class=(["\'])[^"\']*bsnl-event-format-card[^"\']*\1)[\s\S]*?<\/article>/i', static function (array $match): string {
        $card = $match[0];
        if (false === stripos($card, 'famelab') || false === stripos($card, 'science communication')) {
            return $card;
        }

        return preg_replace('/(<[^>]+class=(["\'])[^"\']*bsnl-event-format-meta[^"\']*\2[^>]*>).*?(<\/[^>]+>)/is', '$1FameLab$3', $card, 1) ?: $card;
    }, $content) ?: $content;
}

function bsnl_light_normalize_gallery_display_text(string $content): string
{
    $content = preg_replace('/(<section\b(?=[^>]*\bid=(["\'])gallery\2)[^>]*>[\s\S]*?<h[23]\b[^>]*>\s*Gallery\s*<\/h[23]>\s*<p\b[^>]*>).*?(<\/p>)/is', '$1BSNL behind the scenes!$3', $content, 1) ?: $content;

    return preg_replace_callback('/<[^>]+class=(["\'])[^"\']*(?:bsnl-gallery-pair-caption|bsnl-gallery-slide|bsnl-gallery-tile)[^"\']*\1[^>]*>[\s\S]*?<\/[^>]+>/i', static function (array $match): string {
        return preg_replace('/(<span\b[^>]*>).*?(<\/span>)/is', '$1text to be updated$2', $match[0]) ?: $match[0];
    }, $content) ?: $content;
}

function bsnl_light_clean_css_value(string $value, string $fallback = ''): string
{
    $value = trim($value);
    if ('' === $value) {
        return $fallback;
    }

    return preg_match('/^[a-zA-Z0-9\s.,:%#()_+\/-]+$/', $value) ? $value : $fallback;
}

function bsnl_light_ensure_upcoming_events_category(): int
{
    $term = term_exists('Upcoming Events', 'category');
    if (!$term) {
        $term = wp_insert_term('Upcoming Events', 'category', [
            'slug' => 'upcoming-events',
        ]);
    }

    if (is_array($term) && isset($term['term_id'])) {
        return (int) $term['term_id'];
    }

    return is_int($term) ? $term : 0;
}
add_action('init', 'bsnl_light_ensure_upcoming_events_category');

function bsnl_light_upcoming_events_category_exclusions(): array
{
    $term = get_category_by_slug('upcoming-events');

    return $term ? [(int) $term->term_id] : [];
}

function bsnl_light_register_event_meta(): void
{
    $meta_keys = ['bsnl_event_datetime', 'bsnl_event_location', 'bsnl_event_url', 'bsnl_event_format', 'bsnl_event_summary'];
    foreach ($meta_keys as $key) {
        register_post_meta('post', $key, [
            'single' => true,
            'type' => 'string',
            'show_in_rest' => true,
            'auth_callback' => static function (): bool {
                return current_user_can('edit_posts');
            },
        ]);
    }
}
add_action('init', 'bsnl_light_register_event_meta');

function bsnl_light_register_display_author_meta(): void
{
    register_post_meta('post', 'bsnl_display_author', [
        'single' => true,
        'type' => 'string',
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => static function (): bool {
            return current_user_can('edit_posts');
        },
    ]);
}
add_action('init', 'bsnl_light_register_display_author_meta');

function bsnl_light_display_author(int $post_id = 0): string
{
    $post_id = $post_id ?: get_the_ID();
    $display_author = trim((string) get_post_meta($post_id, 'bsnl_display_author', true));
    if ('' !== $display_author) {
        return $display_author;
    }

    $post = get_post($post_id);
    $author = $post instanceof WP_Post ? get_the_author_meta('display_name', (int) $post->post_author) : get_the_author();

    return '' !== trim((string) $author) ? (string) $author : __('BSNL Team', 'bsnl-light');
}

function bsnl_light_display_author_meta_box(WP_Post $post): void
{
    wp_nonce_field('bsnl_display_author', 'bsnl_display_author_nonce');

    $value = (string) get_post_meta($post->ID, 'bsnl_display_author', true);
    echo '<p style="margin-top:0;">Optional public author name shown on News and post pages. Leave empty to use the WordPress account author.</p>';
    echo '<p><label for="bsnl_display_author"><strong>' . esc_html__('Display author', 'bsnl-light') . '</strong></label><br>';
    echo '<input style="width:100%;" id="bsnl_display_author" name="bsnl_display_author" type="text" value="' . esc_attr($value) . '" placeholder="e.g. Jiayi Tan"></p>';
}

function bsnl_light_add_display_author_meta_box(): void
{
    add_meta_box('bsnl-display-author', __('BSNL display author', 'bsnl-light'), 'bsnl_light_display_author_meta_box', 'post', 'side');
}
add_action('add_meta_boxes', 'bsnl_light_add_display_author_meta_box');

function bsnl_light_save_display_author(int $post_id): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!isset($_POST['bsnl_display_author_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bsnl_display_author_nonce'])), 'bsnl_display_author')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $display_author = sanitize_text_field(wp_unslash($_POST['bsnl_display_author'] ?? ''));
    '' === $display_author ? delete_post_meta($post_id, 'bsnl_display_author') : update_post_meta($post_id, 'bsnl_display_author', $display_author);
}
add_action('save_post_post', 'bsnl_light_save_display_author');

function bsnl_light_event_details_meta_box(WP_Post $post): void
{
    wp_nonce_field('bsnl_event_details', 'bsnl_event_details_nonce');

    $datetime = (string) get_post_meta($post->ID, 'bsnl_event_datetime', true);
    $datetime_value = $datetime ? str_replace(' ', 'T', substr($datetime, 0, 16)) : '';
    $fields = [
        'bsnl_event_datetime' => ['Event date and time', 'datetime-local', $datetime_value],
        'bsnl_event_location' => ['Location', 'text', (string) get_post_meta($post->ID, 'bsnl_event_location', true)],
        'bsnl_event_format' => ['Event format', 'text', (string) get_post_meta($post->ID, 'bsnl_event_format', true)],
        'bsnl_event_url' => ['Registration / details URL', 'url', (string) get_post_meta($post->ID, 'bsnl_event_url', true)],
        'bsnl_event_summary' => ['Short calendar summary', 'text', (string) get_post_meta($post->ID, 'bsnl_event_summary', true)],
    ];

    echo '<p style="margin-top:0;">Use these fields when this post is in the <strong>Upcoming Events</strong> category.</p>';
    foreach ($fields as $key => $field) {
        [$label, $type, $value] = $field;
        echo '<p><label for="' . esc_attr($key) . '"><strong>' . esc_html($label) . '</strong></label><br>';
        echo '<input style="width:100%;" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="' . esc_attr($type) . '" value="' . esc_attr($value) . '"></p>';
    }
}

function bsnl_light_add_event_details_meta_box(): void
{
    add_meta_box('bsnl-event-details', __('BSNL event details', 'bsnl-light'), 'bsnl_light_event_details_meta_box', 'post', 'side');
}
add_action('add_meta_boxes', 'bsnl_light_add_event_details_meta_box');

function bsnl_light_save_event_details(int $post_id): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!isset($_POST['bsnl_event_details_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bsnl_event_details_nonce'])), 'bsnl_event_details')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $text_fields = ['bsnl_event_location', 'bsnl_event_format', 'bsnl_event_summary'];
    foreach ($text_fields as $key) {
        $value = sanitize_text_field(wp_unslash($_POST[$key] ?? ''));
        '' === $value ? delete_post_meta($post_id, $key) : update_post_meta($post_id, $key, $value);
    }

    $url = esc_url_raw(wp_unslash($_POST['bsnl_event_url'] ?? ''));
    '' === $url ? delete_post_meta($post_id, 'bsnl_event_url') : update_post_meta($post_id, 'bsnl_event_url', $url);

    $datetime = sanitize_text_field(wp_unslash($_POST['bsnl_event_datetime'] ?? ''));
    $datetime = str_replace('T', ' ', $datetime);
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $datetime)) {
        update_post_meta($post_id, 'bsnl_event_datetime', $datetime . ':00');
    } else {
        delete_post_meta($post_id, 'bsnl_event_datetime');
    }
}
add_action('save_post_post', 'bsnl_light_save_event_details');

function bsnl_light_normalize_event_datetime(string $datetime): string
{
    $datetime = trim(str_replace(' ', 'T', $datetime));
    if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $datetime)) {
        $datetime .= ':00';
    }

    return $datetime;
}

function bsnl_light_event_from_posts(int $limit): array
{
    $query = new WP_Query([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'ignore_sticky_posts' => true,
        'no_found_rows' => true,
        'category_name' => 'upcoming-events',
        'meta_key' => 'bsnl_event_datetime',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => [
            [
                'key' => 'bsnl_event_datetime',
                'value' => current_time('mysql'),
                'compare' => '>=',
                'type' => 'DATETIME',
            ],
        ],
    ]);

    $events = [];
    foreach ($query->posts as $post) {
        $datetime = bsnl_light_normalize_event_datetime((string) get_post_meta($post->ID, 'bsnl_event_datetime', true));
        if (!$datetime) {
            continue;
        }

        $summary = (string) get_post_meta($post->ID, 'bsnl_event_summary', true);
        if ('' === $summary) {
            $summary = $post->post_excerpt ?: wp_trim_words(wp_strip_all_tags($post->post_content), 18, '');
        }

        $events[] = [
            'title' => get_the_title($post),
            'datetime' => $datetime,
            'location' => (string) get_post_meta($post->ID, 'bsnl_event_location', true),
            'summary' => $summary,
            'url' => (string) get_post_meta($post->ID, 'bsnl_event_url', true) ?: get_permalink($post),
            'format' => (string) get_post_meta($post->ID, 'bsnl_event_format', true),
        ];
    }

    return $events;
}

function bsnl_light_event_from_events_page(int $limit): array
{
    $page = get_page_by_title('Events');
    if (!$page instanceof WP_Post) {
        return [];
    }

    $content = (string) $page->post_content;
    preg_match_all('/<article class="bsnl-calendar-row"[^>]*data-event-datetime="([^"]+)"[^>]*>.*?<h3>(.*?)<\/h3>.*?<p>(.*?)<\/p>.*?<a[^>]*href="([^"]+)"/s', $content, $matches, PREG_SET_ORDER);

    $events = [];
    foreach ($matches as $match) {
        $description = trim(wp_strip_all_tags($match[3]));
        $parts = array_map('trim', explode('·', $description, 2));
        $url = esc_url_raw(html_entity_decode($match[4]));
        if (0 === strpos($url, '#')) {
            $url = get_permalink($page) . $url;
        }

        $events[] = [
            'title' => trim(wp_strip_all_tags($match[2])),
            'datetime' => bsnl_light_normalize_event_datetime($match[1]),
            'location' => $parts[0] ?? '',
            'summary' => $parts[1] ?? $description,
            'url' => $url,
        ];

        if (count($events) >= $limit) {
            break;
        }
    }

    return $events;
}

function bsnl_light_get_upcoming_events(int $limit = 4): array
{
    $events = [];
    foreach ([
        bsnl_light_event_from_posts($limit),
        bsnl_light_event_from_events_page($limit),
    ] as $event_group) {
        foreach ($event_group as $event) {
            if (!isset($event['datetime']) || !strtotime((string) $event['datetime'])) {
                continue;
            }

            $dedupe_key = strtolower(trim((string) ($event['title'] ?? ''))) . '|' . bsnl_light_normalize_event_datetime((string) $event['datetime']);
            if (isset($events[$dedupe_key])) {
                continue;
            }

            $events[$dedupe_key] = $event;
        }
    }

    $events = array_values($events);
    usort($events, static function (array $a, array $b): int {
        return strtotime((string) ($a['datetime'] ?? '')) <=> strtotime((string) ($b['datetime'] ?? ''));
    });

    return array_slice($events, 0, $limit);
}

function bsnl_light_event_timestamp(array $event): int
{
    $timestamp = strtotime((string) ($event['datetime'] ?? ''));
    return $timestamp ?: current_time('timestamp');
}

function bsnl_light_event_meta_text(array $event): string
{
    $timestamp = bsnl_light_event_timestamp($event);
    $date = date_i18n('j F Y', $timestamp);
    $location = trim((string) ($event['location'] ?? ''));

    return $location ? $date . ' | ' . $location : $date;
}

function bsnl_light_render_calendar_row(array $event): string
{
    $timestamp = bsnl_light_event_timestamp($event);
    $description = trim((string) ($event['location'] ?? ''));
    $summary = trim((string) ($event['summary'] ?? ''));
    if ($summary) {
        $description = $description ? $description . ' · ' . $summary : $summary;
    }

    ob_start();
    ?>
    <article class="bsnl-calendar-row" data-event-datetime="<?php echo esc_attr((string) ($event['datetime'] ?? '')); ?>">
      <div class="bsnl-calendar-date"><span><?php echo esc_html(date_i18n('M', $timestamp)); ?></span><strong><?php echo esc_html(date_i18n('j', $timestamp)); ?></strong></div>
      <div class="bsnl-calendar-body"><h3><?php echo esc_html((string) ($event['title'] ?? 'Upcoming BSNL event')); ?></h3><p><?php echo esc_html($description); ?></p></div>
      <a class="bsnl-section-link" href="<?php echo esc_url((string) ($event['url'] ?? bsnl_light_page_url('Events', 'upcoming'))); ?>">Details -></a>
    </article>
    <?php
    return (string) ob_get_clean();
}

function bsnl_light_upcoming_events_calendar_shortcode(array $atts = []): string
{
    $atts = shortcode_atts(['limit' => 4], $atts, 'bsnl_upcoming_events_calendar');
    $events = bsnl_light_get_upcoming_events(max(1, (int) $atts['limit']));

    ob_start();
    ?>
    <div class="bsnl-event-calendar"><div class="bsnl-calendar-board">
      <?php foreach ($events as $event) : ?>
        <?php echo bsnl_light_render_calendar_row($event); ?>
      <?php endforeach; ?>
    </div></div>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_upcoming_events_calendar', 'bsnl_light_upcoming_events_calendar_shortcode');

function bsnl_light_contact_details_shortcode(): string
{
    $email = bsnl_light_contact_email();

    ob_start();
    ?>
    <div class="bsnl-contact-stack bsnl-contact-static">
      <section class="bsnl-contact-info-block">
        <h2>Stay connected</h2>
        <p class="bsnl-contact-copy">Follow BSNL on <a href="https://www.linkedin.com/company/biosciencenetworklausanne">LinkedIn</a> and <a href="https://www.instagram.com/bsnllausanne/">Instagram</a>, or <a href="<?php echo esc_url(bsnl_light_newsletter_url()); ?>">subscribe to our newsletter</a> for the latest events, opportunities, and updates from Lausanne's life science community and beyond.</p>
        <p class="bsnl-contact-copy">For questions about our events, partnership opportunities, speaker proposals, alumni updates, or the BSNL community, contact us at <a class="bsnl-contact-email-link" href="<?php echo esc_url('mailto:' . $email); ?>"><?php echo esc_html($email); ?></a>.</p>
      </section>
      <section class="bsnl-contact-info-block">
        <h2>Location</h2>
        <p class="bsnl-contact-location-name">Lausanne, Switzerland</p>
        <p class="bsnl-contact-copy">BSNL operates across Lausanne's life science ecosystem. We do not maintain a public office, and event venues are listed on the relevant event page.</p>
      </section>
    </div>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_contact_details', 'bsnl_light_contact_details_shortcode');

function bsnl_light_contact_form_shortcode(array $atts = []): string
{
    $atts = shortcode_atts([
        'field_font_family' => '',
        'field_font_size' => '',
        'field_font_weight' => '',
        'button_font_family' => '',
        'button_font_size' => '',
        'button_font_weight' => '',
        'button_letter_spacing' => '',
        'label_font_size' => '',
        'message_height' => '',
    ], $atts, 'bsnl_contact_form');
    $style_vars = [
        '--bsnl-form-field-font-family' => bsnl_light_clean_css_value((string) $atts['field_font_family']),
        '--bsnl-form-field-font-size' => bsnl_light_clean_css_value((string) $atts['field_font_size']),
        '--bsnl-form-field-font-weight' => bsnl_light_clean_css_value((string) $atts['field_font_weight']),
        '--bsnl-form-button-font-family' => bsnl_light_clean_css_value((string) $atts['button_font_family']),
        '--bsnl-form-button-font-size' => bsnl_light_clean_css_value((string) $atts['button_font_size']),
        '--bsnl-form-button-font-weight' => bsnl_light_clean_css_value((string) $atts['button_font_weight']),
        '--bsnl-form-button-letter-spacing' => bsnl_light_clean_css_value((string) $atts['button_letter_spacing']),
        '--bsnl-form-label-font-size' => bsnl_light_clean_css_value((string) $atts['label_font_size']),
        '--bsnl-form-message-height' => bsnl_light_clean_css_value((string) $atts['message_height']),
    ];
    $inline_styles = [];
    foreach ($style_vars as $property => $value) {
        if ('' !== $value) {
            $inline_styles[] = $property . ':' . $value;
        }
    }

    $types = ['General', 'Events', 'Partnership', 'Speaker', 'Newsletter', 'Team', 'Alumni'];
    $values = [
        'name' => '',
        'email' => '',
        'affiliation' => '',
        'type' => '',
        'message' => '',
    ];
    $notice = '';
    $notice_class = '';

    if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '') && isset($_POST['bsnl_contact_form'])) {
        $values = [
            'name' => sanitize_text_field(wp_unslash($_POST['bsnl_name'] ?? '')),
            'email' => sanitize_email(wp_unslash($_POST['bsnl_email'] ?? '')),
            'affiliation' => sanitize_text_field(wp_unslash($_POST['bsnl_affiliation'] ?? '')),
            'type' => sanitize_text_field(wp_unslash($_POST['bsnl_inquiry_type'] ?? '')),
            'message' => sanitize_textarea_field(wp_unslash($_POST['bsnl_message'] ?? '')),
        ];

        if (!isset($_POST['bsnl_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bsnl_contact_nonce'])), 'bsnl_contact_form')) {
            $notice = __('The form could not be verified. Please refresh the page and try again.', 'bsnl-light');
            $notice_class = 'is-error';
        } elseif (!empty($_POST['bsnl_website'])) {
            $notice = __('Thank you. Your message has been received.', 'bsnl-light');
            $notice_class = 'is-success';
            $values = ['name' => '', 'email' => '', 'affiliation' => '', 'type' => '', 'message' => ''];
        } elseif ('' === $values['name'] || '' === $values['email'] || '' === $values['type'] || '' === $values['message']) {
            $notice = __('Please complete your name, email, enquiry type, and message.', 'bsnl-light');
            $notice_class = 'is-error';
        } elseif (!is_email($values['email'])) {
            $notice = __('Please use a valid email address.', 'bsnl-light');
            $notice_class = 'is-error';
        } else {
            if (!in_array($values['type'], $types, true)) {
                $notice = __('Please select a valid enquiry type.', 'bsnl-light');
                $notice_class = 'is-error';
            } else {
                $to = bsnl_light_contact_email();
                $host = parse_url(home_url('/'), PHP_URL_HOST) ?: 'bsnl.ch';
                $subject = sprintf('[BSNL %s enquiry] %s', $values['type'], $values['name']);
                $body = sprintf(
                    "Name: %s\nEmail: %s\nAffiliation / Organization: %s\nEnquiry type: %s\n\nMessage:\n%s\n",
                    $values['name'],
                    $values['email'],
                    $values['affiliation'],
                    $values['type'],
                    $values['message']
                );
                $headers = [
                    'From: BioScience Network Lausanne <wordpress@' . $host . '>',
                    'Reply-To: ' . $values['name'] . ' <' . $values['email'] . '>',
                ];

                if (wp_mail($to, $subject, $body, $headers)) {
                    $notice = __('Thank you. Your message has been sent to BSNL.', 'bsnl-light');
                    $notice_class = 'is-success';
                    $values = ['name' => '', 'email' => '', 'affiliation' => '', 'type' => '', 'message' => ''];
                } else {
                    $notice = sprintf(
                        __('The form could not be sent. Please write directly to %s.', 'bsnl-light'),
                        bsnl_light_contact_email()
                    );
                    $notice_class = 'is-error';
                }
            }
        }
    }

    ob_start();
    ?>
    <form id="bsnl-contact-form" class="bsnl-contact-form" method="post" action="#bsnl-contact-form" <?php if ($inline_styles) : ?>style="<?php echo esc_attr(implode(';', $inline_styles)); ?>"<?php endif; ?>>
      <?php if ($notice) : ?>
        <div class="bsnl-form-notice <?php echo esc_attr($notice_class); ?>"><?php echo esc_html($notice); ?></div>
      <?php endif; ?>
      <input type="hidden" name="bsnl_contact_form" value="1">
      <?php echo wp_nonce_field('bsnl_contact_form', 'bsnl_contact_nonce', true, false); ?>
      <p class="bsnl-field-hidden" aria-hidden="true"><label>Website <input type="text" name="bsnl_website" tabindex="-1" autocomplete="off"></label></p>
      <div class="bsnl-form-row">
        <label for="bsnl-name">Name <span class="bsnl-required" aria-hidden="true">*</span><span class="screen-reader-text">required</span></label>
        <input id="bsnl-name" name="bsnl_name" type="text" value="<?php echo esc_attr($values['name']); ?>" autocomplete="name" required>
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-email">Email <span class="bsnl-required" aria-hidden="true">*</span><span class="screen-reader-text">required</span></label>
        <input id="bsnl-email" name="bsnl_email" type="email" value="<?php echo esc_attr($values['email']); ?>" autocomplete="email" required>
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-affiliation">Affiliation / Organization</label>
        <input id="bsnl-affiliation" name="bsnl_affiliation" type="text" value="<?php echo esc_attr($values['affiliation']); ?>" autocomplete="organization">
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-inquiry-type">Enquiry type <span class="bsnl-required" aria-hidden="true">*</span><span class="screen-reader-text">required</span></label>
        <select id="bsnl-inquiry-type" name="bsnl_inquiry_type" required>
          <option value="" <?php selected($values['type'], ''); ?>><?php esc_html_e('Select an enquiry type', 'bsnl-light'); ?></option>
          <?php foreach ($types as $type) : ?>
            <option value="<?php echo esc_attr($type); ?>" <?php selected($values['type'], $type); ?>><?php echo esc_html($type); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-message">Message <span class="bsnl-required" aria-hidden="true">*</span><span class="screen-reader-text">required</span></label>
        <textarea id="bsnl-message" name="bsnl_message" rows="5" required><?php echo esc_textarea($values['message']); ?></textarea>
      </div>
      <button class="bsnl-button bsnl-button-quiet" type="submit">Send enquiry</button>
    </form>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_contact_form', 'bsnl_light_contact_form_shortcode');

function bsnl_light_alumni_request_form_shortcode(): string
{
    $values = [
        'name' => '',
        'email' => '',
        'years' => '',
        'role' => '',
        'affiliation' => '',
        'linkedin' => '',
        'message' => '',
    ];
    $notice = '';
    $notice_class = '';

    if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '') && isset($_POST['bsnl_alumni_form'])) {
        $values = [
            'name' => sanitize_text_field(wp_unslash($_POST['bsnl_alumni_name'] ?? '')),
            'email' => sanitize_email(wp_unslash($_POST['bsnl_alumni_email'] ?? '')),
            'years' => sanitize_text_field(wp_unslash($_POST['bsnl_alumni_years'] ?? '')),
            'role' => sanitize_text_field(wp_unslash($_POST['bsnl_alumni_role'] ?? '')),
            'affiliation' => sanitize_text_field(wp_unslash($_POST['bsnl_alumni_affiliation'] ?? '')),
            'linkedin' => esc_url_raw(wp_unslash($_POST['bsnl_alumni_linkedin'] ?? '')),
            'message' => sanitize_textarea_field(wp_unslash($_POST['bsnl_alumni_message'] ?? '')),
        ];

        if (!isset($_POST['bsnl_alumni_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bsnl_alumni_nonce'])), 'bsnl_alumni_form')) {
            $notice = __('The form could not be verified. Please refresh the page and try again.', 'bsnl-light');
            $notice_class = 'is-error';
        } elseif (!empty($_POST['bsnl_alumni_website'])) {
            $notice = __('Thank you. Your request has been received.', 'bsnl-light');
            $notice_class = 'is-success';
            $values = ['name' => '', 'email' => '', 'years' => '', 'role' => '', 'affiliation' => '', 'linkedin' => '', 'message' => ''];
        } elseif ('' === $values['name'] || '' === $values['email']) {
            $notice = __('Please complete at least your name and email.', 'bsnl-light');
            $notice_class = 'is-error';
        } elseif (!is_email($values['email'])) {
            $notice = __('Please use a valid email address.', 'bsnl-light');
            $notice_class = 'is-error';
        } else {
            $host = parse_url(home_url('/'), PHP_URL_HOST) ?: 'bsnl.ch';
            $subject = sprintf('[BSNL alumni directory request] %s', $values['name']);
            $body = sprintf(
                "Name: %s\nEmail: %s\nBSNL years: %s\nBSNL role: %s\nCurrent affiliation: %s\nLinkedIn: %s\n\nMessage:\n%s\n",
                $values['name'],
                $values['email'],
                $values['years'],
                $values['role'],
                $values['affiliation'],
                $values['linkedin'],
                $values['message']
            );
            $headers = [
                'From: BioScience Network Lausanne <wordpress@' . $host . '>',
                'Reply-To: ' . $values['name'] . ' <' . $values['email'] . '>',
            ];

            if (wp_mail(bsnl_light_contact_email(), $subject, $body, $headers)) {
                $notice = __('Thank you. Your alumni directory request has been sent to BSNL.', 'bsnl-light');
                $notice_class = 'is-success';
                $values = ['name' => '', 'email' => '', 'years' => '', 'role' => '', 'affiliation' => '', 'linkedin' => '', 'message' => ''];
            } else {
                $notice = sprintf(
                    __('The request could not be sent. Please write directly to %s.', 'bsnl-light'),
                    bsnl_light_contact_email()
                );
                $notice_class = 'is-error';
            }
        }
    }

    ob_start();
    ?>
    <form id="bsnl-alumni-request-form" class="bsnl-contact-form bsnl-alumni-request-form" method="post" action="#bsnl-alumni-request-form">
      <?php if ($notice) : ?>
        <div class="bsnl-form-notice <?php echo esc_attr($notice_class); ?>"><?php echo esc_html($notice); ?></div>
      <?php endif; ?>
      <input type="hidden" name="bsnl_alumni_form" value="1">
      <?php echo wp_nonce_field('bsnl_alumni_form', 'bsnl_alumni_nonce', true, false); ?>
      <p class="bsnl-field-hidden" aria-hidden="true"><label>Website <input type="text" name="bsnl_alumni_website" tabindex="-1" autocomplete="off"></label></p>
      <div class="bsnl-form-row">
        <label for="bsnl-alumni-name">Name</label>
        <input id="bsnl-alumni-name" name="bsnl_alumni_name" type="text" value="<?php echo esc_attr($values['name']); ?>" autocomplete="name" required>
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-alumni-email">Email</label>
        <input id="bsnl-alumni-email" name="bsnl_alumni_email" type="email" value="<?php echo esc_attr($values['email']); ?>" autocomplete="email" required>
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-alumni-years">BSNL years</label>
        <input id="bsnl-alumni-years" name="bsnl_alumni_years" type="text" value="<?php echo esc_attr($values['years']); ?>" placeholder="e.g. 2021-2023">
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-alumni-role">BSNL role</label>
        <input id="bsnl-alumni-role" name="bsnl_alumni_role" type="text" value="<?php echo esc_attr($values['role']); ?>" placeholder="e.g. committee member, event manager">
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-alumni-affiliation">Current affiliation / role</label>
        <input id="bsnl-alumni-affiliation" name="bsnl_alumni_affiliation" type="text" value="<?php echo esc_attr($values['affiliation']); ?>">
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-alumni-linkedin">LinkedIn</label>
        <input id="bsnl-alumni-linkedin" name="bsnl_alumni_linkedin" type="url" value="<?php echo esc_attr($values['linkedin']); ?>">
      </div>
      <div class="bsnl-form-row">
        <label for="bsnl-alumni-message">Message</label>
        <textarea id="bsnl-alumni-message" name="bsnl_alumni_message" rows="5"><?php echo esc_textarea($values['message']); ?></textarea>
      </div>
      <button class="bsnl-button bsnl-button-quiet" type="submit">Send alumni request</button>
    </form>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_alumni_request_form', 'bsnl_light_alumni_request_form_shortcode');

function bsnl_light_recruitment_notice_shortcode(array $atts = []): string
{
    $atts = shortcode_atts([
        'days' => 180,
        'fallback' => '',
        'link_text' => 'Read recruitment call',
    ], $atts, 'bsnl_recruitment_notice');

    $days = max(0, (int) $atts['days']);
    $query_args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'ignore_sticky_posts' => true,
        'no_found_rows' => true,
        'category_name' => 'recruitment',
    ];

    if ($days > 0) {
        $query_args['date_query'] = [
            [
                'after' => gmdate('Y-m-d', current_time('timestamp') - ($days * DAY_IN_SECONDS)),
                'inclusive' => true,
            ],
        ];
    }

    $query = new WP_Query($query_args);

    ob_start();
    ?>
    <div class="bsnl-recruitment-notice">
      <?php if ($query->have_posts()) : ?>
        <?php while ($query->have_posts()) : $query->the_post(); ?>
          <div class="bsnl-recruitment-label"><?php esc_html_e('Recruitment update', 'bsnl-light'); ?></div>
          <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 28, '')); ?></p>
          <a class="bsnl-section-link" href="<?php the_permalink(); ?>"><?php echo esc_html((string) $atts['link_text']); ?> -&gt;</a>
        <?php endwhile; wp_reset_postdata(); ?>
      <?php else : ?>
        <?php if ('' !== trim((string) $atts['fallback'])) : ?>
          <p><?php echo wp_kses_post((string) $atts['fallback']); ?></p>
        <?php else : ?>
          <p><?php echo wp_kses_post(sprintf(
              'Recruitment is closed for now. New calls are shared on the <a href="%s">News page</a>, <a href="%s">LinkedIn</a>, <a href="%s">Instagram</a>, and the <a href="%s">newsletter</a>.',
              esc_url(bsnl_light_page_url('News')),
              esc_url('https://www.linkedin.com/company/biosciencenetworklausanne'),
              esc_url('https://www.instagram.com/bsnllausanne/'),
              esc_url(bsnl_light_newsletter_url())
          )); ?></p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_recruitment_notice', 'bsnl_light_recruitment_notice_shortcode');

function bsnl_light_extract_first_image(string $html): string
{
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches)) {
        return esc_url_raw($matches[1]);
    }

    return '';
}

function bsnl_light_news_listing_shortcode(array $atts = []): string
{
    $atts = shortcode_atts([
        'posts' => 10,
        'paginate' => 'true',
    ], $atts, 'bsnl_news_listing');
    $posts_arg = strtolower(trim((string) $atts['posts']));
    $posts_per_page = ('all' === $posts_arg || '-1' === $posts_arg) ? -1 : max(1, (int) $atts['posts']);
    $paginate = in_array(strtolower((string) $atts['paginate']), ['1', 'true', 'yes'], true) && -1 !== $posts_per_page;
    $paged = $paginate ? max(1, (int) ($_GET['news_page'] ?? 1)) : 1;

    $query = new WP_Query([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'ignore_sticky_posts' => true,
        'category__not_in' => bsnl_light_upcoming_events_category_exclusions(),
    ]);

    ob_start();
    ?>
    <div class="bsnl-news-listing">
      <?php if ($query->have_posts()) : ?>
        <?php while ($query->have_posts()) : $query->the_post(); ?>
          <?php
          $image = has_post_thumbnail() ? (get_the_post_thumbnail_url(get_the_ID(), 'large') ?: '') : '';
          if ('' === $image) {
              $image = bsnl_light_extract_first_image((string) get_post_field('post_content', get_the_ID()));
          }
          $category = get_the_category();
          $label = $category ? $category[0]->name : __('News', 'bsnl-light');
          $author = bsnl_light_display_author(get_the_ID());
          ?>
          <article class="bsnl-news-list-item">
            <div class="bsnl-news-list-image" <?php if ($image) : ?>style="background-image:url('<?php echo esc_url($image); ?>')"<?php endif; ?> aria-hidden="true"></div>
            <div class="bsnl-news-list-body">
              <div class="bsnl-news-list-meta"><span><?php echo esc_html($label); ?></span><span><?php echo esc_html(get_the_date('j M Y')); ?></span></div>
              <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 30, '')); ?></p>
              <div class="bsnl-news-author">By <?php echo esc_html($author); ?></div>
              <a class="bsnl-section-link" href="<?php the_permalink(); ?>">Read -></a>
            </div>
          </article>
        <?php endwhile; wp_reset_postdata(); ?>
      <?php else : ?>
        <p><?php esc_html_e('No news yet.', 'bsnl-light'); ?></p>
      <?php endif; ?>
      <?php if ($paginate && $query->max_num_pages > 1) : ?>
        <?php
        $total_pages = (int) $query->max_num_pages;
        $window_size = 5;
        $window_start = max(1, min($paged - 2, max(1, $total_pages - $window_size + 1)));
        $window_end = min($total_pages, $window_start + $window_size - 1);
        $news_page_url = static function (int $page_number): string {
            $base_url = get_permalink();
            if ($page_number <= 1) {
                return esc_url(remove_query_arg('news_page', $base_url));
            }

            return esc_url(add_query_arg('news_page', $page_number, $base_url));
        };
        ?>
        <nav class="bsnl-news-pagination" aria-label="<?php esc_attr_e('News pagination', 'bsnl-light'); ?>">
          <?php if ($paged > 1) : ?>
            <a class="bsnl-page-arrow" href="<?php echo $news_page_url(1); ?>" aria-label="<?php esc_attr_e('First news page', 'bsnl-light'); ?>">&#124;&#8249;</a>
            <a class="bsnl-page-arrow" href="<?php echo $news_page_url($paged - 1); ?>" aria-label="<?php esc_attr_e('Previous news page', 'bsnl-light'); ?>">&#8249;</a>
          <?php else : ?>
            <span class="bsnl-page-arrow is-disabled" aria-hidden="true">&#124;&#8249;</span>
            <span class="bsnl-page-arrow is-disabled" aria-hidden="true">&#8249;</span>
          <?php endif; ?>
          <?php for ($page_number = $window_start; $page_number <= $window_end; $page_number++) : ?>
            <?php
            $page_url = $news_page_url($page_number);
            $is_current = $page_number === $paged;
            ?>
            <a class="<?php echo $is_current ? 'is-current' : ''; ?>" href="<?php echo $page_url; ?>" <?php if ($is_current) : ?>aria-current="page"<?php endif; ?>><?php echo esc_html((string) $page_number); ?></a>
          <?php endfor; ?>
          <?php if ($paged < $total_pages) : ?>
            <a class="bsnl-page-arrow" href="<?php echo $news_page_url($paged + 1); ?>" aria-label="<?php esc_attr_e('Next news page', 'bsnl-light'); ?>">&#8250;</a>
            <a class="bsnl-page-arrow" href="<?php echo $news_page_url($total_pages); ?>" aria-label="<?php esc_attr_e('Last news page', 'bsnl-light'); ?>">&#8250;&#124;</a>
          <?php else : ?>
            <span class="bsnl-page-arrow is-disabled" aria-hidden="true">&#8250;</span>
            <span class="bsnl-page-arrow is-disabled" aria-hidden="true">&#8250;&#124;</span>
          <?php endif; ?>
        </nav>
      <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_news_listing', 'bsnl_light_news_listing_shortcode');

function bsnl_light_home_posts(array $args): WP_Query
{
    return new WP_Query(array_merge([
        'post_type' => 'post',
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'no_found_rows' => true,
    ], $args));
}

function bsnl_light_home_post_card_image(WP_Post $post): string
{
    return has_post_thumbnail($post) ? (get_the_post_thumbnail_url($post, 'large') ?: '') : '';
}

function bsnl_light_home_events_shortcode(array $atts = []): string
{
    $atts = shortcode_atts([
        'next_event_label' => bsnl_light_home_text('next_event_label'),
        'view_event_text' => bsnl_light_home_text('view_event_text'),
        'more_events_title' => bsnl_light_home_text('more_events_title'),
        'full_calendar_text' => bsnl_light_home_text('full_calendar_text'),
    ], $atts, 'bsnl_home_events');
    $events_url = bsnl_light_page_url('Events', 'upcoming');
    $upcoming_events = bsnl_light_get_upcoming_events(4);
    $next_event = $upcoming_events[0] ?? [
        'title' => 'Upcoming BSNL event',
        'datetime' => '',
        'location' => 'BSNL',
        'summary' => 'New sessions are announced on the website, LinkedIn, Instagram, and the newsletter.',
        'url' => $events_url,
    ];
    $more_events = array_slice($upcoming_events, 1, 3);

    ob_start();
    ?>
    <section class="bsnl-event-module">
      <article class="bsnl-next-card" data-countdown="<?php echo esc_attr((string) ($next_event['datetime'] ?? '')); ?>">
        <div class="bsnl-module-label"><?php echo esc_html((string) $atts['next_event_label']); ?></div>
        <h3><?php echo esc_html((string) ($next_event['title'] ?? 'Upcoming BSNL event')); ?></h3>
        <p class="bsnl-event-meta"><?php echo esc_html(bsnl_light_event_meta_text($next_event)); ?></p>
        <p class="bsnl-next-description"><?php echo esc_html((string) ($next_event['summary'] ?? 'The next BSNL session for the Lausanne life science community.')); ?></p>
        <div class="bsnl-count-grid">
          <div><strong data-unit="days">--</strong><span>Days</span></div>
          <div><strong data-unit="hours">--</strong><span>Hours</span></div>
          <div><strong data-unit="mins">--</strong><span>Mins</span></div>
          <div><strong data-unit="secs">--</strong><span>Secs</span></div>
        </div>
        <a class="bsnl-section-link" href="<?php echo esc_url((string) ($next_event['url'] ?? $events_url)); ?>"><?php echo esc_html((string) $atts['view_event_text']); ?> -&gt;</a>
      </article>
      <aside class="bsnl-upcoming-list">
        <h3><?php echo esc_html((string) $atts['more_events_title']); ?></h3>
        <?php foreach ($more_events as $index => $event) : ?>
          <?php
          $timestamp = bsnl_light_event_timestamp($event);
          $box_classes = ['bsnl-date-box'];
          if (1 === $index) {
              $box_classes[] = 'bsnl-date-violet';
          } elseif (2 === $index) {
              $box_classes[] = 'bsnl-date-blue';
          }
          ?>
          <a class="bsnl-mini-event" href="<?php echo esc_url((string) ($event['url'] ?? $events_url)); ?>"><span class="<?php echo esc_attr(implode(' ', $box_classes)); ?>"><span><?php echo esc_html(date_i18n('M', $timestamp)); ?></span><strong><?php echo esc_html(date_i18n('j', $timestamp)); ?></strong></span><span><strong><?php echo esc_html((string) ($event['title'] ?? 'Upcoming event')); ?></strong><em><?php echo esc_html((string) ($event['location'] ?? 'BSNL')); ?></em></span></a>
        <?php endforeach; ?>
        <a class="bsnl-section-link" href="<?php echo esc_url($events_url); ?>"><?php echo esc_html((string) $atts['full_calendar_text']); ?> -&gt;</a>
      </aside>
    </section>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_home_events', 'bsnl_light_home_events_shortcode');

function bsnl_light_home_news_shortcode(): string
{
    $posts = bsnl_light_home_posts([
        'posts_per_page' => 6,
        'category__not_in' => bsnl_light_upcoming_events_category_exclusions(),
    ]);
    $fallback = [
        ['meta' => ['Workshop', '16 Apr 2026'], 'title' => 'Beyond the Bench at Biopole Lausanne', 'text' => 'A career workshop on values, transferable skills, and life science career planning.', 'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=900&q=80'],
        ['meta' => ['Career fair', '4 May 2026'], 'title' => 'Life Science Career Day 2026', 'text' => 'BSNL co-organized the 14th LSCD, bringing companies, organizations, and 300+ participants together.', 'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=80'],
        ['meta' => ['Biotech Chat', '27 May 2026'], 'title' => 'Project Management in Life Sciences', 'text' => 'A BSNL and Project Management Institute session on project management careers in life sciences.', 'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80'],
        ['meta' => ['Community', 'Newsletter'], 'title' => 'Stay connected with BSNL', 'text' => 'Join the mailing list for BSNL news, events, and opportunities.', 'image' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=900&q=80'],
    ];

    ob_start();
    ?>
    <div class="bsnl-highlights-carousel" data-highlight-carousel>
      <div class="bsnl-highlights-viewport">
        <div class="bsnl-news-cards">
          <?php if ($posts->have_posts()) : ?>
            <?php while ($posts->have_posts()) : $posts->the_post(); ?>
              <?php
              $image = bsnl_light_home_post_card_image(get_post());
              $image = '' !== $image ? $image : 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=900&q=80';
              $category = get_the_category();
              $label = $category ? $category[0]->name : 'News';
              ?>
              <article class="bsnl-news-card"><div class="bsnl-news-image" style="background-image:url('<?php echo esc_url($image); ?>')" aria-hidden="true"></div><div class="bsnl-news-body"><div class="bsnl-news-meta"><span><?php echo esc_html($label); ?></span><span><?php echo esc_html(get_the_date('j M Y')); ?></span></div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18, '')); ?></p></div></article>
            <?php endwhile; wp_reset_postdata(); ?>
          <?php else : ?>
            <?php foreach ($fallback as $item) : ?>
              <article class="bsnl-news-card"><div class="bsnl-news-image" style="background-image:url('<?php echo esc_url($item['image']); ?>')" aria-hidden="true"></div><div class="bsnl-news-body"><div class="bsnl-news-meta"><span><?php echo esc_html($item['meta'][0]); ?></span><span><?php echo esc_html($item['meta'][1]); ?></span></div><h3><?php echo esc_html($item['title']); ?></h3><p><?php echo esc_html($item['text']); ?></p></div></article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="bsnl-highlight-dots" aria-label="Highlights carousel controls"></div>
    </div>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_home_news', 'bsnl_light_home_news_shortcode');

function bsnl_light_site_footer(): void
{
    ?>
    <footer class="bsnl-follow-section bsnl-global-footer" id="newsletter">
      <div class="bsnl-follow-social">
        <p class="bsnl-follow-label">Stay connected with BSNL</p>
        <div class="bsnl-social-icons" aria-label="<?php esc_attr_e('Follow BSNL', 'bsnl-light'); ?>">
          <a class="bsnl-social-icon" href="https://www.linkedin.com/company/biosciencenetworklausanne" aria-label="<?php esc_attr_e('BSNL LinkedIn', 'bsnl-light'); ?>"><svg class="bsnl-social-logo" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.9 8.9H3.8V20h3.1V8.9ZM5.4 4a1.8 1.8 0 1 0 0 3.6A1.8 1.8 0 0 0 5.4 4Zm5.1 4.9h-3V20h3v-5.8c0-1.6.7-2.6 2.1-2.6 1.2 0 1.8.8 1.8 2.5V20h3.1v-6.4c0-3.3-1.7-5-4.2-5-1.4 0-2.3.6-2.8 1.4V8.9Z"/></svg></a>
          <a class="bsnl-social-icon" href="https://www.instagram.com/bsnllausanne/" aria-label="<?php esc_attr_e('BSNL Instagram', 'bsnl-light'); ?>"><svg class="bsnl-social-logo" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor"><rect x="4" y="4" width="16" height="16" rx="4"></rect><circle cx="12" cy="12" r="3.6"></circle><circle cx="16.8" cy="7.2" r="0.8" fill="currentColor" stroke="none"></circle></svg></a>
          <a class="bsnl-social-icon" href="<?php echo esc_url('mailto:' . bsnl_light_contact_email()); ?>" aria-label="<?php esc_attr_e('Email BSNL', 'bsnl-light'); ?>">@</a>
          <a class="bsnl-social-icon bsnl-newsletter-icon" href="<?php echo esc_url(bsnl_light_newsletter_url()); ?>" aria-label="<?php esc_attr_e('Subscribe to the BSNL newsletter', 'bsnl-light'); ?>">
            <svg class="bsnl-social-logo" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M5 5h14v14H5z"></path><path d="M8 9h8"></path><path d="M8 12h8"></path><path d="M8 15h5"></path></svg>
          </a>
          <a class="bsnl-social-icon bsnl-linktree-icon" href="https://linktr.ee/bsnllausanne" aria-label="<?php esc_attr_e('BSNL Linktree', 'bsnl-light'); ?>"><svg class="bsnl-social-logo" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v16"></path><path d="M7 8h10"></path><path d="M5.5 12h13"></path><path d="M8 16h8"></path></svg></a>
        </div>
      </div>
      <nav class="bsnl-footer-links" aria-label="<?php esc_attr_e('Organization footer links', 'bsnl-light'); ?>">
        <h3>Organization</h3>
        <a href="<?php echo esc_url(bsnl_light_page_url('About Us')); ?>">About Us</a>
        <a href="<?php echo esc_url(bsnl_light_page_url('Partnership')); ?>">Partnership</a>
        <a href="<?php echo esc_url(bsnl_light_page_url('Contact')); ?>">Contact</a>
      </nav>
      <nav class="bsnl-footer-links" aria-label="<?php esc_attr_e('Resources footer links', 'bsnl-light'); ?>">
        <h3>Resources</h3>
        <a href="<?php echo esc_url(bsnl_light_page_url('Events')); ?>">Events</a>
        <a href="<?php echo esc_url(bsnl_light_page_url('News')); ?>">News</a>
        <a href="https://linktr.ee/bsnllausanne">Linktree</a>
      </nav>
    </footer>
    <div class="bsnl-site-credit"><span>&copy; <?php echo esc_html(date('Y')); ?> BioScience Network Lausanne &middot; Cover image from Unsplash</span></div>
    <?php
}
