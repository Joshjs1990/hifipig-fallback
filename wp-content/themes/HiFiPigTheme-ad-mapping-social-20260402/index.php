<?php get_header(); ?>

<main class="site-main" id="main-content">
  <div class="layout" data-sidebar="right">

    <section class="content-area">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <?php get_template_part('template-parts/content'); ?>
      <?php endwhile; the_posts_pagination(); else: ?>
        <p>No posts found.</p>
      <?php endif; ?>
    </section>

    <?php get_sidebar(); ?>

  </div>
</main>

<?php get_footer(); ?>