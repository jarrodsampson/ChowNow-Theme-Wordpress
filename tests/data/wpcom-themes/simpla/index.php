<?php get_header(); ?>
<div id="content">
<?php is_tag(); ?>
	<?php if (have_posts()) :?>
		<?php $postCount=0; ?>
		<?php while (have_posts()) : the_post();?>
			<?php $postCount++;?>
	<div <?php post_class('entry entry-' . $postCount); ?> id="post-<?php the_ID(); ?>">
		<div class="entrytitle">
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2> 
			<h3><?php the_time(get_option('date_format')) ?></h3>
		</div>
		<div class="entrybody">
			<?php the_content('Read the rest of this entry &raquo;'); ?>
			<?php wp_link_pages(); ?>
		</div>
		
		<div class="entrymeta">
		<div class="postinfo">
			<span class="postedby">Posted by <?php the_author() ?></span><br />
			<?php if ( !is_page() ) { ?>
				<span class="filedto">Filed in <?php the_category(', ') ?></span><br />
				<?php the_tags('<span class="filedto">Tags: ', ', ', '</span><br />'); ?>
			<?php } ?>
			<span class="filedto"><?php edit_post_link('Edit', '', ''); ?></span>
		</div>
		<?php comments_popup_link('Leave a Comment &#187;', '1 Comment &#187;', '% Comments &#187;', 'commentslink'); ?>
		</div>
		
	</div>
	<div class="commentsblock">
		<?php comments_template(); ?>
	</div>
	<?php endwhile; ?>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
		
	<?php else : ?>

		<h2>Not Found</h2>
		<div class="entrybody">Sorry, but you are looking for something that isn't here.</div>

	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
