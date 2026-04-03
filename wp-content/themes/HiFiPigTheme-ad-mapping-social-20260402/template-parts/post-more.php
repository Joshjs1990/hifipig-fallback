<?php
/**
 * Post bottom section: prev/next + latest posts
 */
defined('ABSPATH') || exit;

$post_id = get_the_ID();
$key = 'hifipig_postmore_' . (int) $post_id;
$cached = function_exists('hifipig_get_fragment') ? hifipig_get_fragment($key) : false;

if ($cached !== false && $cached !== '') {
  echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  return;
}

ob_start();
?>
<section class="post-more" aria-label="More posts">
  <div class="post-more__inner">
    <div class="post-more__block">
      <h2 class="post-more__title">Next / Previous</h2>
      <?php get_template_part('template-parts/post', 'nav'); ?>
    </div>

    <div class="post-more__block">
      <h2 class="post-more__title">Latest posts</h2>
      <?php
      $latest = new WP_Query([
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 4,
        'post__not_in'           => [$post_id],
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
      ]);

      if ($latest->have_posts()) :
      ?>
        <div class="post-more__grid">
          <?php while ($latest->have_posts()) : $latest->the_post(); ?>
            <a class="post-more__card" href="<?php the_permalink(); ?>">
              <span class="post-more__thumb">
                <?php the_post_thumbnail('medium', [
                  'loading'  => 'lazy',
                  'decoding' => 'async',
                ]); ?>
              </span>
              <span class="post-more__card-body">
                <span class="post-more__card-title"><?php the_title(); ?></span>
                <span class="post-more__card-meta"><?php echo esc_html(get_the_date()); ?></span>
              </span>
            </a>
          <?php endwhile; ?>
        </div>
      <?php
      endif;
      wp_reset_postdata();
      ?>
    </div>
  </div>
</section>
<?php
$html = ob_get_clean();

if (function_exists('hifipig_set_fragment')) {
  hifipig_set_fragment($key, $html, 300);
}

echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
