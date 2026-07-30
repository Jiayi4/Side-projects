<?php
/**
 * Search results template.
 */

if (!function_exists('bsnl_light_search_section_label')) {
    function bsnl_light_search_section_label(int $post_id): string
    {
        $post_type = get_post_type($post_id);

        if ('post' === $post_type) {
            $categories = get_the_category($post_id);
            $category_terms = [];
            foreach ($categories ?: [] as $category) {
                $category_terms[] = strtolower($category->slug . ' ' . $category->name);
            }
            $category_text = implode(' ', $category_terms);

            if (false !== strpos($category_text, 'recruit')) {
                return __('Recruitment', 'bsnl-light');
            }

            if (false !== strpos($category_text, 'upcoming')) {
                return __('Upcoming event', 'bsnl-light');
            }

            if (false !== strpos($category_text, 'event')) {
                return __('Events', 'bsnl-light');
            }

            return __('News', 'bsnl-light');
        }

        $slug = (string) get_post_field('post_name', $post_id);
        $title = strtolower((string) get_the_title($post_id));
        $events = [
            'events',
            'life-science-career-day-lscd',
            'life-science-career-day-2026',
            'faces-of-industrial-research-fir',
            'famelab',
            'biotech-chat',
            'biotech-chats',
            'workshops',
            'company-visits',
            'career-paths-in-life-sciences-2026',
            'soft-skills-negotiation-101-2026',
            'alumni-apero-epfl-innovation-park-2026',
        ];

        if (in_array($slug, $events, true) || false !== strpos($title, 'event') || false !== strpos($title, 'workshop')) {
            return __('Events', 'bsnl-light');
        }

        if ('about-us' === $slug || 'bsnl-bylaws' === $slug || 'our-team' === $slug || '420-2' === $slug) {
            return __('About Us', 'bsnl-light');
        }

        if ('news' === $slug) {
            return __('News', 'bsnl-light');
        }

        if ('partnership' === $slug) {
            return __('Partnership', 'bsnl-light');
        }

        if ('contact' === $slug) {
            return __('Contact', 'bsnl-light');
        }

        if ('home' === $slug) {
            return __('Home', 'bsnl-light');
        }

        return __('Page', 'bsnl-light');
    }
}

if (!function_exists('bsnl_light_search_excerpt')) {
    function bsnl_light_search_excerpt(int $post_id): string
    {
        $text = get_the_excerpt($post_id);

        if ('' === trim($text)) {
            $text = (string) get_post_field('post_content', $post_id);
        }

        $text = strip_shortcodes($text);
        $text = wp_strip_all_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, get_bloginfo('charset'));
        $text = preg_replace('/\s+/', ' ', $text) ?: '';
        $text = trim($text);

        return wp_trim_words($text, 24, '...');
    }
}

if (!function_exists('bsnl_light_search_result_meta')) {
    function bsnl_light_search_result_meta(int $post_id, string $section): string
    {
        if ('post' === get_post_type($post_id)) {
            return sprintf(
                /* translators: 1: post date, 2: author name */
                __('Post / %1$s / By %2$s', 'bsnl-light'),
                get_the_date('j M Y', $post_id),
                bsnl_light_display_author($post_id)
            );
        }

        return sprintf(
            /* translators: %s: website section label */
            __('Page / %s', 'bsnl-light'),
            $section
        );
    }
}

get_header();

$query = get_search_query();
global $wp_query;
?>

<main class="bsnl-page">
  <header class="bsnl-page-hero">
    <div class="bsnl-page-shell">
      <div class="bsnl-page-heading">
        <a class="bsnl-page-kicker" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Go to homepage', 'bsnl-light'); ?>"><span class="bsnl-page-dots" aria-hidden="true"><span></span><span></span><span></span></span>BioScience Network Lausanne</a>
        <h1><?php esc_html_e('Search', 'bsnl-light'); ?></h1>
        <div class="bsnl-page-title-rule" aria-hidden="true"></div>
      </div>
    </div>
  </header>

  <section class="bsnl-page-content bsnl-search-page">
    <form class="bsnl-search-results-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <label class="screen-reader-text" for="bsnl-search-results-field"><?php esc_html_e('Search BSNL', 'bsnl-light'); ?></label>
      <input id="bsnl-search-results-field" type="search" name="s" value="<?php echo esc_attr($query); ?>" placeholder="<?php esc_attr_e('Search BSNL', 'bsnl-light'); ?>">
      <button class="bsnl-button" type="submit"><?php esc_html_e('Search', 'bsnl-light'); ?></button>
    </form>

    <?php if ('' !== trim($query)) : ?>
      <p class="bsnl-search-summary">
        <?php
        printf(
            /* translators: 1: number of results, 2: search query */
            esc_html__('%1$s results for "%2$s"', 'bsnl-light'),
            esc_html(number_format_i18n((int) $wp_query->found_posts)),
            esc_html($query)
        );
        ?>
      </p>
    <?php endif; ?>

    <?php if (have_posts()) : ?>
      <div class="bsnl-search-results-list">
        <?php while (have_posts()) : the_post(); ?>
          <?php
          $section = bsnl_light_search_section_label(get_the_ID());
          $excerpt = bsnl_light_search_excerpt(get_the_ID());
          ?>
          <article <?php post_class('bsnl-search-card'); ?>>
            <div class="bsnl-search-card-meta">
              <span class="bsnl-search-tag"><?php echo esc_html($section); ?></span>
              <span><?php echo esc_html(bsnl_light_search_result_meta(get_the_ID(), $section)); ?></span>
            </div>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php if ('' !== $excerpt) : ?>
              <p><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
            <a class="bsnl-section-link" href="<?php the_permalink(); ?>"><?php esc_html_e('Open', 'bsnl-light'); ?> -&gt;</a>
          </article>
        <?php endwhile; ?>
      </div>
      <?php the_posts_pagination([
          'mid_size' => 1,
          'prev_text' => __('Previous', 'bsnl-light'),
          'next_text' => __('Next', 'bsnl-light'),
      ]); ?>
    <?php else : ?>
      <div class="bsnl-search-empty">
        <h2><?php esc_html_e('No results found', 'bsnl-light'); ?></h2>
        <p><?php esc_html_e('Try a broader keyword, or browse events, news, partnership, and contact information.', 'bsnl-light'); ?></p>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php
get_footer();
