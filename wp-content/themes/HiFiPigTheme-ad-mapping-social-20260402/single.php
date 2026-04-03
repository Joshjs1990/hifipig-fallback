<?php get_header(); ?>

<main class="site-main">
  <div class="layout" data-sidebar="right">

    <section class="content-area">
      <?php while ( have_posts() ) : the_post(); ?>

        <article <?php post_class(); ?>>

          <?php do_action('hifipig_before_entry_title'); ?>

          <h1><?php the_title(); ?></h1>
          <time class="post-single__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
            <?php echo esc_html(get_the_date()); ?>
          </time>

          <?php do_action('hifipig_after_entry_title'); ?>

          <?php the_content(); ?>

          <?php get_template_part('template-parts/post', 'more'); ?>

        </article>

      <?php endwhile; ?>
    </section>

    <?php get_sidebar(); ?>

  </div>
</main>

<?php get_footer(); ?>
