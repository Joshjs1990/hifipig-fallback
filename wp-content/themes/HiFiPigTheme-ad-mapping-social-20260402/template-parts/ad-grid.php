<?php
/**
 * Archive ad grid (after every 2 rows of posts)
 */
defined('ABSPATH') || exit;
?>
<div class="post-ad-grid" aria-label="Sponsored">
  <div class="post-ad-grid__lead">
    <?php get_template_part('template-parts/ad', 'top'); ?>
  </div>
  <div class="post-ad-grid__row">
    <div class="post-ad-grid__col">
      <?php get_template_part('template-parts/ad', 'top'); ?>
    </div>
    <div class="post-ad-grid__col">
      <?php get_template_part('template-parts/ad', 'top'); ?>
    </div>
  </div>
</div>
