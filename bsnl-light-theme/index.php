<?php
/**
 * Fallback template.
 */

get_header();
?>

<main class="bsnl-page">
  <header class="bsnl-page-hero">
    <div class="bsnl-page-shell">
      <div class="bsnl-eyebrow">BioScience Network Lausanne</div>
      <h1><?php bloginfo('name'); ?></h1>
    </div>
  </header>

  <section class="bsnl-page-content">
    <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('bsnl-post-summary'); ?>>
          <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php the_excerpt(); ?>
        </article>
      <?php endwhile; ?>
      <?php the_posts_pagination(); ?>
    <?php else : ?>
      <p><?php esc_html_e('No content found.', 'bsnl-light'); ?></p>
    <?php endif; ?>
  </section>
</main>

<?php
get_footer();

