<?php get_header(); ?>
<main class="site-main">
  <div class="layout" data-sidebar="right">
    <section class="content-area">
      <?php get_template_part('template-parts/ad', 'top'); ?>
      <h1>Search: <?php echo esc_html(get_search_query()); ?></h1>
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php get_template_part('template-parts/content'); ?>
      <?php endwhile; the_posts_pagination(); else: ?>
        <p>No results. Try another search.</p>
        <?php get_search_form(); ?>
      <?php endif; ?>
    </section>

    <?php get_sidebar(); ?>
  </div>
</main>
<?php get_footer(); ?>
