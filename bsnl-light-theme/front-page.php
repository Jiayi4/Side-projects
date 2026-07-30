<?php
/**
 * Editor-owned front page template.
 */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>
  <main class="bsnl-site">
    <?php the_content(); ?>
  </main>
<?php endwhile; ?>

<?php
get_footer();
