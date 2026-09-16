<?php
/**
 * Plugin Name: BSNL Collaborators
 * Plugin URI: https://bsnl.ch/
 * Description: Manages Partnership collaborators with a name, logo, website URL, and display order.
 * Version: 1.1.0
 * Author: BioScience Network Lausanne
 * Text Domain: bsnl-collaborators
 */

if (!defined('ABSPATH')) {
    exit;
}

const BSNL_COLLABORATORS_POST_TYPE = 'bsnl_collaborator';
const BSNL_COLLABORATORS_URL_META = '_bsnl_collaborator_url';
const BSNL_COLLABORATORS_LEGACY_LOGO_META = '_bsnl_collaborator_legacy_logo';
const BSNL_COLLABORATORS_DISPLAY_OPTION = 'bsnl_collaborators_display';

function bsnl_collaborators_display_settings(): array
{
    $settings = get_option(BSNL_COLLABORATORS_DISPLAY_OPTION, []);
    $settings = is_array($settings) ? $settings : [];

    return [
        'logo_width' => min(360, max(80, absint($settings['logo_width'] ?? 224))),
        'logo_height' => min(160, max(40, absint($settings['logo_height'] ?? 72))),
    ];
}

function bsnl_collaborators_sanitize_display_settings($settings): array
{
    $settings = is_array($settings) ? $settings : [];

    return [
        'logo_width' => min(360, max(80, absint($settings['logo_width'] ?? 224))),
        'logo_height' => min(160, max(40, absint($settings['logo_height'] ?? 72))),
    ];
}

function bsnl_collaborators_enqueue_styles(): void
{
    wp_enqueue_style(
        'bsnl-collaborators-display',
        plugin_dir_url(__FILE__) . 'assets/css/display.css',
        [],
        '1.1.0'
    );
}
add_action('wp_enqueue_scripts', 'bsnl_collaborators_enqueue_styles', 20);

function bsnl_collaborators_register_post_type(): void
{
    register_post_type(BSNL_COLLABORATORS_POST_TYPE, [
        'labels' => [
            'name' => __('Collaborators', 'bsnl-collaborators'),
            'singular_name' => __('Collaborator', 'bsnl-collaborators'),
            'add_new_item' => __('Add New Collaborator', 'bsnl-collaborators'),
            'edit_item' => __('Edit Collaborator', 'bsnl-collaborators'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-groups',
        'supports' => ['title', 'thumbnail', 'page-attributes'],
        'map_meta_cap' => true,
    ]);

    register_post_meta(BSNL_COLLABORATORS_POST_TYPE, BSNL_COLLABORATORS_URL_META, [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
        'auth_callback' => static function (): bool {
            return current_user_can('edit_posts');
        },
    ]);
}
add_action('init', 'bsnl_collaborators_register_post_type');

function bsnl_collaborators_add_meta_box(): void
{
    add_meta_box(
        'bsnl-collaborator-details',
        __('Collaborator details', 'bsnl-collaborators'),
        'bsnl_collaborators_render_meta_box',
        BSNL_COLLABORATORS_POST_TYPE,
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_' . BSNL_COLLABORATORS_POST_TYPE, 'bsnl_collaborators_add_meta_box');

function bsnl_collaborators_render_meta_box(WP_Post $post): void
{
    wp_nonce_field('bsnl_collaborator_save', 'bsnl_collaborator_nonce');
    $url = (string) get_post_meta($post->ID, BSNL_COLLABORATORS_URL_META, true);
    $legacy_logo = (string) get_post_meta($post->ID, BSNL_COLLABORATORS_LEGACY_LOGO_META, true);
    ?>
    <p>
      <label for="bsnl-collaborator-url"><strong><?php esc_html_e('Organisation website', 'bsnl-collaborators'); ?></strong></label><br>
      <input class="widefat" id="bsnl-collaborator-url" name="bsnl_collaborator_url" type="url" value="<?php echo esc_attr($url); ?>" placeholder="https://example.org/">
    </p>
    <p><?php esc_html_e('Set the logo using Featured image in the right sidebar. Use Page Attributes > Order to control its position.', 'bsnl-collaborators'); ?></p>
    <?php if ($legacy_logo) : ?>
      <p><label for="bsnl-collaborator-legacy-logo"><?php esc_html_e('Legacy logo URL', 'bsnl-collaborators'); ?></label><br><input class="widefat" id="bsnl-collaborator-legacy-logo" name="bsnl_collaborator_legacy_logo" type="url" value="<?php echo esc_attr($legacy_logo); ?>"></p>
    <?php endif; ?>
    <?php
}

function bsnl_collaborators_save_meta(int $post_id): void
{
    if (!isset($_POST['bsnl_collaborator_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bsnl_collaborator_nonce'])), 'bsnl_collaborator_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $url = isset($_POST['bsnl_collaborator_url']) ? esc_url_raw(wp_unslash($_POST['bsnl_collaborator_url'])) : '';
    update_post_meta($post_id, BSNL_COLLABORATORS_URL_META, $url);

    if (isset($_POST['bsnl_collaborator_legacy_logo'])) {
        update_post_meta($post_id, BSNL_COLLABORATORS_LEGACY_LOGO_META, esc_url_raw(wp_unslash($_POST['bsnl_collaborator_legacy_logo'])));
    }
}
add_action('save_post_' . BSNL_COLLABORATORS_POST_TYPE, 'bsnl_collaborators_save_meta');

function bsnl_collaborators_columns(array $columns): array
{
    return [
        'cb' => $columns['cb'],
        'logo' => __('Logo', 'bsnl-collaborators'),
        'title' => __('Organisation', 'bsnl-collaborators'),
        'website' => __('Website', 'bsnl-collaborators'),
        'menu_order' => __('Order', 'bsnl-collaborators'),
        'date' => $columns['date'],
    ];
}
add_filter('manage_' . BSNL_COLLABORATORS_POST_TYPE . '_posts_columns', 'bsnl_collaborators_columns');

function bsnl_collaborators_column_content(string $column, int $post_id): void
{
    if ('logo' === $column) {
        $thumbnail = get_the_post_thumbnail($post_id, [80, 48]);
        $legacy_logo = (string) get_post_meta($post_id, BSNL_COLLABORATORS_LEGACY_LOGO_META, true);
        echo $thumbnail ? wp_kses_post($thumbnail) : ($legacy_logo ? '<img src="' . esc_url($legacy_logo) . '" alt="" style="max-width:80px;max-height:48px;">' : '&mdash;');
    }
    if ('website' === $column) {
        $url = (string) get_post_meta($post_id, BSNL_COLLABORATORS_URL_META, true);
        echo $url ? '<a href="' . esc_url($url) . '" target="_blank" rel="noopener">' . esc_html(wp_parse_url($url, PHP_URL_HOST) ?: $url) . '</a>' : '&mdash;';
    }
    if ('menu_order' === $column) {
        echo esc_html((string) get_post_field('menu_order', $post_id));
    }
}
add_action('manage_' . BSNL_COLLABORATORS_POST_TYPE . '_posts_custom_column', 'bsnl_collaborators_column_content', 10, 2);

function bsnl_collaborators_shortcode(array $atts = []): string
{
    $atts = shortcode_atts([
        'heading' => __('Collaborators', 'bsnl-collaborators'),
        'section_id' => 'collaborators',
    ], $atts, 'bsnl_collaborators');

    $query = new WP_Query([
        'post_type' => BSNL_COLLABORATORS_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
    ]);

    if (!$query->have_posts()) {
        return '';
    }

    ob_start();
    ?>
    <section class="bsnl-page-section" id="<?php echo esc_attr(sanitize_title((string) $atts['section_id'])); ?>">
      <h2><?php echo esc_html((string) $atts['heading']); ?></h2>
      <div class="bsnl-company-logo-grid bsnl-dynamic-collaborators" style="--bsnl-collaborator-logo-width:<?php echo esc_attr((string) bsnl_collaborators_display_settings()['logo_width']); ?>px;--bsnl-collaborator-logo-height:<?php echo esc_attr((string) bsnl_collaborators_display_settings()['logo_height']); ?>px;">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
          <?php
          $post_id = get_the_ID();
          $title = get_the_title();
          $url = (string) get_post_meta($post_id, BSNL_COLLABORATORS_URL_META, true);
          $legacy_logo = (string) get_post_meta($post_id, BSNL_COLLABORATORS_LEGACY_LOGO_META, true);
          $logo = has_post_thumbnail($post_id)
              ? get_the_post_thumbnail($post_id, 'medium', ['alt' => $title, 'loading' => 'lazy'])
              : ($legacy_logo ? '<img src="' . esc_url($legacy_logo) . '" alt="' . esc_attr($title) . '" loading="lazy">' : '<span>' . esc_html($title) . '</span>');
          ?>
          <?php if ($url) : ?>
            <a class="bsnl-company-logo-link" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr(sprintf(__('Visit %s', 'bsnl-collaborators'), $title)); ?>"><?php echo wp_kses_post($logo); ?></a>
          <?php else : ?>
            <div class="bsnl-company-logo-link" aria-label="<?php echo esc_attr($title); ?>"><?php echo wp_kses_post($logo); ?></div>
          <?php endif; ?>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}
add_shortcode('bsnl_collaborators', 'bsnl_collaborators_shortcode');

function bsnl_collaborators_add_import_page(): void
{
    add_submenu_page(
        'edit.php?post_type=' . BSNL_COLLABORATORS_POST_TYPE,
        __('Import existing collaborators', 'bsnl-collaborators'),
        __('Import existing collaborators', 'bsnl-collaborators'),
        'manage_options',
        'bsnl-collaborators-import',
        'bsnl_collaborators_import_page'
    );
}
add_action('admin_menu', 'bsnl_collaborators_add_import_page');

function bsnl_collaborators_add_display_settings_page(): void
{
    add_submenu_page(
        'edit.php?post_type=' . BSNL_COLLABORATORS_POST_TYPE,
        __('Logo display settings', 'bsnl-collaborators'),
        __('Logo display settings', 'bsnl-collaborators'),
        'manage_options',
        'bsnl-collaborators-display',
        'bsnl_collaborators_display_settings_page'
    );
}
add_action('admin_menu', 'bsnl_collaborators_add_display_settings_page');

function bsnl_collaborators_register_display_settings(): void
{
    register_setting(
        'bsnl_collaborators_display',
        BSNL_COLLABORATORS_DISPLAY_OPTION,
        ['sanitize_callback' => 'bsnl_collaborators_sanitize_display_settings']
    );
}
add_action('admin_init', 'bsnl_collaborators_register_display_settings');

function bsnl_collaborators_display_settings_page(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to manage logo display settings.', 'bsnl-collaborators'));
    }
    $settings = bsnl_collaborators_display_settings();
    ?>
    <div class="wrap">
      <h1><?php esc_html_e('Logo display settings', 'bsnl-collaborators'); ?></h1>
      <p><?php esc_html_e('These values apply to every Collaborator logo on the Partnership page.', 'bsnl-collaborators'); ?></p>
      <form method="post" action="options.php">
        <?php settings_fields('bsnl_collaborators_display'); ?>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="bsnl-collaborator-logo-width"><?php esc_html_e('Maximum logo width', 'bsnl-collaborators'); ?></label></th>
            <td><input id="bsnl-collaborator-logo-width" name="<?php echo esc_attr(BSNL_COLLABORATORS_DISPLAY_OPTION); ?>[logo_width]" type="number" min="80" max="360" step="1" value="<?php echo esc_attr((string) $settings['logo_width']); ?>"> px <p class="description"><?php esc_html_e('Default: 224 px. This controls the visual width of all logos.', 'bsnl-collaborators'); ?></p></td>
          </tr>
          <tr>
            <th scope="row"><label for="bsnl-collaborator-logo-height"><?php esc_html_e('Logo display height', 'bsnl-collaborators'); ?></label></th>
            <td><input id="bsnl-collaborator-logo-height" name="<?php echo esc_attr(BSNL_COLLABORATORS_DISPLAY_OPTION); ?>[logo_height]" type="number" min="40" max="160" step="1" value="<?php echo esc_attr((string) $settings['logo_height']); ?>"> px <p class="description"><?php esc_html_e('Default: 72 px. Images retain their proportions and are never stretched.', 'bsnl-collaborators'); ?></p></td>
          </tr>
        </table>
        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}

function bsnl_collaborators_get_attribute(string $markup, string $attribute): string
{
    if (!preg_match('/\\b' . preg_quote($attribute, '/') . '=("|\\\')(.*?)\\1/i', $markup, $match)) {
        return '';
    }
    return html_entity_decode((string) $match[2], ENT_QUOTES, get_bloginfo('charset'));
}

function bsnl_collaborators_import_existing(): array
{
    $page = get_page_by_path('partnership', OBJECT, 'page');
    if (!$page instanceof WP_Post) {
        return ['error' => __('The Partnership page could not be found.', 'bsnl-collaborators')];
    }

    $content = (string) $page->post_content;
    if (false !== strpos($content, '[bsnl_collaborators')) {
        return ['error' => __('The Partnership page already uses the Collaborators module.', 'bsnl-collaborators')];
    }
    if (!preg_match('/<section\\b(?=[^>]*\\bid=("|\\\')collaborators\\1)[^>]*>[\\s\\S]*?<\\/section>/i', $content, $section_match)) {
        return ['error' => __('The existing Collaborators section could not be identified. No changes were made.', 'bsnl-collaborators')];
    }

    preg_match_all('/<a\\b[^>]*>[\\s\\S]*?<\\/a>/i', $section_match[0], $anchor_matches);
    $cards = [];
    foreach ($anchor_matches[0] as $anchor) {
        if (false === strpos($anchor, 'bsnl-company-logo-link')) {
            continue;
        }
        $url = bsnl_collaborators_get_attribute($anchor, 'href');
        $logo = bsnl_collaborators_get_attribute($anchor, 'src');
        $title = bsnl_collaborators_get_attribute($anchor, 'alt');
        if (!$title) {
            $title = preg_replace('/^Visit\\s+/i', '', bsnl_collaborators_get_attribute($anchor, 'aria-label')) ?: '';
        }
        if (!$url || !$title) {
            return ['error' => __('One or more existing collaborator cards could not be read safely. No changes were made.', 'bsnl-collaborators')];
        }
        $cards[] = ['title' => sanitize_text_field($title), 'url' => esc_url_raw($url), 'logo' => esc_url_raw($logo)];
    }
    if (!$cards) {
        return ['error' => __('No collaborator cards were found. No changes were made.', 'bsnl-collaborators')];
    }

    foreach ($cards as $index => $card) {
        $existing = get_posts([
            'post_type' => BSNL_COLLABORATORS_POST_TYPE,
            'post_status' => 'any',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'meta_key' => BSNL_COLLABORATORS_URL_META,
            'meta_value' => $card['url'],
        ]);
        $post_id = $existing ? (int) $existing[0] : wp_insert_post([
            'post_type' => BSNL_COLLABORATORS_POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $card['title'],
            'menu_order' => ($index + 1) * 10,
        ], true);
        if (is_wp_error($post_id)) {
            return ['error' => $post_id->get_error_message()];
        }
        update_post_meta((int) $post_id, BSNL_COLLABORATORS_URL_META, $card['url']);
        if ($card['logo']) {
            update_post_meta((int) $post_id, BSNL_COLLABORATORS_LEGACY_LOGO_META, $card['logo']);
        }
    }

    $updated_content = str_replace($section_match[0], '[bsnl_collaborators]', $content);
    $result = wp_update_post(['ID' => (int) $page->ID, 'post_content' => $updated_content], true);
    if (is_wp_error($result)) {
        return ['error' => $result->get_error_message()];
    }
    return ['count' => count($cards)];
}

function bsnl_collaborators_import_page(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to import collaborators.', 'bsnl-collaborators'));
    }
    $message = '';
    if (isset($_POST['bsnl_collaborators_import'])) {
        check_admin_referer('bsnl_collaborators_import');
        $result = bsnl_collaborators_import_existing();
        $message = isset($result['error'])
            ? '<div class="notice notice-error"><p>' . esc_html($result['error']) . '</p></div>'
            : '<div class="notice notice-success"><p>' . esc_html(sprintf(__('%d collaborators imported. The Partnership page now uses the Collaborators module.', 'bsnl-collaborators'), $result['count'])) . '</p></div>';
    }
    ?>
    <div class="wrap">
      <h1><?php esc_html_e('Import existing collaborators', 'bsnl-collaborators'); ?></h1>
      <?php echo wp_kses_post($message); ?>
      <p><?php esc_html_e('This one-time import reads the current Collaborators logo grid, creates editable Collaborator entries, and replaces that grid on the Partnership page with the dynamic Collaborators module.', 'bsnl-collaborators'); ?></p>
      <p><?php esc_html_e('Existing external logo URLs are preserved. Replace them over time by setting a Featured image on each Collaborator.', 'bsnl-collaborators'); ?></p>
      <form method="post">
        <?php wp_nonce_field('bsnl_collaborators_import'); ?>
        <input type="hidden" name="bsnl_collaborators_import" value="1">
        <?php submit_button(__('Import collaborators', 'bsnl-collaborators'), 'primary', 'submit', false); ?>
      </form>
    </div>
    <?php
}
