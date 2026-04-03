<?php
/**
 * Sidebar (cached for guests)
 */
defined('ABSPATH') || exit;

if (!is_active_sidebar('primary-sidebar') && !function_exists('hifipig_get_dynamic_sidebar_ads_html')) {
  return;
}

$dynamic_ads_html = function_exists('hifipig_get_dynamic_sidebar_ads_html')
  ? hifipig_get_dynamic_sidebar_ads_html()
  : '';
$uses_dynamic_sidebar_ads = $dynamic_ads_html !== '';

if (!is_active_sidebar('primary-sidebar') && $dynamic_ads_html === '') {
  return;
}

$key = function_exists('hifipig_get_sidebar_cache_key')
  ? hifipig_get_sidebar_cache_key()
  : 'hifipig_sidebar_v1';
$cached = function_exists('hifipig_get_fragment') ? hifipig_get_fragment($key) : false;

if ($cached !== false && $cached !== '') {
  echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  return;
}

ob_start();
?>
<aside class="site-aside" data-type="type-2">
  <div class="site-sidebar" data-sticky="sidebar"<?php echo $uses_dynamic_sidebar_ads ? ' data-dynamic-sidebar="true"' : ''; ?>>
    <?php if (is_active_sidebar('primary-sidebar')) : ?>
      <?php dynamic_sidebar('primary-sidebar'); ?>
    <?php endif; ?>
    <?php echo $dynamic_ads_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  </div>
</aside>
<?php
$html = ob_get_clean();

if (function_exists('hifipig_set_fragment')) {
  hifipig_set_fragment($key, $html, 300);
}

echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
