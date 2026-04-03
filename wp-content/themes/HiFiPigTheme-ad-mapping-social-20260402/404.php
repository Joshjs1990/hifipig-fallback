<?php get_header(); ?>

<main class="site-main" id="main-content">
  <section class="error-404">
    <div class="error-404__media">
      <?php
        $img_path = get_template_directory() . '/assets/404.jpg';
        $img_url = get_template_directory_uri() . '/assets/404.jpg';
        $size = @getimagesize($img_path);
        $width = is_array($size) && !empty($size[0]) ? (int) $size[0] : 1200;
        $height = is_array($size) && !empty($size[1]) ? (int) $size[1] : 800;
      ?>
      <img
        src="<?php echo esc_url($img_url); ?>"
        width="<?php echo esc_attr($width); ?>"
        height="<?php echo esc_attr($height); ?>"
        loading="lazy"
        decoding="async"
        alt="404 error happened. But, hey, it could be worse."
      >
    </div>
    <div class="error-404__content">
      <p class="error-404__kicker">Page not found</p>
      <h1 class="error-404__title">404</h1>
      <p class="error-404__text">We’re sorry, that page doesn’t exist or the site is royally fucked. Here’s an emo to brighten your day!</p>
      <a class="error-404__cta" href="<?php echo esc_url(home_url('/')); ?>">Back to home</a>
    </div>
  </section>
</main>

<?php get_footer(); ?>
