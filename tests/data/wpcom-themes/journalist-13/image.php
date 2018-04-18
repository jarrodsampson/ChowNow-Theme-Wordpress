<?php get_header(); ?>

<div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div <?php post_class() ?>>
	<h2 id="post-<?php the_ID(); ?>"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <?php the_title(); ?></h2>
	<div class="meta"><?php edit_post_link(__('Edit This','journalist-13'), ' '); ?></div>
	<div class="main">
		<p class="attachment"><a href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image( $post->ID, 'auto' ); ?></a></p>
		<div class="caption"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); ?></div>
		<div class="image-description"><?php if ( !empty($post->post_content) ) the_content(); ?></div>

		<div class="navigation">
			<div class="alignleft"><?php previous_image_link() ?></div>
			<div class="alignright"><?php next_image_link() ?></div>
		</div>
	</div>
	<div class="comments">
		<?php comments_popup_link(__('leave a comment','journalist-13'), __('<strong>1</strong> comment','journalist-13'), __('<strong>%</strong> comments','journalist-13')); ?>
	</div>
</div>

<?php comments_template(); ?>

<?php endwhile; else: ?>
<div class="warning">
	<p><?php _e('Sorry, no posts matched your criteria, please try and search again.','journalist-13'); ?></p>
</div>
<?php endif; ?>

</div> <!-- End content -->

<?php get_sidebar(); ?>

<div class="clearleft"></div>

<?php get_footer(); ?>
