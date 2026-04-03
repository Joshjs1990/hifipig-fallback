<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<script>
(function(){
  try{
    var key='hifipig_theme';
    var t=localStorage.getItem(key);
    if(t!=='dark' && t!=='light' && t!=='pink'){
      t = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    document.documentElement.setAttribute('data-theme', t);
    document.documentElement.style.colorScheme = (t === 'dark') ? 'dark' : 'light';
    if (document.cookie.indexOf('wordpress_logged_in_') === -1) {
      document.documentElement.classList.add('no-wp-login-cookie');
    }
  }catch(e){}
})();
</script>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content">Skip to content</a>

<header class="site-header">
  <div class="inner">

    <div class="site-brand">
      <?php
      if (function_exists('the_custom_logo') && has_custom_logo()) {
        the_custom_logo();
      } else {
        ?>
        <a class="site-title" href="<?php echo esc_url(home_url('/')); ?>">
          <?php bloginfo('name'); ?>
        </a>
        <?php
      }
      ?>
    </div>

<!-- Mobile toggle -->
    <button
      class="menu-toggle"
      type="button"
      aria-controls="primary-menu"
      aria-expanded="false"
    >
      <span class="screen-reader-text">Menu</span>
      <span class="bars"><span></span></span>
    </button>
    
  <!-- PRIMARY NAV -->
<nav class="site-nav" aria-label="Primary navigation">
  <?php
  $menu_key = 'hifipig_primary_menu_v1';
  $menu_html = function_exists('hifipig_get_fragment') ? hifipig_get_fragment($menu_key) : false;

  if ($menu_html === false || $menu_html === '') {
    $menu_html = wp_nav_menu([
      'theme_location' => 'primary',
      'container'      => false,
      'menu_class'     => 'menu',
      'menu_id'        => 'primary-menu',
      'fallback_cb'    => false,
      'echo'           => false,
    ]);

    if (function_exists('hifipig_set_fragment') && $menu_html) {
      hifipig_set_fragment($menu_key, $menu_html, 300);
    }
  }

  echo $menu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  ?>
</nav>



<!-- Theme toggle -->
 <button class="theme-toggle" type="button" aria-label="Toggle theme" data-theme-toggle>
  <svg class="theme-toggle__moon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
    <defs>
      <mask id="moon-cut">
        <rect width="24" height="24" fill="#fff"></rect>
        <circle cx="14.2" cy="10.2" r="7.2" fill="#000"></circle>
      </mask>
    </defs>
    <circle cx="9.5" cy="12" r="8.6" fill="currentColor" mask="url(#moon-cut)"></circle>
  </svg>

  <svg class="theme-toggle__sun" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
    <circle cx="12" cy="12" r="3.5" fill="none" stroke="currentColor" stroke-width="1.8"></circle>
    <circle cx="12" cy="2.8" r="1" fill="currentColor"></circle>
    <circle cx="12" cy="21.2" r="1" fill="currentColor"></circle>
    <circle cx="2.8" cy="12" r="1" fill="currentColor"></circle>
    <circle cx="21.2" cy="12" r="1" fill="currentColor"></circle>
    <circle cx="4.6" cy="4.6" r="1" fill="currentColor"></circle>
    <circle cx="19.4" cy="19.4" r="1" fill="currentColor"></circle>
    <circle cx="19.4" cy="4.6" r="1" fill="currentColor"></circle>
    <circle cx="4.6" cy="19.4" r="1" fill="currentColor"></circle>
  </svg>
</button>

<!-- Search button -->
<button class="header-search" type="button" aria-label="Search" data-search-open>
  <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
    <path d="M10 4a6 6 0 104.472 10.03l3.749 3.75 1.414-1.415-3.75-3.749A6 6 0 0010 4zm0 2a4 4 0 110 8 4 4 0 010-8z"></path>
  </svg>
</button>

<div class="search-overlay" data-search-overlay>
  <div class="search-panel" role="dialog" aria-modal="true" aria-label="Search">
    <div class="search-panel__head">
      <input class="search-panel__input" type="search" placeholder="Search..." autocomplete="off" data-search-input>
    </div>
    <div class="search-panel__results" data-search-results></div>
  </div>
</div>

  </div>
</header>

<?php if (!is_single() && (get_theme_mod('hifipig_banner_enabled') || is_home() || is_front_page())): ?>
  <div class="site-banner">
    <div class="site-banner__inner">
      <?php $img = get_theme_mod('hifipig_banner_image'); ?>
      <?php if ($img): ?>
        <div class="site-banner__media">
          <?php
            $img_id = attachment_url_to_postid($img);
            $banner_attrs = [
              'class'         => 'site-banner__img',
              'decoding'      => 'async',
              'loading'       => 'eager',
              'fetchpriority' => 'high',
            ];

            if ($img_id) {
              echo wp_get_attachment_image($img_id, 'full', false, $banner_attrs);
            } else {
              $attr_html = '';
              foreach ($banner_attrs as $key => $value) {
                $attr_html .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
              }
              echo '<img src="' . esc_url($img) . '" alt=""' . $attr_html . '>';
            }
          ?>
        </div>
      <?php endif; ?>

      <?php $title = get_theme_mod('hifipig_banner_title'); ?>
      <?php if ($title): ?>
        <h2 class="site-banner__title"><?php echo esc_html($title); ?></h2>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
