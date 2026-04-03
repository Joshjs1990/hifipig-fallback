<?php get_header(); ?>

<main class="site-main">
  <div class="layout" data-sidebar="right">
    <section class="content-area">
      <?php while ( have_posts() ) : the_post(); ?>
        <article>
          <?php get_template_part('template-parts/ad', 'top'); ?>
          <h1><?php the_title(); ?></h1>
          <?php the_content(); ?>
        </article>
      <?php endwhile; ?>
    </section>

    <?php get_sidebar(); ?>
  </div>
</main>

<?php get_footer(); ?>
