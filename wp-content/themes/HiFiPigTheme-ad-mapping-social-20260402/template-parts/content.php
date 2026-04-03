<article <?php post_class('post-card'); ?>>

  <?php
    static $hifipig_card_index = 0;
    $hifipig_card_index++;
    $is_first_card = $hifipig_card_index === 1;

    $thumb_attrs = [
      'loading'  => $is_first_card ? 'eager' : 'lazy',
      'decoding' => 'async',
    ];

    if ($is_first_card) {
      $thumb_attrs['fetchpriority'] = 'high';
    }

    $card_video_url = (string) get_post_meta(get_the_ID(), '_hifipig_card_youtube_url', true);
    $card_video_embed = hifipig_get_youtube_embed_url($card_video_url);
    $media_loading = $is_first_card ? 'eager' : 'lazy';
  ?>
  <?php if ($card_video_embed) : ?>
    <div class="post-card__media post-card__media--video" style="position:relative;overflow:hidden;aspect-ratio:16/9;background:#000;">
      <iframe
        src="<?php echo esc_url($card_video_embed); ?>"
        title="<?php echo esc_attr(sprintf('YouTube video: %s', get_the_title())); ?>"
        width="100%"
        height="100%"
        style="position:absolute;inset:0;width:100% !important;height:100% !important;min-width:100%;min-height:100%;max-width:none;border:0;display:block;"
        loading="<?php echo esc_attr($media_loading); ?>"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        referrerpolicy="strict-origin-when-cross-origin"
        allowfullscreen
      ></iframe>
    </div>
  <?php else : ?>
    <a class="post-card__media" href="<?php the_permalink(); ?>">
      <?php the_post_thumbnail('large', $thumb_attrs); ?>
    </a>
  <?php endif; ?>

  <div class="post-card__body">

    <div class="post-card__meta">
      <div class="post-card__cats"><?php the_category(' '); ?></div>
      <time class="post-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
        <?php echo esc_html(get_the_date()); ?>
      </time>
    </div>

    <h2 class="post-card__title">
      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h2>

    <div class="post-card__excerpt">
      <?php the_excerpt(); ?>
    </div>

    <div class="post-card__divider" aria-hidden="true"></div>

    <?php
      $ad_id  = (int) get_post_meta(get_the_ID(), '_hifipig_card_ad_image_id', true);
      $ad_url = (string) get_post_meta(get_the_ID(), '_hifipig_card_ad_url', true);

      $cats = get_the_category();
      if (is_array($cats) && count($cats) === 1) {
        $cat = $cats[0];
        $override = (int) get_term_meta($cat->term_id, '_hifipig_category_sponsor_override', true);
        if ($override) {
          $cat_image_id = (int) get_term_meta($cat->term_id, '_hifipig_category_image_id', true);
          if ($cat_image_id) {
            $ad_id = $cat_image_id;
            $ad_url = (string) get_term_meta($cat->term_id, '_hifipig_category_image_url', true);
          }
        }
      }

      if ($ad_id) :
        $img = wp_get_attachment_image($ad_id, 'medium', false, [
          'class' => 'post-card__ad-image',
          'loading' => 'lazy',
          'decoding' => 'async',
        ]);
    ?>
      <div class="post-card__ad">
        <?php if ($ad_url) : ?>
          <a class="post-card__ad-link" href="<?php echo esc_url($ad_url); ?>" rel="nofollow sponsored noopener" target="_blank">
            <?php echo $img; ?>
          </a>
        <?php else : ?>
          <?php echo $img; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</article>

<?php
  global $wp_query;
  if ($wp_query && $wp_query->is_main_query() && ($wp_query->is_archive() || $wp_query->is_home() || $wp_query->is_search())) {
    $posts_per_row = 2;
    $rows_per_ad = 2;
    $interval = $posts_per_row * $rows_per_ad;
    $last_index = max(0, (int) $wp_query->post_count - 1);
    if ($interval > 0 && (($wp_query->current_post + 1) % $interval) === 0 && $wp_query->current_post < $last_index) {
      get_template_part('template-parts/ad', 'grid');
    }
  }
?>
