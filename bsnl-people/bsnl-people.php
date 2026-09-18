<?php
/**
 * Plugin Name: BSNL People
 * Plugin URI: https://bsnl.ch/
 * Description: Manages BSNL board members, members, and alumni from one People directory.
 * Version: 1.3.2
 * Author: BioScience Network Lausanne
 * Text Domain: bsnl-people
 */

if (!defined('ABSPATH')) {
    exit;
}

const BSNL_PEOPLE_POST_TYPE = 'bsnl_person';
const BSNL_PEOPLE_GROUP_META = '_bsnl_people_group';
const BSNL_PEOPLE_ROLE_META = '_bsnl_people_role';
const BSNL_PEOPLE_LINKEDIN_META = '_bsnl_people_linkedin';
const BSNL_PEOPLE_SORT_NAME_META = '_bsnl_people_sort_name';
const BSNL_PEOPLE_LEGACY_PHOTO_META = '_bsnl_people_legacy_photo';
const BSNL_PEOPLE_ALUMNI_TYPE_META = '_bsnl_people_alumni_type';
const BSNL_PEOPLE_AFFILIATION_META = '_bsnl_people_affiliation';
const BSNL_PEOPLE_YEARS_META = '_bsnl_people_years';
const BSNL_PEOPLE_FORMER_ROLE_META = '_bsnl_people_former_role';
const BSNL_PEOPLE_TESTIMONIAL_META = '_bsnl_people_testimonial';

function bsnl_people_groups(): array
{
    return [
        'board' => __('Board', 'bsnl-people'),
        'members' => __('Members', 'bsnl-people'),
        'alumni' => __('Alumni', 'bsnl-people'),
    ];
}

function bsnl_people_alumni_types(): array
{
    return [
        'board_alumni' => __('Board Alumni', 'bsnl-people'),
        'member_alumni' => __('Member Alumni', 'bsnl-people'),
    ];
}

function bsnl_people_normalize_alumni_type(string $type): string
{
    $type = sanitize_key($type);
    if ('alumni' === $type || '' === $type) {
        return 'member_alumni';
    }

    return array_key_exists($type, bsnl_people_alumni_types()) ? $type : 'member_alumni';
}

function bsnl_people_normalize_group(string $group): string
{
    $aliases = [
        'current-board' => 'board',
        'board-member' => 'board',
        'board-members' => 'board',
        'member' => 'members',
        'alumnus' => 'alumni',
        'alumna' => 'alumni',
    ];
    $group = sanitize_key($group);
    $group = $aliases[$group] ?? $group;

    return array_key_exists($group, bsnl_people_groups()) ? $group : 'members';
}

function bsnl_people_register_post_type(): void
{
    register_post_type(BSNL_PEOPLE_POST_TYPE, [
        'labels' => [
            'name' => __('People', 'bsnl-people'),
            'singular_name' => __('Person', 'bsnl-people'),
            'add_new_item' => __('Add New Person', 'bsnl-people'),
            'edit_item' => __('Edit Person', 'bsnl-people'),
            'all_items' => __('All People', 'bsnl-people'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-groups',
        'supports' => ['title', 'thumbnail', 'page-attributes'],
        'map_meta_cap' => true,
    ]);

    foreach ([
        BSNL_PEOPLE_GROUP_META => 'sanitize_key',
        BSNL_PEOPLE_ROLE_META => 'sanitize_text_field',
        BSNL_PEOPLE_LINKEDIN_META => 'esc_url_raw',
        BSNL_PEOPLE_SORT_NAME_META => 'sanitize_text_field',
        BSNL_PEOPLE_LEGACY_PHOTO_META => 'esc_url_raw',
        BSNL_PEOPLE_ALUMNI_TYPE_META => 'sanitize_key',
        BSNL_PEOPLE_AFFILIATION_META => 'sanitize_text_field',
        BSNL_PEOPLE_YEARS_META => 'sanitize_text_field',
        BSNL_PEOPLE_FORMER_ROLE_META => 'sanitize_text_field',
        BSNL_PEOPLE_TESTIMONIAL_META => 'sanitize_textarea_field',
    ] as $meta_key => $sanitize_callback) {
        register_post_meta(BSNL_PEOPLE_POST_TYPE, $meta_key, [
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => $sanitize_callback,
            'auth_callback' => static function (): bool {
                return current_user_can('edit_posts');
            },
        ]);
    }
}
add_action('init', 'bsnl_people_register_post_type');

function bsnl_people_add_meta_box(): void
{
    add_meta_box(
        'bsnl-people-details',
        __('Person details', 'bsnl-people'),
        'bsnl_people_render_meta_box',
        BSNL_PEOPLE_POST_TYPE,
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_' . BSNL_PEOPLE_POST_TYPE, 'bsnl_people_add_meta_box');

function bsnl_people_render_meta_box(WP_Post $post): void
{
    wp_nonce_field('bsnl_people_save', 'bsnl_people_nonce');
    $group = bsnl_people_normalize_group((string) get_post_meta($post->ID, BSNL_PEOPLE_GROUP_META, true));
    $role = (string) get_post_meta($post->ID, BSNL_PEOPLE_ROLE_META, true);
    $linkedin = (string) get_post_meta($post->ID, BSNL_PEOPLE_LINKEDIN_META, true);
    $sort_name = (string) get_post_meta($post->ID, BSNL_PEOPLE_SORT_NAME_META, true);
    $affiliation = (string) get_post_meta($post->ID, BSNL_PEOPLE_AFFILIATION_META, true);
    $years = (string) get_post_meta($post->ID, BSNL_PEOPLE_YEARS_META, true);
    $former_role = (string) get_post_meta($post->ID, BSNL_PEOPLE_FORMER_ROLE_META, true);
    $testimonial = (string) get_post_meta($post->ID, BSNL_PEOPLE_TESTIMONIAL_META, true);
    $alumni_type = bsnl_people_normalize_alumni_type((string) get_post_meta($post->ID, BSNL_PEOPLE_ALUMNI_TYPE_META, true));
    $legacy_photo = (string) get_post_meta($post->ID, BSNL_PEOPLE_LEGACY_PHOTO_META, true);
    ?>
    <p>
      <label for="bsnl-people-group"><strong><?php esc_html_e('Group', 'bsnl-people'); ?></strong></label><br>
      <select class="widefat" id="bsnl-people-group" name="bsnl_people_group">
        <?php foreach (bsnl_people_groups() as $value => $label) : ?>
          <option value="<?php echo esc_attr($value); ?>" <?php selected($group, $value); ?>><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <p>
      <label for="bsnl-people-role"><strong><?php esc_html_e('Role', 'bsnl-people'); ?></strong></label><br>
      <input class="widefat" id="bsnl-people-role" name="bsnl_people_role" type="text" value="<?php echo esc_attr($role); ?>" placeholder="e.g. President, Events Team">
    </p>
    <p>
      <label for="bsnl-people-linkedin"><strong><?php esc_html_e('LinkedIn URL', 'bsnl-people'); ?></strong></label><br>
      <input class="widefat" id="bsnl-people-linkedin" name="bsnl_people_linkedin" type="url" value="<?php echo esc_attr($linkedin); ?>" placeholder="https://www.linkedin.com/in/...">
    </p>
    <p>
      <label for="bsnl-people-sort-name"><strong><?php esc_html_e('Custom alphabetical sort name', 'bsnl-people'); ?></strong></label><br>
      <input class="widefat" id="bsnl-people-sort-name" name="bsnl_people_sort_name" type="text" value="<?php echo esc_attr($sort_name); ?>" placeholder="Optional, e.g. Marie Curie">
      <span class="description"><?php esc_html_e('Members are sorted by first name. Alumni are sorted by leaving year, then first name. Fill this only when a name needs a custom alphabetical position.', 'bsnl-people'); ?></span>
    </p>
    <p><?php esc_html_e('Set the portrait with Featured image. For Board, use Page Attributes > Order to control position.', 'bsnl-people'); ?></p>
    <?php if ($legacy_photo && !has_post_thumbnail($post)) : ?>
      <p><strong><?php esc_html_e('Imported legacy photo', 'bsnl-people'); ?></strong><br><img src="<?php echo esc_url($legacy_photo); ?>" alt="" style="display:block;max-width:160px;max-height:160px;margin-top:8px;object-fit:cover;"></p>
    <?php endif; ?>
    <hr>
    <h3><?php esc_html_e('Alumni information', 'bsnl-people'); ?></h3>
    <p class="description"><?php esc_html_e('These fields preserve information from the legacy Alumni page. They are optional and do not change the current public page by themselves.', 'bsnl-people'); ?></p>
    <p>
      <label for="bsnl-people-alumni-type"><strong><?php esc_html_e('Alumni category', 'bsnl-people'); ?></strong></label><br>
      <select class="widefat" id="bsnl-people-alumni-type" name="bsnl_people_alumni_type">
        <?php foreach (bsnl_people_alumni_types() as $value => $label) : ?>
          <option value="<?php echo esc_attr($value); ?>" <?php selected($alumni_type, $value); ?>><?php echo esc_html($label); ?></option>
        <?php endforeach; ?>
      </select>
      <span class="description"><?php esc_html_e('Used when Group is set to Alumni.', 'bsnl-people'); ?></span>
    </p>
    <p>
      <label for="bsnl-people-affiliation"><strong><?php esc_html_e('Current affiliation or position', 'bsnl-people'); ?></strong></label><br>
      <input class="widefat" id="bsnl-people-affiliation" name="bsnl_people_affiliation" type="text" value="<?php echo esc_attr($affiliation); ?>">
    </p>
    <p>
      <label for="bsnl-people-years"><strong><?php esc_html_e('BSNL years', 'bsnl-people'); ?></strong></label><br>
      <input class="widefat" id="bsnl-people-years" name="bsnl_people_years" type="text" value="<?php echo esc_attr($years); ?>" placeholder="e.g. 2017-2019">
    </p>
    <p>
      <label for="bsnl-people-former-role"><strong><?php esc_html_e('Former BSNL role', 'bsnl-people'); ?></strong></label><br>
      <input class="widefat" id="bsnl-people-former-role" name="bsnl_people_former_role" type="text" value="<?php echo esc_attr($former_role); ?>">
    </p>
    <p>
      <label for="bsnl-people-testimonial"><strong><?php esc_html_e('Testimonial', 'bsnl-people'); ?></strong></label><br>
      <textarea class="widefat" id="bsnl-people-testimonial" name="bsnl_people_testimonial" rows="5"><?php echo esc_textarea($testimonial); ?></textarea>
    </p>
    <?php
}

function bsnl_people_save_meta(int $post_id): void
{
    if (!isset($_POST['bsnl_people_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bsnl_people_nonce'])), 'bsnl_people_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $group = bsnl_people_normalize_group((string) wp_unslash($_POST['bsnl_people_group'] ?? 'members'));
    update_post_meta($post_id, BSNL_PEOPLE_GROUP_META, $group);

    $fields = [
        BSNL_PEOPLE_ROLE_META => sanitize_text_field(wp_unslash($_POST['bsnl_people_role'] ?? '')),
        BSNL_PEOPLE_LINKEDIN_META => esc_url_raw(wp_unslash($_POST['bsnl_people_linkedin'] ?? '')),
        BSNL_PEOPLE_SORT_NAME_META => sanitize_text_field(wp_unslash($_POST['bsnl_people_sort_name'] ?? '')),
        BSNL_PEOPLE_ALUMNI_TYPE_META => bsnl_people_normalize_alumni_type((string) wp_unslash($_POST['bsnl_people_alumni_type'] ?? 'member_alumni')),
        BSNL_PEOPLE_AFFILIATION_META => sanitize_text_field(wp_unslash($_POST['bsnl_people_affiliation'] ?? '')),
        BSNL_PEOPLE_YEARS_META => sanitize_text_field(wp_unslash($_POST['bsnl_people_years'] ?? '')),
        BSNL_PEOPLE_FORMER_ROLE_META => sanitize_text_field(wp_unslash($_POST['bsnl_people_former_role'] ?? '')),
        BSNL_PEOPLE_TESTIMONIAL_META => sanitize_textarea_field(wp_unslash($_POST['bsnl_people_testimonial'] ?? '')),
    ];
    foreach ($fields as $meta_key => $value) {
        '' === $value ? delete_post_meta($post_id, $meta_key) : update_post_meta($post_id, $meta_key, $value);
    }
}
add_action('save_post_' . BSNL_PEOPLE_POST_TYPE, 'bsnl_people_save_meta');

function bsnl_people_columns(array $columns): array
{
    return [
        'cb' => $columns['cb'],
        'portrait' => __('Photo', 'bsnl-people'),
        'title' => __('Name', 'bsnl-people'),
        'group' => __('Group', 'bsnl-people'),
        'alumni_type' => __('Alumni category', 'bsnl-people'),
        'role' => __('Role', 'bsnl-people'),
        'menu_order' => __('Board order', 'bsnl-people'),
        'date' => $columns['date'],
    ];
}
add_filter('manage_' . BSNL_PEOPLE_POST_TYPE . '_posts_columns', 'bsnl_people_columns');

function bsnl_people_column_content(string $column, int $post_id): void
{
    if ('portrait' === $column) {
        $thumbnail = get_the_post_thumbnail($post_id, [56, 56]);
        $legacy_photo = (string) get_post_meta($post_id, BSNL_PEOPLE_LEGACY_PHOTO_META, true);
        echo $thumbnail ? wp_kses_post($thumbnail) : ($legacy_photo ? '<img src="' . esc_url($legacy_photo) . '" alt="" style="width:56px;height:56px;object-fit:cover;">' : '&mdash;');
    } elseif ('group' === $column) {
        $group = bsnl_people_normalize_group((string) get_post_meta($post_id, BSNL_PEOPLE_GROUP_META, true));
        echo esc_html(bsnl_people_groups()[$group]);
    } elseif ('alumni_type' === $column) {
        $group = bsnl_people_normalize_group((string) get_post_meta($post_id, BSNL_PEOPLE_GROUP_META, true));
        if ('alumni' === $group) {
            $type = bsnl_people_normalize_alumni_type((string) get_post_meta($post_id, BSNL_PEOPLE_ALUMNI_TYPE_META, true));
            echo esc_html(bsnl_people_alumni_types()[$type]);
        } else {
            echo '&mdash;';
        }
    } elseif ('role' === $column) {
        echo esc_html((string) get_post_meta($post_id, BSNL_PEOPLE_ROLE_META, true));
    } elseif ('menu_order' === $column) {
        echo esc_html((string) get_post_field('menu_order', $post_id));
    }
}
add_action('manage_' . BSNL_PEOPLE_POST_TYPE . '_posts_custom_column', 'bsnl_people_column_content', 10, 2);

function bsnl_people_group_filter(): void
{
    global $typenow;
    if (BSNL_PEOPLE_POST_TYPE !== $typenow) {
        return;
    }
    $requested = isset($_GET['bsnl_people_group_filter']) ? sanitize_key(wp_unslash($_GET['bsnl_people_group_filter'])) : '';
    ?>
    <select name="bsnl_people_group_filter">
      <option value=""><?php esc_html_e('All groups', 'bsnl-people'); ?></option>
      <?php foreach (bsnl_people_groups() as $value => $label) : ?>
        <option value="<?php echo esc_attr($value); ?>" <?php selected($requested, $value); ?>><?php echo esc_html($label); ?></option>
      <?php endforeach; ?>
    </select>
    <?php
    $requested_type = isset($_GET['bsnl_people_alumni_type_filter']) ? sanitize_key(wp_unslash($_GET['bsnl_people_alumni_type_filter'])) : '';
    $requested_type = '' !== $requested_type ? bsnl_people_normalize_alumni_type($requested_type) : '';
    ?>
    <select name="bsnl_people_alumni_type_filter">
      <option value=""><?php esc_html_e('All alumni categories', 'bsnl-people'); ?></option>
      <?php foreach (bsnl_people_alumni_types() as $value => $label) : ?>
        <option value="<?php echo esc_attr($value); ?>" <?php selected($requested_type, $value); ?>><?php echo esc_html($label); ?></option>
      <?php endforeach; ?>
    </select>
    <?php
}
add_action('restrict_manage_posts', 'bsnl_people_group_filter');

function bsnl_people_apply_group_filter(WP_Query $query): void
{
    global $pagenow;
    if (!is_admin() || 'edit.php' !== $pagenow || !$query->is_main_query() || BSNL_PEOPLE_POST_TYPE !== $query->get('post_type')) {
        return;
    }
    $requested = isset($_GET['bsnl_people_group_filter']) ? sanitize_key(wp_unslash($_GET['bsnl_people_group_filter'])) : '';
    if (array_key_exists($requested, bsnl_people_groups())) {
        $query->set('meta_key', BSNL_PEOPLE_GROUP_META);
        $query->set('meta_value', $requested);
    }
    $requested_type = isset($_GET['bsnl_people_alumni_type_filter']) ? sanitize_key(wp_unslash($_GET['bsnl_people_alumni_type_filter'])) : '';
    if (array_key_exists($requested_type, bsnl_people_alumni_types())) {
        $meta_query = (array) $query->get('meta_query');
        $meta_query[] = 'member_alumni' === $requested_type ? [
            'relation' => 'OR',
            [
                'key' => BSNL_PEOPLE_ALUMNI_TYPE_META,
                'value' => ['member_alumni', 'alumni'],
                'compare' => 'IN',
            ],
            [
                'key' => BSNL_PEOPLE_ALUMNI_TYPE_META,
                'compare' => 'NOT EXISTS',
            ],
        ] : [
            'key' => BSNL_PEOPLE_ALUMNI_TYPE_META,
            'value' => $requested_type,
            'compare' => '=',
        ];
        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'bsnl_people_apply_group_filter');

function bsnl_people_bulk_actions(array $actions): array
{
    $actions['bsnl_people_move_board'] = __('Move to Board', 'bsnl-people');
    $actions['bsnl_people_move_members'] = __('Move to Members', 'bsnl-people');
    $actions['bsnl_people_move_alumni'] = __('Move to Alumni', 'bsnl-people');

    return $actions;
}
add_filter('bulk_actions-edit-' . BSNL_PEOPLE_POST_TYPE, 'bsnl_people_bulk_actions');

function bsnl_people_handle_bulk_action(string $redirect_url, string $action, array $post_ids): string
{
    $groups = [
        'bsnl_people_move_board' => 'board',
        'bsnl_people_move_members' => 'members',
        'bsnl_people_move_alumni' => 'alumni',
    ];
    if (!isset($groups[$action])) {
        return $redirect_url;
    }

    $moved = 0;
    foreach ($post_ids as $post_id) {
        $post_id = absint($post_id);
        if ($post_id && current_user_can('edit_post', $post_id)) {
            update_post_meta($post_id, BSNL_PEOPLE_GROUP_META, $groups[$action]);
            $moved++;
        }
    }

    return add_query_arg([
        'bsnl_people_moved' => $moved,
        'bsnl_people_group' => $groups[$action],
    ], $redirect_url);
}
add_filter('handle_bulk_actions-edit-' . BSNL_PEOPLE_POST_TYPE, 'bsnl_people_handle_bulk_action', 10, 3);

function bsnl_people_bulk_action_notice(): void
{
    if (!isset($_GET['bsnl_people_moved'], $_GET['bsnl_people_group'])) {
        return;
    }
    $moved = absint($_GET['bsnl_people_moved']);
    $group = bsnl_people_normalize_group(sanitize_key(wp_unslash($_GET['bsnl_people_group'])));
    printf(
        '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
        esc_html(sprintf(_n('%1$d person moved to %2$s.', '%1$d people moved to %2$s.', $moved, 'bsnl-people'), $moved, bsnl_people_groups()[$group]))
    );
}
add_action('admin_notices', 'bsnl_people_bulk_action_notice');

function bsnl_people_sort_key(WP_Post $person): string
{
    $custom = trim((string) get_post_meta($person->ID, BSNL_PEOPLE_SORT_NAME_META, true));
    if ('' !== $custom) {
        $custom = function_exists('mb_strtolower') ? mb_strtolower($custom) : strtolower($custom);
        return remove_accents($custom);
    }

    $name = trim(wp_strip_all_tags(get_the_title($person)));
    $parts = preg_split('/\s+/u', $name) ?: [];
    $first_name = $parts ? (string) reset($parts) : $name;

    $key = $first_name . ' ' . $name;
    $key = function_exists('mb_strtolower') ? mb_strtolower($key) : strtolower($key);

    return remove_accents($key);
}

function bsnl_people_alumni_leaving_year(WP_Post $person): int
{
    $years = (string) get_post_meta($person->ID, BSNL_PEOPLE_YEARS_META, true);
    if (!preg_match_all('/\b(?:19|20)\d{2}\b/', $years, $matches) || empty($matches[0])) {
        return 0;
    }

    return (int) end($matches[0]);
}

function bsnl_people_get_people(string $group, string $alumni_type = ''): array
{
    $args = [
        'post_type' => BSNL_PEOPLE_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => BSNL_PEOPLE_GROUP_META,
        'meta_value' => $group,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
        'no_found_rows' => true,
    ];
    if ('alumni' === $group && '' !== $alumni_type) {
        $normalized_type = bsnl_people_normalize_alumni_type($alumni_type);
        $args['meta_query'] = 'member_alumni' === $normalized_type ? [
            'relation' => 'OR',
            [
                'key' => BSNL_PEOPLE_ALUMNI_TYPE_META,
                'value' => ['member_alumni', 'alumni'],
                'compare' => 'IN',
            ],
            [
                'key' => BSNL_PEOPLE_ALUMNI_TYPE_META,
                'compare' => 'NOT EXISTS',
            ],
        ] : [
            [
                'key' => BSNL_PEOPLE_ALUMNI_TYPE_META,
                'value' => $normalized_type,
                'compare' => '=',
            ],
        ];
    }
    $people = get_posts($args);

    if ('alumni' === $group) {
        usort($people, static function (WP_Post $a, WP_Post $b): int {
            $year_comparison = bsnl_people_alumni_leaving_year($b) <=> bsnl_people_alumni_leaving_year($a);
            return 0 !== $year_comparison ? $year_comparison : strnatcasecmp(bsnl_people_sort_key($a), bsnl_people_sort_key($b));
        });
    } elseif ('board' !== $group) {
        usort($people, static function (WP_Post $a, WP_Post $b): int {
            return strnatcasecmp(bsnl_people_sort_key($a), bsnl_people_sort_key($b));
        });
    }

    return $people;
}

function bsnl_people_initials(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name)) ?: [];
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= function_exists('mb_substr') ? mb_substr($part, 0, 1) : substr($part, 0, 1);
    }

    return strtoupper($initials);
}

function bsnl_people_render_cards(array $people): string
{
    ob_start();
    ?>
    <div class="bsnl-team-grid bsnl-people-grid">
      <?php foreach ($people as $person) : ?>
        <?php
        $name = get_the_title($person);
        $role = (string) get_post_meta($person->ID, BSNL_PEOPLE_ROLE_META, true);
        $linkedin = (string) get_post_meta($person->ID, BSNL_PEOPLE_LINKEDIN_META, true);
        $legacy_photo = (string) get_post_meta($person->ID, BSNL_PEOPLE_LEGACY_PHOTO_META, true);
        ?>
        <article class="bsnl-team-card bsnl-person-card">
          <figure>
            <?php if (has_post_thumbnail($person)) : ?>
              <?php echo get_the_post_thumbnail($person, 'medium_large', ['alt' => $name, 'loading' => 'lazy']); ?>
            <?php elseif ($legacy_photo) : ?>
              <img src="<?php echo esc_url($legacy_photo); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
            <?php else : ?>
              <span class="bsnl-person-placeholder" aria-hidden="true"><?php echo esc_html(bsnl_people_initials($name)); ?></span>
            <?php endif; ?>
          </figure>
          <h3><?php echo esc_html($name); ?></h3>
          <?php if ($role) : ?><p><?php echo esc_html($role); ?></p><?php endif; ?>
          <?php if ($linkedin) : ?><a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener">LinkedIn -&gt;</a><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

function bsnl_people_render_alumni(array $people): string
{
    ob_start();
    ?>
    <div class="bsnl-alumni-directory bsnl-people-alumni">
      <?php foreach ($people as $person) : ?>
        <?php
        $name = get_the_title($person);
        $role = (string) get_post_meta($person->ID, BSNL_PEOPLE_ROLE_META, true);
        $linkedin = (string) get_post_meta($person->ID, BSNL_PEOPLE_LINKEDIN_META, true);
        ?>
        <article class="bsnl-alumni-entry">
          <strong><?php echo esc_html($name); ?></strong>
          <span>
            <?php if ($role) : ?><?php echo esc_html($role); ?><?php endif; ?>
            <?php if ($linkedin) : ?><a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener">LinkedIn -&gt;</a><?php endif; ?>
          </span>
        </article>
      <?php endforeach; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

function bsnl_people_shortcode(array $atts = []): string
{
    $atts = shortcode_atts(['group' => 'members', 'alumni_type' => ''], $atts, 'bsnl_people');
    $group = bsnl_people_normalize_group((string) $atts['group']);
    $people = bsnl_people_get_people($group, (string) $atts['alumni_type']);
    if (!$people) {
        return '';
    }

    return 'alumni' === $group ? bsnl_people_render_alumni($people) : bsnl_people_render_cards($people);
}
add_shortcode('bsnl_people', 'bsnl_people_shortcode');

function bsnl_people_enqueue_styles(): void
{
    wp_enqueue_style(
        'bsnl-people',
        plugin_dir_url(__FILE__) . 'assets/css/people.css',
        [],
        '1.3.2'
    );
}
add_action('wp_enqueue_scripts', 'bsnl_people_enqueue_styles', 20);

function bsnl_people_add_import_page(): void
{
    add_submenu_page(
        'edit.php?post_type=' . BSNL_PEOPLE_POST_TYPE,
        __('Import existing people', 'bsnl-people'),
        __('Import existing people', 'bsnl-people'),
        'manage_options',
        'bsnl-people-import',
        'bsnl_people_import_page'
    );
}
add_action('admin_menu', 'bsnl_people_add_import_page');

function bsnl_people_dom_text(DOMXPath $xpath, DOMNode $context, string $query): string
{
    $nodes = $xpath->query($query, $context);
    if (!$nodes || 0 === $nodes->length) {
        return '';
    }

    return trim(preg_replace('/\s+/u', ' ', (string) $nodes->item(0)->textContent) ?: '');
}

function bsnl_people_dom_attribute(DOMXPath $xpath, DOMNode $context, string $query, string $attribute): string
{
    $nodes = $xpath->query($query, $context);
    if (!$nodes || 0 === $nodes->length || !$nodes->item(0) instanceof DOMElement) {
        return '';
    }

    return trim((string) $nodes->item(0)->getAttribute($attribute));
}

function bsnl_people_group_from_node(DOMNode $node): string
{
    $context = '';
    $current = $node;
    for ($depth = 0; $depth < 4 && $current instanceof DOMElement; $depth++) {
        $context .= ' ' . $current->getAttribute('id') . ' ' . $current->getAttribute('class');
        $current = $current->parentNode;
    }
    $context = strtolower($context);
    if (false !== strpos($context, 'alumni')) {
        return 'alumni';
    }
    if (false !== strpos($context, 'board') || false !== strpos($context, 'committee')) {
        return 'board';
    }

    return 'members';
}

function bsnl_people_existing_names(): array
{
    $names = [];
    $people = get_posts([
        'post_type' => BSNL_PEOPLE_POST_TYPE,
        'post_status' => ['publish', 'draft', 'private'],
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);
    foreach ($people as $person_id) {
        $name = strtolower(trim((string) get_the_title((int) $person_id)));
        if ('' !== $name) {
            $names[$name] = true;
        }
    }

    return $names;
}

function bsnl_people_import_existing(): array
{
    if (!class_exists('DOMDocument')) {
        return ['created' => 0, 'skipped' => 0, 'error' => __('The PHP DOM extension is not available, so the existing cards could not be read.', 'bsnl-people')];
    }

    $created = 0;
    $skipped = 0;
    $existing = bsnl_people_existing_names();
    $pages = get_pages(['post_status' => ['publish', 'draft', 'private'], 'number' => 0]);

    foreach ($pages as $page) {
        if (!($page instanceof WP_Post) || (false === stripos((string) $page->post_content, 'bsnl-team-card') && false === stripos((string) $page->post_content, 'bsnl-alumni-entry'))) {
            continue;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . (string) $page->post_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if (!$loaded) {
            continue;
        }

        $xpath = new DOMXPath($dom);
        $queries = [
            ['//*[contains(concat(" ", normalize-space(@class), " "), " bsnl-team-card ")]', null],
            ['//*[contains(concat(" ", normalize-space(@class), " "), " bsnl-alumni-entry ")]', 'alumni'],
        ];

        foreach ($queries as [$query, $forced_group]) {
            $nodes = $xpath->query($query);
            if (!$nodes) {
                continue;
            }
            foreach ($nodes as $node) {
                $name = bsnl_people_dom_text($xpath, $node, './/h3[1]');
                if ('' === $name) {
                    $name = bsnl_people_dom_text($xpath, $node, './/strong[1]');
                }
                $key = strtolower($name);
                if ('' === $name || isset($existing[$key])) {
                    $skipped++;
                    continue;
                }

                $role = bsnl_people_dom_text($xpath, $node, './/p[1]');
                if ('' === $role) {
                    $role = bsnl_people_dom_text($xpath, $node, './/span[1]');
                }
                $linkedin = bsnl_people_dom_attribute($xpath, $node, './/a[1]', 'href');
                $photo = bsnl_people_dom_attribute($xpath, $node, './/img[1]', 'src');
                $group = $forced_group ?: bsnl_people_group_from_node($node);

                $person_id = wp_insert_post([
                    'post_type' => BSNL_PEOPLE_POST_TYPE,
                    'post_status' => 'publish',
                    'post_title' => $name,
                ], true);
                if (is_wp_error($person_id)) {
                    $skipped++;
                    continue;
                }

                update_post_meta($person_id, BSNL_PEOPLE_GROUP_META, $group);
                if ($role) {
                    update_post_meta($person_id, BSNL_PEOPLE_ROLE_META, sanitize_text_field($role));
                }
                if ($linkedin) {
                    update_post_meta($person_id, BSNL_PEOPLE_LINKEDIN_META, esc_url_raw($linkedin));
                }
                if ($photo) {
                    update_post_meta($person_id, BSNL_PEOPLE_LEGACY_PHOTO_META, esc_url_raw($photo));
                }
                $existing[$key] = true;
                $created++;
            }
        }
    }

    return ['created' => $created, 'skipped' => $skipped, 'error' => ''];
}

function bsnl_people_normalized_name(string $name): string
{
    $name = html_entity_decode(wp_strip_all_tags($name), ENT_QUOTES, 'UTF-8');
    $name = preg_replace('/\s*[|,]\s*(?:twitter|linkedin)\s*$/iu', '', $name) ?: $name;
    $name = preg_replace('/\s+/u', ' ', trim($name)) ?: trim($name);

    return $name;
}

function bsnl_people_looks_like_name(string $text): bool
{
    $text = bsnl_people_normalized_name($text);
    if ('' === $text || strlen($text) > 100 || false !== strpos($text, ':') || false !== strpos($text, '@') || false !== stripos($text, 'http')) {
        return false;
    }
    $lower = strtolower(remove_accents($text));
    $excluded = ['alumni', 'board alumni', 'contact us', 'follow us', 'image', 'testimonial', 'current position'];
    if (in_array($lower, $excluded, true)) {
        return false;
    }
    $without_degree = preg_replace('/,?\s*(?:ph\.?d\.?|md|msc|mba)\b/iu', '', $text) ?: $text;
    $parts = preg_split('/\s+/u', trim($without_degree)) ?: [];

    return count($parts) >= 2 && count($parts) <= 8 && 1 === preg_match('/\p{L}/u', $text);
}

function bsnl_people_legacy_block_lines(array $blocks): array
{
    $lines = [];
    foreach ($blocks as $block) {
        $block = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) $block)) ?: '');
        if ('' !== $block && !in_array($block, $lines, true)) {
            $lines[] = $block;
        }
    }

    return $lines;
}

function bsnl_people_finalize_alumni_candidate(?array $candidate): ?array
{
    if (!$candidate || !bsnl_people_looks_like_name((string) ($candidate['name'] ?? ''))) {
        return null;
    }

    $lines = bsnl_people_legacy_block_lines($candidate['lines'] ?? []);
    $affiliation = '';
    $years = '';
    $former_role = '';
    $testimonial = '';

    foreach ($lines as $line) {
        if (preg_match('/\bBSNL\s*:\s*(.+)$/iu', $line, $matches)) {
            $years = trim($matches[1]);
            continue;
        }
        if (false !== stripos($line, 'former ') || false !== stripos($line, 'co-founder') || false !== stripos($line, 'board member')) {
            $former_role = trim($former_role . ' ' . $line);
            continue;
        }
        if (strlen($line) >= 170 || preg_match('/^[“"\']/', $line)) {
            $testimonial = trim($testimonial . ' ' . $line);
            continue;
        }
        if ('' === $affiliation && 0 !== strcasecmp($line, (string) $candidate['name']) && 'Image' !== $line) {
            $affiliation = $line;
        }
    }

    return [
        'name' => bsnl_people_normalized_name((string) $candidate['name']),
        'alumni_type' => 'board' === ($candidate['section'] ?? '') ? 'board_alumni' : 'member_alumni',
        'affiliation' => $affiliation,
        'years' => $years,
        'former_role' => $former_role,
        'testimonial' => $testimonial,
        'linkedin' => esc_url_raw((string) ($candidate['linkedin'] ?? '')),
        'photo' => esc_url_raw((string) ($candidate['photo'] ?? '')),
    ];
}

function bsnl_people_scan_board_alumni_text(string $html, array $photos = []): array
{
    $with_breaks = preg_replace('/<br\s*\/?\s*>/iu', "\n", $html) ?: $html;
    $with_breaks = preg_replace('/<\/(?:p|div|section|article|h[1-6]|blockquote|li)>/iu', "\n", $with_breaks) ?: $with_breaks;
    $text = html_entity_decode(wp_strip_all_tags($with_breaks), ENT_QUOTES, 'UTF-8');
    $lines = preg_split('/\R+/u', $text) ?: [];

    $inside_board = false;
    $candidate = null;
    $candidates = [];
    $photo_index = 0;
    foreach ($lines as $line) {
        $line = trim(preg_replace('/\s+/u', ' ', $line) ?: '');
        if ('' === $line) {
            continue;
        }
        $lower = strtolower(remove_accents($line));
        if (!$inside_board) {
            if ('board alumni' === $lower) {
                $inside_board = true;
            }
            continue;
        }
        if ('alumni' === $lower) {
            break;
        }

        if (preg_match('/,\s*ph\.?d\.?$/iu', $line) && bsnl_people_looks_like_name($line)) {
            $finished = bsnl_people_finalize_alumni_candidate($candidate);
            if ($finished) {
                $candidates[] = $finished;
            }
            $candidate = [
                'name' => $line,
                'section' => 'board',
                'lines' => [],
                'linkedin' => '',
                'photo' => (string) ($photos[$photo_index] ?? ''),
            ];
            $photo_index++;
            continue;
        }

        if ($candidate && 'image' !== $lower) {
            $candidate['lines'][] = $line;
        }
    }
    $finished = bsnl_people_finalize_alumni_candidate($candidate);
    if ($finished) {
        $candidates[] = $finished;
    }

    return $candidates;
}

function bsnl_people_scan_legacy_alumni_page(int $page_id): array
{
    $page = get_post($page_id);
    if (!($page instanceof WP_Post) || 'page' !== $page->post_type) {
        return ['candidates' => [], 'error' => __('The selected Alumni page could not be found.', 'bsnl-people')];
    }
    if (!class_exists('DOMDocument')) {
        return ['candidates' => [], 'error' => __('The PHP DOM extension is not available, so the Alumni page could not be scanned.', 'bsnl-people')];
    }

    $html = apply_filters('the_content', (string) $page->post_content);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    if (!$loaded) {
        return ['candidates' => [], 'error' => __('The Alumni page HTML could not be read.', 'bsnl-people')];
    }

    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query('//h2|//h3|//h4|//p|//blockquote|//strong|//img|//a');
    if (!$nodes) {
        return ['candidates' => [], 'error' => __('No readable Alumni content was found.', 'bsnl-people')];
    }

    $section = '';
    $candidate = null;
    $candidates = [];
    $board_photos = [];
    foreach ($nodes as $node) {
        if (!$node instanceof DOMElement) {
            continue;
        }
        $tag = strtolower($node->tagName);
        $text = trim(preg_replace('/\s+/u', ' ', (string) $node->textContent) ?: '');
        $lower = strtolower(remove_accents($text));

        if (in_array($tag, ['h2', 'h3', 'h4'], true)) {
            if ('board alumni' === $lower) {
                $finished = bsnl_people_finalize_alumni_candidate($candidate);
                if ($finished) {
                    $candidates[] = $finished;
                }
                $candidate = null;
                $section = 'board';
                continue;
            }
            if ('alumni' === $lower) {
                $finished = bsnl_people_finalize_alumni_candidate($candidate);
                if ($finished) {
                    $candidates[] = $finished;
                }
                $candidate = null;
                $section = 'alumni';
                continue;
            }
            if ('' !== $section && bsnl_people_looks_like_name($text)) {
                $finished = bsnl_people_finalize_alumni_candidate($candidate);
                if ($finished) {
                    $candidates[] = $finished;
                }
                $candidate = ['name' => $text, 'section' => $section, 'lines' => [], 'linkedin' => '', 'photo' => ''];
                continue;
            }
            if ('' !== $section && in_array($tag, ['h2', 'h3'], true)) {
                $finished = bsnl_people_finalize_alumni_candidate($candidate);
                if ($finished) {
                    $candidates[] = $finished;
                }
                $candidate = null;
                $section = '';
            }
        }

        if ('board' === $section && 'p' === $tag && $node->getElementsByTagName('strong')->length > 0) {
            $strong_node = $node->getElementsByTagName('strong')->item(0);
            $strong_name = $strong_node ? trim(preg_replace('/\s+/u', ' ', (string) $strong_node->textContent) ?: '') : '';
            if (bsnl_people_looks_like_name($strong_name)) {
                $finished = bsnl_people_finalize_alumni_candidate($candidate);
                if ($finished) {
                    $candidates[] = $finished;
                }
                $remaining = trim(str_replace($strong_name, '', $text));
                $candidate = ['name' => $strong_name, 'section' => $section, 'lines' => $remaining ? [$remaining] : [], 'linkedin' => '', 'photo' => ''];
                continue;
            }
        }

        if ('board' === $section && 'strong' === $tag && bsnl_people_looks_like_name($text)) {
            $same_person = $candidate && 0 === strcasecmp(bsnl_people_normalized_name((string) $candidate['name']), bsnl_people_normalized_name($text));
            $has_degree = 1 === preg_match('/\bph\.?d\.?\b/iu', $text);
            if (!$same_person && $has_degree) {
                $finished = bsnl_people_finalize_alumni_candidate($candidate);
                if ($finished) {
                    $candidates[] = $finished;
                }
                $candidate = ['name' => $text, 'section' => $section, 'lines' => [], 'linkedin' => '', 'photo' => ''];
                continue;
            }
        }

        if (!$candidate) {
            if ('board' === $section && 'img' === $tag) {
                $photo = trim((string) $node->getAttribute('src'));
                if ('' !== $photo) {
                    $board_photos[] = $photo;
                }
            }
            continue;
        }
        if ('img' === $tag && '' === $candidate['photo']) {
            $candidate['photo'] = (string) $node->getAttribute('src');
            if ('board' === $section && '' !== $candidate['photo']) {
                $board_photos[] = $candidate['photo'];
            }
        } elseif ('a' === $tag && '' === $candidate['linkedin'] && false !== stripos((string) $node->getAttribute('href'), 'linkedin.com')) {
            $candidate['linkedin'] = (string) $node->getAttribute('href');
        } elseif (in_array($tag, ['p', 'blockquote'], true)) {
            $candidate['lines'][] = $text;
        }
    }
    $finished = bsnl_people_finalize_alumni_candidate($candidate);
    if ($finished) {
        $candidates[] = $finished;
    }

    $board_candidates = array_values(array_filter($candidates, static function (array $item): bool {
        return 'board_alumni' === ($item['alumni_type'] ?? '');
    }));
    $fallback_board = bsnl_people_scan_board_alumni_text($html, array_values(array_unique($board_photos)));
    if (count($fallback_board) > count($board_candidates)) {
        $candidates = array_merge($candidates, $fallback_board);
    }

    $unique = [];
    foreach ($candidates as $item) {
        $key = strtolower(remove_accents((string) $item['name']));
        if (!isset($unique[$key])) {
            $unique[$key] = $item;
        }
    }

    return ['candidates' => array_values($unique), 'error' => ''];
}

function bsnl_people_alumni_preview_key(): string
{
    return 'bsnl_people_alumni_preview_' . get_current_user_id();
}

function bsnl_people_import_alumni_candidates(array $candidates): array
{
    $created = 0;
    $skipped = 0;
    $existing = bsnl_people_existing_names();
    foreach ($candidates as $candidate) {
        $name = bsnl_people_normalized_name((string) ($candidate['name'] ?? ''));
        $key = strtolower($name);
        if ('' === $name || isset($existing[$key])) {
            $skipped++;
            continue;
        }

        $person_id = wp_insert_post([
            'post_type' => BSNL_PEOPLE_POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $name,
        ], true);
        if (is_wp_error($person_id)) {
            $skipped++;
            continue;
        }

        update_post_meta($person_id, BSNL_PEOPLE_GROUP_META, 'alumni');
        $meta = [
            BSNL_PEOPLE_ALUMNI_TYPE_META => bsnl_people_normalize_alumni_type((string) ($candidate['alumni_type'] ?? 'member_alumni')),
            BSNL_PEOPLE_AFFILIATION_META => sanitize_text_field((string) ($candidate['affiliation'] ?? '')),
            BSNL_PEOPLE_YEARS_META => sanitize_text_field((string) ($candidate['years'] ?? '')),
            BSNL_PEOPLE_FORMER_ROLE_META => sanitize_text_field((string) ($candidate['former_role'] ?? '')),
            BSNL_PEOPLE_TESTIMONIAL_META => sanitize_textarea_field((string) ($candidate['testimonial'] ?? '')),
            BSNL_PEOPLE_LINKEDIN_META => esc_url_raw((string) ($candidate['linkedin'] ?? '')),
            BSNL_PEOPLE_LEGACY_PHOTO_META => esc_url_raw((string) ($candidate['photo'] ?? '')),
        ];
        foreach ($meta as $meta_key => $value) {
            if ('' !== $value) {
                update_post_meta($person_id, $meta_key, $value);
            }
        }
        $display_role = $meta[BSNL_PEOPLE_AFFILIATION_META] ?: $meta[BSNL_PEOPLE_FORMER_ROLE_META];
        if ($display_role) {
            update_post_meta($person_id, BSNL_PEOPLE_ROLE_META, $display_role);
        }
        $existing[$key] = true;
        $created++;
    }

    return ['created' => $created, 'skipped' => $skipped];
}

function bsnl_people_legacy_photo_ids(): array
{
    return get_posts([
        'post_type' => BSNL_PEOPLE_POST_TYPE,
        'post_status' => ['publish', 'draft', 'private'],
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => BSNL_PEOPLE_LEGACY_PHOTO_META,
                'compare' => 'EXISTS',
            ],
        ],
        'no_found_rows' => true,
    ]);
}

function bsnl_people_import_legacy_photos(): array
{
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $imported = 0;
    $skipped = 0;
    $failed = 0;
    foreach (bsnl_people_legacy_photo_ids() as $person_id) {
        $person_id = absint($person_id);
        if (!$person_id || has_post_thumbnail($person_id) || !current_user_can('edit_post', $person_id)) {
            $skipped++;
            continue;
        }
        $photo_url = esc_url_raw((string) get_post_meta($person_id, BSNL_PEOPLE_LEGACY_PHOTO_META, true));
        if ('' === $photo_url) {
            $skipped++;
            continue;
        }

        $attachment_id = media_sideload_image($photo_url, $person_id, get_the_title($person_id), 'id');
        if (is_wp_error($attachment_id)) {
            $failed++;
            continue;
        }
        set_post_thumbnail($person_id, (int) $attachment_id);
        $imported++;
    }

    return ['imported' => $imported, 'skipped' => $skipped, 'failed' => $failed];
}

function bsnl_people_import_page(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to import people.', 'bsnl-people'));
    }

    $result = null;
    $alumni_result = null;
    $photo_result = null;
    $scan_error = '';
    $preview = null;
    $selected_page_id = 0;
    if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '') && isset($_POST['bsnl_people_import'])) {
        check_admin_referer('bsnl_people_import');
        $result = bsnl_people_import_existing();
    } elseif ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '') && isset($_POST['bsnl_people_scan_alumni'])) {
        check_admin_referer('bsnl_people_scan_alumni');
        $selected_page_id = absint($_POST['bsnl_people_alumni_page'] ?? 0);
        $scan = bsnl_people_scan_legacy_alumni_page($selected_page_id);
        $scan_error = (string) $scan['error'];
        if ('' === $scan_error) {
            $preview = [
                'page_id' => $selected_page_id,
                'candidates' => $scan['candidates'],
            ];
            set_transient(bsnl_people_alumni_preview_key(), $preview, HOUR_IN_SECONDS);
        }
    } elseif ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '') && isset($_POST['bsnl_people_confirm_alumni'])) {
        check_admin_referer('bsnl_people_confirm_alumni');
        $saved_preview = get_transient(bsnl_people_alumni_preview_key());
        if (!is_array($saved_preview) || !isset($saved_preview['candidates']) || !is_array($saved_preview['candidates'])) {
            $scan_error = __('The migration preview expired. Scan the Alumni page again.', 'bsnl-people');
        } else {
            $alumni_result = bsnl_people_import_alumni_candidates($saved_preview['candidates']);
            delete_transient(bsnl_people_alumni_preview_key());
        }
    } elseif ('POST' === ($_SERVER['REQUEST_METHOD'] ?? '') && isset($_POST['bsnl_people_import_photos'])) {
        check_admin_referer('bsnl_people_import_photos');
        $photo_result = bsnl_people_import_legacy_photos();
    }

    $pages = get_pages(['post_status' => ['publish', 'draft', 'private'], 'number' => 0, 'sort_column' => 'post_title']);
    if (!$selected_page_id) {
        foreach ($pages as $candidate_page) {
            if ($candidate_page instanceof WP_Post && false !== stripos($candidate_page->post_title . ' ' . $candidate_page->post_name, 'alumni')) {
                $selected_page_id = (int) $candidate_page->ID;
                break;
            }
        }
    }
    ?>
    <div class="wrap">
      <h1><?php esc_html_e('Import existing people', 'bsnl-people'); ?></h1>
      <p><?php esc_html_e('Import tools create editable People records. They never delete or rewrite page content.', 'bsnl-people'); ?></p>
      <h2><?php esc_html_e('Current team cards', 'bsnl-people'); ?></h2>
      <p><?php esc_html_e('Use this once to read current BSNL team cards that use the new website layout.', 'bsnl-people'); ?></p>
      <?php if (is_array($result)) : ?>
        <?php if ($result['error']) : ?>
          <div class="notice notice-error"><p><?php echo esc_html($result['error']); ?></p></div>
        <?php else : ?>
          <div class="notice notice-success"><p><?php echo esc_html(sprintf(__('%1$d people imported; %2$d existing or unreadable entries skipped.', 'bsnl-people'), $result['created'], $result['skipped'])); ?></p></div>
        <?php endif; ?>
      <?php endif; ?>
      <form method="post">
        <?php wp_nonce_field('bsnl_people_import'); ?>
        <input type="hidden" name="bsnl_people_import" value="1">
        <?php submit_button(__('Import existing people', 'bsnl-people')); ?>
      </form>
      <hr>
      <h2><?php esc_html_e('Legacy Alumni page', 'bsnl-people'); ?></h2>
      <p><?php esc_html_e('First scan the old Alumni page. Review every detected person and field before confirming the import. Existing People with the same name are skipped.', 'bsnl-people'); ?></p>
      <?php if ($scan_error) : ?>
        <div class="notice notice-error inline"><p><?php echo esc_html($scan_error); ?></p></div>
      <?php endif; ?>
      <?php if (is_array($alumni_result)) : ?>
        <div class="notice notice-success inline"><p><?php echo esc_html(sprintf(__('%1$d alumni imported; %2$d existing or unreadable entries skipped. The public Alumni page was not changed.', 'bsnl-people'), $alumni_result['created'], $alumni_result['skipped'])); ?></p></div>
      <?php endif; ?>
      <form method="post">
        <?php wp_nonce_field('bsnl_people_scan_alumni'); ?>
        <input type="hidden" name="bsnl_people_scan_alumni" value="1">
        <label for="bsnl-people-alumni-page"><strong><?php esc_html_e('Source page', 'bsnl-people'); ?></strong></label>
        <select id="bsnl-people-alumni-page" name="bsnl_people_alumni_page">
          <?php foreach ($pages as $source_page) : ?>
            <?php if ($source_page instanceof WP_Post) : ?>
              <option value="<?php echo esc_attr((string) $source_page->ID); ?>" <?php selected($selected_page_id, (int) $source_page->ID); ?>><?php echo esc_html($source_page->post_title ?: __('Untitled page', 'bsnl-people')); ?></option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
        <?php submit_button(__('Scan and preview Alumni', 'bsnl-people'), 'secondary', 'submit', false); ?>
      </form>
      <?php if (is_array($preview)) : ?>
        <?php $existing_names = bsnl_people_existing_names(); ?>
        <h3><?php echo esc_html(sprintf(__('Migration preview: %d detected people', 'bsnl-people'), count($preview['candidates']))); ?></h3>
        <table class="widefat striped">
          <thead><tr>
            <th><?php esc_html_e('Name', 'bsnl-people'); ?></th>
            <th><?php esc_html_e('Legacy section', 'bsnl-people'); ?></th>
            <th><?php esc_html_e('Current affiliation', 'bsnl-people'); ?></th>
            <th><?php esc_html_e('BSNL years', 'bsnl-people'); ?></th>
            <th><?php esc_html_e('Former role', 'bsnl-people'); ?></th>
            <th><?php esc_html_e('Status', 'bsnl-people'); ?></th>
          </tr></thead>
          <tbody>
            <?php foreach ($preview['candidates'] as $person) : ?>
              <?php $already_exists = isset($existing_names[strtolower((string) $person['name'])]); ?>
              <tr>
                <td><strong><?php echo esc_html((string) $person['name']); ?></strong></td>
                <td><?php echo esc_html('board_alumni' === $person['alumni_type'] ? __('Board Alumni', 'bsnl-people') : __('Member Alumni', 'bsnl-people')); ?></td>
                <td><?php echo esc_html((string) $person['affiliation']); ?></td>
                <td><?php echo esc_html((string) $person['years']); ?></td>
                <td><?php echo esc_html((string) $person['former_role']); ?></td>
                <td><?php echo esc_html($already_exists ? __('Already in People - will skip', 'bsnl-people') : __('Ready to import', 'bsnl-people')); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php if ($preview['candidates']) : ?>
          <form method="post">
            <?php wp_nonce_field('bsnl_people_confirm_alumni'); ?>
            <input type="hidden" name="bsnl_people_confirm_alumni" value="1">
            <?php submit_button(__('Confirm Alumni import', 'bsnl-people')); ?>
          </form>
        <?php endif; ?>
      <?php endif; ?>
      <hr>
      <h2><?php esc_html_e('Legacy People photos', 'bsnl-people'); ?></h2>
      <p><?php esc_html_e('Copy saved legacy photo URLs into the WordPress Media Library and set them as Featured images. People who already have a Featured image are skipped.', 'bsnl-people'); ?></p>
      <?php if (is_array($photo_result)) : ?>
        <div class="notice notice-success inline"><p><?php echo esc_html(sprintf(__('%1$d photos imported, %2$d skipped, %3$d failed.', 'bsnl-people'), $photo_result['imported'], $photo_result['skipped'], $photo_result['failed'])); ?></p></div>
      <?php endif; ?>
      <p><?php echo esc_html(sprintf(__('%d People records currently have a saved legacy photo URL.', 'bsnl-people'), count(bsnl_people_legacy_photo_ids()))); ?></p>
      <form method="post">
        <?php wp_nonce_field('bsnl_people_import_photos'); ?>
        <input type="hidden" name="bsnl_people_import_photos" value="1">
        <?php submit_button(__('Import legacy photos into Media Library', 'bsnl-people'), 'secondary'); ?>
      </form>
      <h2><?php esc_html_e('After reviewing the imported records', 'bsnl-people'); ?></h2>
      <p><?php esc_html_e('Replace each old static grid with the matching shortcode. Keep the existing section heading.', 'bsnl-people'); ?></p>
      <pre>[bsnl_people group="board"]
[bsnl_people group="members"]
[bsnl_people group="alumni"]</pre>
      <p><strong><?php esc_html_e('The migration does not require this replacement. Keep the current Alumni page unchanged until you choose to redesign or activate the People output.', 'bsnl-people'); ?></strong></p>
    </div>
    <?php
}
