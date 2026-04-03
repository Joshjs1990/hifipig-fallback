<?php
/**
 * HiFiPigTheme (TheCoolMoon) - functions
 * Template-level performance pass (v2)
 */

defined('ABSPATH') || exit;

/** Theme version for cache-busting */
function hifipig_theme_version() {
  $style_file = get_stylesheet_directory() . '/style.css';
  if (file_exists($style_file)) {
    return (string) filemtime($style_file);
  }

  $theme = wp_get_theme();
  return $theme && $theme->exists() ? $theme->get('Version') : '1.0';
}

/** -------------------------------------------------------
 * Theme supports + menus
 * ------------------------------------------------------ */
add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('align-wide');
  add_theme_support('custom-logo', [
    'height'      => 60,
    'width'       => 220,
    'flex-height' => true,
    'flex-width'  => true,
  ]);
  add_theme_support('html5', [
    'search-form','comment-form','comment-list','gallery','caption','style','script'
  ]);

  register_nav_menus([
    'primary' => 'Primary Menu',
  ]);
});

/** -------------------------------------------------------
 * Admin bar and cache safety
 * ------------------------------------------------------ */
add_filter('show_admin_bar', function ($show) {
  return is_user_logged_in() ? $show : false;
}, 100);

add_action('init', function () {
  if (is_admin() || !is_user_logged_in()) return;

  // Prevent logged-in HTML (including admin bar) from being cached and leaked.
  if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
  if (!defined('DONOTCACHEDB')) define('DONOTCACHEDB', true);
  if (!defined('DONOTMINIFY')) define('DONOTMINIFY', true);
}, 0);

/** -------------------------------------------------------
 * Post card ad image + URL (secondary featured image)
 * ------------------------------------------------------ */
add_action('add_meta_boxes', function () {
  add_meta_box(
    'hifipig_card_ad',
    'Post Card Media',
    'hifipig_render_card_ad_metabox',
    'post',
    'side',
    'default'
  );
});

function hifipig_get_youtube_video_id($value) {
  $value = trim((string) $value);
  if ($value === '') {
    return '';
  }

  if (preg_match('/^[A-Za-z0-9_-]{11}$/', $value)) {
    return $value;
  }

  $parts = wp_parse_url($value);
  if (!is_array($parts) || empty($parts['host'])) {
    return '';
  }

  $host = strtolower((string) $parts['host']);
  $path = isset($parts['path']) ? trim((string) $parts['path'], '/') : '';
  $query = [];
  if (!empty($parts['query'])) {
    parse_str((string) $parts['query'], $query);
  }

  $candidate = '';

  if ($host === 'youtu.be' || $host === 'www.youtu.be') {
    $segments = explode('/', $path);
    $candidate = isset($segments[0]) ? (string) $segments[0] : '';
  } elseif (preg_match('/(^|\\.)youtube\\.com$/', $host) || preg_match('/(^|\\.)youtube-nocookie\\.com$/', $host)) {
    if (!empty($query['v'])) {
      $candidate = (string) $query['v'];
    } elseif (preg_match('#^(?:embed|shorts|live)/([^/?#]+)#', $path, $match)) {
      $candidate = (string) $match[1];
    }
  }

  if ($candidate && preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate)) {
    return $candidate;
  }

  return '';
}

function hifipig_get_youtube_embed_url($value) {
  $video_id = hifipig_get_youtube_video_id($value);
  if ($video_id === '') {
    return '';
  }

  return add_query_arg(
    [
      'rel' => '0',
      'playsinline' => '1',
    ],
    'https://www.youtube-nocookie.com/embed/' . rawurlencode($video_id)
  );
}

function hifipig_render_card_ad_metabox($post) {
  wp_nonce_field('hifipig_card_ad_save', 'hifipig_card_ad_nonce');

  $ad_id = (int) get_post_meta($post->ID, '_hifipig_card_ad_image_id', true);
  $ad_url = (string) get_post_meta($post->ID, '_hifipig_card_ad_url', true);
  $youtube_url = (string) get_post_meta($post->ID, '_hifipig_card_youtube_url', true);
  $img = $ad_id ? wp_get_attachment_image($ad_id, 'thumbnail', false, ['class' => 'hifipig-card-ad-preview']) : '';
  ?>
  <div class="hifipig-card-ad">
    <p>
      <label for="hifipig_card_youtube_url"><strong>Card video (YouTube URL, optional)</strong></label>
      <input type="url" class="widefat" name="hifipig_card_youtube_url" id="hifipig_card_youtube_url" value="<?php echo esc_attr($youtube_url); ?>" placeholder="https://www.youtube.com/watch?v=...">
      <small>Replaces the featured image with a playable video on home/archive cards.</small>
    </p>
    <div class="hifipig-card-ad__preview">
      <?php echo $img ?: '<div class="hifipig-card-ad__placeholder">No image selected</div>'; ?>
    </div>
    <input type="hidden" name="hifipig_card_ad_image_id" id="hifipig_card_ad_image_id" value="<?php echo esc_attr($ad_id); ?>">
    <p>
      <label for="hifipig_card_ad_url"><strong>Link URL (optional)</strong></label>
      <input type="url" class="widefat" name="hifipig_card_ad_url" id="hifipig_card_ad_url" value="<?php echo esc_attr($ad_url); ?>" placeholder="https://">
    </p>
    <p>
      <button type="button" class="button" id="hifipig_card_ad_select">
        <?php echo $ad_id ? 'Change image' : 'Select image'; ?>
      </button>
      <button type="button" class="button-link-delete" id="hifipig_card_ad_clear"<?php echo $ad_id ? '' : ' style="display:none;"'; ?>>Remove</button>
    </p>
  </div>
  <?php
}

add_action('save_post', function ($post_id) {
  if (!isset($_POST['hifipig_card_ad_nonce']) || !wp_verify_nonce($_POST['hifipig_card_ad_nonce'], 'hifipig_card_ad_save')) {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $ad_id = isset($_POST['hifipig_card_ad_image_id']) ? absint($_POST['hifipig_card_ad_image_id']) : 0;
  if ($ad_id) update_post_meta($post_id, '_hifipig_card_ad_image_id', $ad_id);
  else delete_post_meta($post_id, '_hifipig_card_ad_image_id');

  $ad_url = isset($_POST['hifipig_card_ad_url']) ? esc_url_raw(wp_unslash($_POST['hifipig_card_ad_url'])) : '';
  if ($ad_url) update_post_meta($post_id, '_hifipig_card_ad_url', $ad_url);
  else delete_post_meta($post_id, '_hifipig_card_ad_url');

  $youtube_url = isset($_POST['hifipig_card_youtube_url']) ? esc_url_raw(wp_unslash($_POST['hifipig_card_youtube_url'])) : '';
  if ($youtube_url && hifipig_get_youtube_embed_url($youtube_url)) {
    update_post_meta($post_id, '_hifipig_card_youtube_url', $youtube_url);
  } else {
    delete_post_meta($post_id, '_hifipig_card_youtube_url');
  }
});

add_action('admin_enqueue_scripts', function ($hook) {
  if (!in_array($hook, ['post.php', 'post-new.php'], true)) return;
  $screen = get_current_screen();
  if (!$screen || $screen->post_type !== 'post') return;

  wp_enqueue_media();
  wp_enqueue_script(
    'hifipig-card-ad-admin',
    get_template_directory_uri() . '/assets/admin-card-ad.js',
    ['jquery'],
    hifipig_theme_version(),
    true
  );
});

/** -------------------------------------------------------
 * Category image (term meta)
 * ------------------------------------------------------ */
add_action('category_add_form_fields', function () {
  wp_nonce_field('hifipig_category_image_save', 'hifipig_category_image_nonce');
  ?>
  <div class="form-field hifipig-category-image">
    <label for="hifipig_category_image_id">Category image</label>
    <div class="hifipig-category-image__preview" data-category-image-preview>
      <div class="hifipig-category-image__placeholder">No image selected</div>
    </div>
    <input type="hidden" id="hifipig_category_image_id" name="hifipig_category_image_id" value="" data-category-image-id>
    <p>
      <button type="button" class="button" data-category-image-select>Select image</button>
      <button type="button" class="button-link" data-category-image-remove style="display:none;">Remove</button>
    </p>
  </div>
  <div class="form-field hifipig-category-image-url">
    <label for="hifipig_category_image_url">Category image URL (optional)</label>
    <input type="url" id="hifipig_category_image_url" name="hifipig_category_image_url" value="" placeholder="https://">
  </div>
  <div class="form-field hifipig-category-sponsor">
    <label for="hifipig_category_sponsor_override">
      <input type="checkbox" id="hifipig_category_sponsor_override" name="hifipig_category_sponsor_override" value="1">
      Sponsor override (use category image as post card secondary image when this is the only category)
    </label>
  </div>
  <?php
});

add_action('category_edit_form_fields', function ($term) {
  wp_nonce_field('hifipig_category_image_save', 'hifipig_category_image_nonce');
  $image_id = (int) get_term_meta($term->term_id, '_hifipig_category_image_id', true);
  $image_url = (string) get_term_meta($term->term_id, '_hifipig_category_image_url', true);
  $override = (int) get_term_meta($term->term_id, '_hifipig_category_sponsor_override', true);
  $image = $image_id ? wp_get_attachment_image($image_id, 'thumbnail', false, ['class' => 'hifipig-category-image__img']) : '';
  ?>
  <tr class="form-field hifipig-category-image">
    <th scope="row"><label for="hifipig_category_image_id">Category image</label></th>
    <td>
      <div class="hifipig-category-image__preview" data-category-image-preview>
        <?php echo $image ?: '<div class="hifipig-category-image__placeholder">No image selected</div>'; ?>
      </div>
      <input type="hidden" id="hifipig_category_image_id" name="hifipig_category_image_id" value="<?php echo esc_attr($image_id); ?>" data-category-image-id>
      <p>
        <button type="button" class="button" data-category-image-select><?php echo $image_id ? 'Change image' : 'Select image'; ?></button>
        <button type="button" class="button-link" data-category-image-remove<?php echo $image_id ? '' : ' style="display:none;"'; ?>>Remove</button>
      </p>
    </td>
  </tr>
  <tr class="form-field hifipig-category-image-url">
    <th scope="row"><label for="hifipig_category_image_url">Category image URL (optional)</label></th>
    <td>
      <input type="url" id="hifipig_category_image_url" name="hifipig_category_image_url" value="<?php echo esc_attr($image_url); ?>" class="regular-text" placeholder="https://">
    </td>
  </tr>
  <tr class="form-field hifipig-category-sponsor">
    <th scope="row">Sponsor override</th>
    <td>
      <label for="hifipig_category_sponsor_override">
        <input type="checkbox" id="hifipig_category_sponsor_override" name="hifipig_category_sponsor_override" value="1" <?php checked(1, $override); ?>>
        Use category image as post card secondary image when this is the only category
      </label>
    </td>
  </tr>
  <?php
});

add_action('created_category', 'hifipig_save_category_image');
add_action('edited_category', 'hifipig_save_category_image');

function hifipig_save_category_image($term_id) {
  if (!isset($_POST['hifipig_category_image_nonce']) || !wp_verify_nonce($_POST['hifipig_category_image_nonce'], 'hifipig_category_image_save')) {
    return;
  }
  if (!current_user_can('manage_categories')) return;

  $image_id = isset($_POST['hifipig_category_image_id']) ? absint($_POST['hifipig_category_image_id']) : 0;
  if ($image_id) {
    update_term_meta($term_id, '_hifipig_category_image_id', $image_id);
  } else {
    delete_term_meta($term_id, '_hifipig_category_image_id');
  }

  $image_url = isset($_POST['hifipig_category_image_url']) ? esc_url_raw(wp_unslash($_POST['hifipig_category_image_url'])) : '';
  if ($image_url) {
    update_term_meta($term_id, '_hifipig_category_image_url', $image_url);
  } else {
    delete_term_meta($term_id, '_hifipig_category_image_url');
  }

  $override = isset($_POST['hifipig_category_sponsor_override']) ? 1 : 0;
  if ($override) {
    update_term_meta($term_id, '_hifipig_category_sponsor_override', 1);
  } else {
    delete_term_meta($term_id, '_hifipig_category_sponsor_override');
  }
}

add_action('admin_enqueue_scripts', function ($hook) {
  if (!in_array($hook, ['edit-tags.php', 'term.php'], true)) return;
  $screen = get_current_screen();
  if (!$screen || $screen->taxonomy !== 'category') return;

  wp_enqueue_media();
  wp_enqueue_script(
    'hifipig-category-image-admin',
    get_template_directory_uri() . '/assets/admin-category-image.js',
    ['jquery'],
    hifipig_theme_version(),
    true
  );
});

/** -------------------------------------------------------
 * Sidebar
 * ------------------------------------------------------ */
add_action('widgets_init', function () {
  register_sidebar([
    'name'          => 'Primary Sidebar',
    'id'            => 'primary-sidebar',
    'before_widget' => '<section class="widget %2$s" id="%1$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3 class="widget-title">',
    'after_title'   => '</h3>',
  ]);
});

/** -------------------------------------------------------
 * Assets (load only what we need)
 * ------------------------------------------------------ */
add_action('wp_enqueue_scripts', function () {
  $ver = hifipig_theme_version();

  // Main stylesheet
  wp_enqueue_style('hifipig-style', get_stylesheet_uri(), [], $ver);

  // Navigation (priority + mobile panel)
  if (has_nav_menu('primary')) {
    wp_enqueue_script(
      'hifipig-nav',
      get_template_directory_uri() . '/assets/priority-nav.js',
      [],
      $ver,
      true
    );
  }

  // Header AJAX search + cookie notice
  wp_enqueue_script(
    'hifipig-header',
    get_template_directory_uri() . '/assets/header.js',
    [],
    $ver,
    true
  );

  wp_localize_script('hifipig-header', 'HiFiPig', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('hifipig_search'),
  ]);

}, 20);

/** Add defer to non-critical theme scripts */
add_filter('script_loader_tag', function ($tag, $handle) {
  if (in_array($handle, ['hifipig-nav','hifipig-header'], true)) {
    if (false === strpos($tag, ' defer')) {
      $tag = str_replace(' src', ' defer src', $tag);
    }
  }
  return $tag;
}, 10, 2);

/**
 * Defer non-critical plugin styles to reduce render blocking.
 * Keep theme stylesheet blocking for above-the-fold.
 */
add_filter('style_loader_tag', function ($html, $handle, $href, $_media) {
  if (is_admin()) return $html;
  if ($handle === 'hifipig-style') return $html;

  $defer = false;
  $candidates = [
    'frontend.css',
    'flick.css',
    '/css/styles.css',
    'all-in-one-seo',
    'aioseo',
    'wordfence',
    'contact-form-7',
  ];

  foreach ($candidates as $needle) {
    if (strpos($href, $needle) !== false) {
      $defer = true;
      break;
    }
  }

  if (!$defer) return $html;

  if (preg_match('/media=([\'"]).*?\\1/', $html)) {
    $html = preg_replace(
      '/media=([\'"]).*?\\1/',
      "media='print' onload=\"this.media='all'\"",
      $html,
      1
    );
  } else {
    $html = str_replace(
      "rel='stylesheet'",
      "rel='stylesheet' media='print' onload=\"this.media='all'\"",
      $html
    );
  }

  if (strpos($html, '<noscript>') === false) {
    $html .= "<noscript><link rel='stylesheet' href='{$href}'></noscript>";
  }

  return $html;
}, 10, 4);

/** -------------------------------------------------------
 * Resource hints for third-party origins
 * ------------------------------------------------------ */
add_filter('wp_resource_hints', function ($hints, $relation_type) {
  if (is_admin()) return $hints;

  $origins = [
    'https://pagead2.googlesyndication.com',
    'https://www.googletagmanager.com',
    'https://www.google-analytics.com',
    'https://web.webpushs.com',
    'https://ep1.adtrafficquality.google',
    'https://ep2.adtrafficquality.google',
  ];

  if ($relation_type === 'preconnect' || $relation_type === 'dns-prefetch') {
    $hints = array_merge($hints, $origins);
    $hints = array_values(array_unique($hints));
  }

  return $hints;
}, 10, 2);

// Archive pages: show 28 posts per page.
add_action('pre_get_posts', function ($query) {
  if (is_admin() || !$query->is_main_query()) return;
  if ($query->is_archive() || $query->is_home() || $query->is_search()) {
    $query->set('posts_per_page', 28);
  }
});

// Drop jquery-migrate on the front end for faster startup.
add_action('wp_default_scripts', function ($scripts) {
  if (is_admin()) return;
  if (!isset($scripts->registered['jquery'])) return;
  $scripts->registered['jquery']->deps = array_diff(
    $scripts->registered['jquery']->deps,
    ['jquery-migrate']
  );
});

// Remove "Category:" prefix from archive titles.
add_filter('get_the_archive_title', function ($title) {
  if (is_category()) {
    $title = single_cat_title('', false);
  }
  return $title;
});

/** -------------------------------------------------------
 * Template fragment caching (guests only)
 * ------------------------------------------------------ */
function hifipig_get_fragment($key) {
  return is_user_logged_in() ? false : get_transient($key);
}
function hifipig_set_fragment($key, $html, $ttl = 300) {
  if (is_user_logged_in()) return;
  set_transient($key, $html, $ttl);
}
function hifipig_delete_fragments_for_post($post_id) {
  delete_transient('hifipig_postnav_' . (int) $post_id);
  delete_transient('hifipig_postmore_' . (int) $post_id);
  hifipig_flush_sidebar_cache();
}
add_action('save_post', 'hifipig_delete_fragments_for_post', 10, 1);
add_action('deleted_post', 'hifipig_delete_fragments_for_post', 10, 1);

function hifipig_count_words($text) {
  $text = wp_strip_all_tags(strip_shortcodes((string) $text), true);
  $text = trim(preg_replace('/\s+/u', ' ', $text));
  if ($text === '') {
    return 0;
  }

  $words = preg_split('/[\p{Z}\s]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
  return is_array($words) ? count($words) : 0;
}

function hifipig_get_sidebar_cache_key() {
  $version = (int) get_option('hifipig_sidebar_cache_version', 1);
  $uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '';
  if ($uri === '') {
    $uri = 'global';
  }

  return 'hifipig_sidebar_render_v3_' . $version . '_' . md5($uri);
}

function hifipig_parse_adrotate_group_ids($value) {
  if (is_array($value)) {
    $value = implode(',', $value);
  }

  $parts = preg_split('/[\s,]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
  if (!is_array($parts) || !$parts) {
    return [];
  }

  $ids = [];

  foreach ($parts as $part) {
    $id = absint($part);
    if ($id > 0) {
      $ids[] = $id;
    }
  }

  return array_values(array_unique($ids));
}

function hifipig_sanitize_adrotate_group_ids($value) {
  $ids = hifipig_parse_adrotate_group_ids($value);
  return $ids ? implode(',', $ids) : '';
}

function hifipig_get_sidebar_adrotate_group_ids() {
  return hifipig_parse_adrotate_group_ids(get_theme_mod('hifipig_adrotate_sidebar_group_id', ''));
}

function hifipig_has_sidebar_adrotate_groups_configured() {
  return !empty(hifipig_get_sidebar_adrotate_group_ids());
}

function hifipig_should_use_dynamic_sidebar_ads() {
  if (!(is_singular('post') || is_home() || is_archive() || is_search())) {
    return false;
  }

  return hifipig_has_sidebar_adrotate_groups_configured();
}

function hifipig_is_replaced_sidebar_ad_widget($instance, $widget) {
  $class = strtolower(is_object($widget) ? get_class($widget) : '');
  $id_base = strtolower((string) (is_object($widget) && isset($widget->id_base) ? $widget->id_base : ''));
  $widget_id = strtolower((string) (is_object($widget) && isset($widget->id) ? $widget->id : ''));

  foreach ([$class, $id_base, $widget_id] as $value) {
    if ($value !== '' && strpos($value, 'adrotate') !== false) {
      return true;
    }
  }

  $serialized = strtolower(wp_json_encode($instance));
  if ($serialized !== '' && strpos($serialized, '[adrotate') !== false) {
    return true;
  }

  return false;
}

add_filter('widget_display_callback', function ($instance, $widget, $args) {
  if (is_admin() || !hifipig_should_use_dynamic_sidebar_ads()) {
    return $instance;
  }

  $sidebar_id = is_array($args) && isset($args['id']) ? (string) $args['id'] : '';
  if ($sidebar_id !== 'primary-sidebar') {
    return $instance;
  }

  if (hifipig_is_replaced_sidebar_ad_widget($instance, $widget)) {
    return false;
  }

  return $instance;
}, 10, 3);

function hifipig_get_dynamic_sidebar_ad_slot_capacity() {
  if (is_singular('post')) {
    return 18;
  }

  if (is_home() || is_archive() || is_search()) {
    return 14;
  }

  return 0;
}

function hifipig_render_dynamic_sidebar_ad_slot($index) {
  $group_ids = hifipig_get_sidebar_adrotate_group_ids();
  if (!$group_ids || !function_exists('shortcode_exists') || !shortcode_exists('adrotate')) {
    return '';
  }

  $index = max(0, (int) $index);
  $group_id = $group_ids[$index % count($group_ids)];
  $shortcode = '[adrotate group="' . $group_id . '"]';
  $slot_html = trim((string) do_shortcode($shortcode));
  if ($slot_html === '' || $slot_html === $shortcode) {
    return '';
  }

  return '<div class="site-sidebar__ad-slot" aria-label="Sponsored" data-adrotate-group="' . esc_attr((string) $group_id) . '" data-slot-index="' . esc_attr((string) $index) . '">' . $slot_html . '</div>';
}

function hifipig_get_dynamic_sidebar_ads_html() {
  if (!hifipig_should_use_dynamic_sidebar_ads()) {
    return '';
  }

  $slot_capacity = hifipig_get_dynamic_sidebar_ad_slot_capacity();
  if ($slot_capacity < 1) {
    return '';
  }

  return '<div class="site-sidebar__dynamic-ads" data-dynamic-sidebar-slots data-slot-capacity="' . esc_attr((string) $slot_capacity) . '" data-next-index="0" aria-live="polite"></div>';
}

function hifipig_ajax_sidebar_ad_slot() {
  $index = isset($_GET['index']) ? absint(wp_unslash($_GET['index'])) : 0;
  $html = hifipig_render_dynamic_sidebar_ad_slot($index);

  if ($html === '') {
    wp_send_json_success(['html' => '']);
  }

  wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_hifipig_sidebar_ad_slot', 'hifipig_ajax_sidebar_ad_slot');
add_action('wp_ajax_nopriv_hifipig_sidebar_ad_slot', 'hifipig_ajax_sidebar_ad_slot');

/** -------------------------------------------------------
 * AJAX search (fast, public post types)
 * ------------------------------------------------------ */
add_action('wp_ajax_hifipig_search', 'hifipig_ajax_search');
add_action('wp_ajax_nopriv_hifipig_search', 'hifipig_ajax_search');

/** -------------------------------------------------------
 * Front-end perf trims (safe defaults)
 * ------------------------------------------------------ */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
add_filter('emoji_svg_url', '__return_false');

add_action('wp_enqueue_scripts', function () {
  if (is_admin()) return;
  // Drop wp-embed; not needed for front-end rendering.
  wp_deregister_script('wp-embed');
  // Remove dashicons for guests.
  if (!is_user_logged_in()) {
    wp_deregister_style('dashicons');
  }
}, 100);

/** -------------------------------------------------------
 * Aggressive plugin asset gating (whitelist only)
 * ------------------------------------------------------ */
function hifipig_is_plugin_assets_whitelisted() {
  if (!is_page()) return false;

  $defaults = [
    'contact',
    'contact-us',
    'subscribe',
    'subscription',
    'newsletter',
  ];

  $whitelist = apply_filters('hifipig_plugin_asset_whitelist', $defaults);
  if (!is_array($whitelist) || !$whitelist) return false;

  $page_id = get_queried_object_id();
  foreach ($whitelist as $item) {
    if (is_int($item) && $page_id === $item) return true;
    if (is_string($item) && $item !== '' && is_page($item)) return true;
  }

  return false;
}

add_action('wp_enqueue_scripts', function () {
  if (is_admin()) return;
  if (!apply_filters('hifipig_enable_aggressive_asset_gating', false)) return;
  if (hifipig_is_plugin_assets_whitelisted()) return;
  $use_adrotate_assets = (int) get_theme_mod('hifipig_adrotate_top_group_id', 0) > 0 || hifipig_has_sidebar_adrotate_groups_configured();

  // Known handles (best-effort).
  $script_handles = [
    'contact-form-7',
    'swv',
    'mailchimp_sf_main_js',
    'adrotate-groups',
    'aioseo/js/src/vue/standalone/blocks/table-of-contents/frontend.js',
    'jquery-ui-datepicker',
    'jquery-ui-core',
    'jquery-form',
  ];

  if ($use_adrotate_assets) {
    $script_handles = array_values(array_diff($script_handles, ['adrotate-groups']));
  }

  $style_handles = [
    'contact-form-7',
    'mailchimp_sf_main_css',
    'mailchimp_sf_main_css-css',
    'flick-css',
  ];

  foreach ($script_handles as $handle) {
    wp_dequeue_script($handle);
  }
  foreach ($style_handles as $handle) {
    wp_dequeue_style($handle);
  }

  // Fallback: block by plugin source path.
  $script_src_blocklist = [
    '/plugins/contact-form-7/',
    '/plugins/mailchimp/',
    '/plugins/adrotate-pro/',
    '/plugins/all-in-one-seo-pack-pro/',
  ];

  if ($use_adrotate_assets) {
    $script_src_blocklist = array_values(array_diff($script_src_blocklist, ['/plugins/adrotate-pro/']));
  }

  $style_src_blocklist = $script_src_blocklist;

  $scripts = wp_scripts();
  if ($scripts && is_array($scripts->queue)) {
    foreach ($scripts->queue as $handle) {
      if (!isset($scripts->registered[$handle])) continue;
      $src = (string) $scripts->registered[$handle]->src;
      if ($src === '') continue;
      foreach ($script_src_blocklist as $needle) {
        if (strpos($src, $needle) !== false) {
          wp_dequeue_script($handle);
          break;
        }
      }
    }
  }

  $styles = wp_styles();
  if ($styles && is_array($styles->queue)) {
    foreach ($styles->queue as $handle) {
      if (!isset($styles->registered[$handle])) continue;
      $src = (string) $styles->registered[$handle]->src;
      if ($src === '') continue;
      foreach ($style_src_blocklist as $needle) {
        if (strpos($src, $needle) !== false) {
          wp_dequeue_style($handle);
          break;
        }
      }
    }
  }
}, 1000);

/**
 * Dequeue block styles on singular pages that do not contain blocks
 * and do not use block widgets (safe default).
 */
function hifipig_has_block_widgets() {
  if (!function_exists('wp_get_sidebars_widgets')) return false;
  $sidebars = wp_get_sidebars_widgets();
  if (!is_array($sidebars)) return false;

  foreach ($sidebars as $sidebar_id => $widget_ids) {
    if ($sidebar_id === 'wp_inactive_widgets') continue;
    if (!is_array($widget_ids)) continue;
    foreach ($widget_ids as $widget_id) {
      if (strpos($widget_id, 'block-') === 0) {
        return true;
      }
    }
  }

  return false;
}

add_action('wp_enqueue_scripts', function () {
  if (is_admin()) return;
  if (!function_exists('has_blocks')) return;
  if (apply_filters('hifipig_keep_block_styles', false)) return;

  if (hifipig_has_block_widgets()) return;

  if (is_singular()) {
    $post = get_post();
    $has_blocks = $post ? has_blocks($post->post_content) : false;
    if ($has_blocks) return;
  }

  wp_dequeue_style('wp-block-library');
  wp_dequeue_style('wp-block-library-theme');
  wp_dequeue_style('global-styles');
  wp_dequeue_style('classic-theme-styles');
}, 100);

/**
 * Image attribute defaults: async decoding + eager logo.
 */
add_filter('wp_get_attachment_image_attributes', function ($attrs, $attachment, $size) {
  if (empty($attrs['decoding'])) {
    $attrs['decoding'] = 'async';
  }

  if (!empty($attrs['class']) && strpos($attrs['class'], 'custom-logo') !== false) {
    $attrs['loading'] = 'eager';
    if (empty($attrs['fetchpriority'])) {
      $attrs['fetchpriority'] = 'high';
    }
  }

  return $attrs;
}, 10, 3);

/** -------------------------------------------------------
 * Content image defaults (LCP-friendly first image)
 * ------------------------------------------------------ */
function hifipig_set_img_attr($tag, $attr, $value) {
  $pattern = '/\\s' . preg_quote($attr, '/') . '=(\"|\').*?\\1/i';
  if (preg_match($pattern, $tag)) {
    return preg_replace($pattern, ' ' . $attr . '="' . esc_attr($value) . '"', $tag, 1);
  }
  return str_replace('<img ', '<img ' . $attr . '="' . esc_attr($value) . '" ', $tag);
}

function hifipig_is_ad_like_img_tag($tag) {
  $lower = strtolower($tag);
  if (strpos($lower, 'adrotate') !== false) return true;
  if (preg_match('/(leaderboard|banner|sponsor|adsbygoogle|doubleclick|googlesyndication)/i', $lower)) {
    return true;
  }

  if (preg_match('/\\swidth=(\"|\')?(\\d+)(\"|\')?/i', $tag, $m)) {
    if ((int) $m[2] <= 400) return true;
  }
  if (preg_match('/\\sheight=(\"|\')?(\\d+)(\"|\')?/i', $tag, $m)) {
    if ((int) $m[2] <= 200) return true;
  }

  if (preg_match('/\\b(?:\\d{3,4})x(?:\\d{2,3})\\b/i', $lower)) {
    if (preg_match('/\\b(?:728x90|300x250|320x50|250x(?:60|200))\\b/i', $lower)) {
      return true;
    }
  }

  return false;
}

add_filter('wp_content_img_tag', function ($tag, $context = null, $_attachment_id = null, $_size = null, $_attr = null) {
  if (!is_singular() || $context !== 'the_content') return $tag;
  if (!in_the_loop() || !is_main_query()) return $tag;
  if (is_singular('post') && !apply_filters('hifipig_enable_content_img_attrs_on_posts', false)) {
    return $tag;
  }

  static $hifipig_lcp_set = false;

  // Always ensure async decoding.
  $tag = hifipig_set_img_attr($tag, 'decoding', 'async');

  $is_ad_like = hifipig_is_ad_like_img_tag($tag);

  if (!$hifipig_lcp_set && !$is_ad_like) {
    $tag = hifipig_set_img_attr($tag, 'loading', 'eager');
    $tag = hifipig_set_img_attr($tag, 'fetchpriority', 'high');
    $hifipig_lcp_set = true;
  } else {
    if (!preg_match('/\\sloading=(\"|\').*?\\1/i', $tag)) {
      $tag = hifipig_set_img_attr($tag, 'loading', 'lazy');
    }
  }

  return $tag;
}, 10, 5);

function hifipig_ajax_search() {
  // Nonce is required for logged-in users; allow public requests for cache safety.
  if (is_user_logged_in()) {
    check_ajax_referer('hifipig_search', 'nonce');
  }

  $q = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
  $q = trim($q);

  if (mb_strlen($q) < 2) {
    wp_send_json_success(['results' => []]);
  }

  $post_types = get_post_types(['public' => true], 'names');
  $post_types = array_values(array_diff($post_types, [
    'attachment',
    'nav_menu_item',
    'revision',
    'custom_css',
    'customize_changeset',
    'oembed_cache',
    'user_request',
    'wp_block',
  ]));
  $post_types = apply_filters('hifipig_search_post_types', $post_types);

  $query = new WP_Query([
    'post_type'              => $post_types,
    'post_status'            => 'publish',
    's'                      => $q,
    'posts_per_page'         => 8,
    'no_found_rows'          => true,
    'ignore_sticky_posts'    => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    'fields'                 => 'ids',
  ]);

  $results = [];
  foreach ($query->posts as $post_id) {
    $results[] = [
      'url'   => get_permalink($post_id),
      'title' => get_the_title($post_id),
      'date'  => get_the_date('', $post_id),
    ];
  }

  wp_send_json_success(['results' => $results]);
}

/** -------------------------------------------------------
 * Customizer: sitewide banner (image + title)
 * ------------------------------------------------------ */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {
  $wp_customize->add_section('hifipig_banner', [
    'title'    => 'Site Banner',
    'priority' => 30,
  ]);

  $wp_customize->add_setting('hifipig_banner_enabled', [
    'default'           => false,
    'sanitize_callback' => function ($v) { return (bool) $v; },
  ]);

  $wp_customize->add_control('hifipig_banner_enabled', [
    'label'   => 'Enable banner below header',
    'section' => 'hifipig_banner',
    'type'    => 'checkbox',
  ]);

  $wp_customize->add_setting('hifipig_banner_title', [
    'default'           => '',
    'sanitize_callback' => 'sanitize_text_field',
  ]);

  $wp_customize->add_control('hifipig_banner_title', [
    'label'   => 'Banner title (optional)',
    'section' => 'hifipig_banner',
    'type'    => 'text',
  ]);

  $wp_customize->add_setting('hifipig_banner_image', [
    'default'           => '',
    'sanitize_callback' => 'esc_url_raw',
  ]);

  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hifipig_banner_image', [
    'label'   => 'Banner image (optional)',
    'section' => 'hifipig_banner',
  ]));

  $wp_customize->add_setting('hifipig_adrotate_top_group_id', [
    'default'           => 0,
    'sanitize_callback' => 'absint',
  ]);

  $wp_customize->add_control('hifipig_adrotate_top_group_id', [
    'label'       => 'AdRotate top ad group ID (optional)',
    'description' => 'If set, top/grid ad slots render this AdRotate group. Leave as 0 to use theme fallback ads.',
    'section'     => 'hifipig_banner',
    'type'        => 'number',
    'input_attrs' => [
      'min'  => 0,
      'step' => 1,
    ],
  ]);

  $wp_customize->add_setting('hifipig_adrotate_sidebar_group_id', [
    'default'           => '',
    'sanitize_callback' => 'hifipig_sanitize_adrotate_group_ids',
  ]);

  $wp_customize->add_control('hifipig_adrotate_sidebar_group_id', [
    'label'       => 'AdRotate sidebar ad group IDs (optional)',
    'description' => 'Comma-separated group IDs. Dynamic sidebar slots cycle through them in order, for example 1,2,1,2.',
    'section'     => 'hifipig_banner',
    'type'        => 'text',
  ]);
});

/** -------------------------------------------------------
 * Fragment cache invalidation
 * ------------------------------------------------------ */
// Header caches the primary nav HTML for guests.
function hifipig_flush_nav_cache() {
  delete_transient('hifipig_primary_menu_v1');
}
add_action('wp_update_nav_menu', 'hifipig_flush_nav_cache');
add_action('save_post_nav_menu_item', 'hifipig_flush_nav_cache');
add_action('switch_theme', 'hifipig_flush_nav_cache');

// Sidebar caches widgets for guests.
function hifipig_flush_sidebar_cache() {
  delete_transient('hifipig_sidebar_v1');
  update_option(
    'hifipig_sidebar_cache_version',
    (int) get_option('hifipig_sidebar_cache_version', 1) + 1,
    false
  );
}
// Flush when widgets change in the admin/customizer.
add_action('update_option_sidebars_widgets', 'hifipig_flush_sidebar_cache');
add_action('customize_save_after', 'hifipig_flush_sidebar_cache');
add_action('switch_theme', 'hifipig_flush_sidebar_cache');
