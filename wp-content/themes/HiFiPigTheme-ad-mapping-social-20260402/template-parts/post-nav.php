<?php
/**
 * Previous/Next post nav (cached per-post for guests)
 */
defined('ABSPATH') || exit;

$post_id = get_the_ID();
$key = 'hifipig_postnav_' . (int) $post_id;

$cached = function_exists('hifipig_get_fragment') ? hifipig_get_fragment($key) : false;
if ($cached !== false && $cached !== '') {
  echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  return;
}

$prev = get_previous_post();
$next = get_next_post();

if (!$prev && !$next) return;

ob_start();
?>
<nav class="post-nav" aria-label="Post">
  <?php if ($prev): ?>
    <a class="post-nav__item post-nav__item--prev" href="<?php echo esc_url(get_permalink($prev)); ?>">
      <span class="post-nav__kicker">Previous</span>
      <span class="post-nav__title"><?php echo esc_html(get_the_title($prev)); ?></span>
    </a>
  <?php endif; ?>

  <?php if ($next): ?>
    <a class="post-nav__item post-nav__item--next" href="<?php echo esc_url(get_permalink($next)); ?>">
      <span class="post-nav__kicker">Next</span>
      <span class="post-nav__title"><?php echo esc_html(get_the_title($next)); ?></span>
    </a>
  <?php endif; ?>
</nav>
<?php
$html = ob_get_clean();

if (function_exists('hifipig_set_fragment')) {
  hifipig_set_fragment($key, $html, DAY_IN_SECONDS);
}

echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped