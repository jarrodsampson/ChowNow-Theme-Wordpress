<?php // Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');
if ( post_password_required() ) { ?>
<p><?php _e('This post is password protected. Enter the password to view comments.','almost-spring'); ?><p>
<?php 
return;
}

function almost_spring_callback($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);
?>
	<li <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<div class="comment-author vcard">
	<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
	<h3 class="commenttitle"><cite class="fn"><?php comment_author_link(); ?></cite> <span class="says"><?php _e('said','almost-spring'); ?></span></h3>
	</div>
<?php if ($comment->comment_approved == '0') : ?>
	<em><?php _e('Your comment is awaiting moderation.','almost-spring') ?></em>
	<br />
<?php endif; ?>
	
	<small class="comment-meta commentmetadata commentmeta">
	<?php comment_date(); ?> @ 
	<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>" 
	title="<?php _e('Permanent link to this comment','almost-spring'); ?>"><?php comment_time(); ?></a>
	<?php edit_comment_link(__('Edit','almost-spring'), ' &#183; ', ''); ?></small>

	<?php comment_text(); ?>

	<div class="reply">
	<?php comment_reply_link(array_merge( $args, array('add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
<?php 
}
	
if ( have_comments() ) : ?>
<div><h2 id="comments">
<?php comments_number(__('Comments','almost-spring'), __('1 Comment','almost-spring'), __('% Comments','almost-spring'));?>
<?php if ( comments_open() ) : ?>
	<a href="#postcomment" title="<?php _e('Jump to the comments form','almost-spring'); ?>">&raquo;</a>
<?php endif; ?>
</h2>
 
<ol class="commentlist" id="commentlist">
<?php wp_list_comments(array('callback'=>'almost_spring_callback')); ?>
</ol>
	
	<p class="small">
	<?php comments_rss_link(__('<abbr title="Really Simple Syndication">RSS</abbr> feed for comments on this post','almost-spring')); ?>
	<?php if ( pings_open() ) : ?>
	&#183; <a href="<?php trackback_url() ?>" rel="trackback"><?php _e('TrackBack <abbr title="Uniform Resource Identifier">URI</abbr>','almost-spring'); ?></a>
	<?php endif; ?>
	</p>

<div class="navigation">
<div class="alignleft"><?php previous_comments_link() ?></div>
<div class="alignright"><?php next_comments_link() ?></div>
</div>
</div>
<?php else : // this is displayed if there are no comments so far ?>
	<?php if (comments_open()) :
		// If comments are open, but there are no comments.
	else : // comments are closed
	endif;
?>

<?php
endif;
?>

<div id="respond">
<?php if (comments_open()) : ?>

	<h2 id="postcomment"><?php comment_form_title(__('Leave a Comment','almost-spring'), __('Leave a Comment for %s','almost-spring')); ?></h2>
	
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	
		<p><?php _e('You must be','almost-spring'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>"><?php _e('logged in','almost-spring'); ?></a> <?php _e('to post a comment.','almost-spring'); ?></p>
	
	<?php else : ?>
	
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		<div id="cancel-comment-reply">
		<small><?php cancel_comment_reply_link() ?></small></div>
		<?php if ( $user_ID ) : ?>
		
			<p><?php _e('Logged in as','almost-spring'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account','almost-spring') ?>"><?php _e('Logout','almost-spring'); ?> &raquo;</a></p>

		<?php else : ?>
	
			<p>
			<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="30" tabindex="1" />
			<label for="author"><?php _e('Name','almost-spring'); ?> <?php if ($req) _e('(required)','almost-spring'); ?></label>
			</p>
			
			<p>
			<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="30" tabindex="2" />
			<label for="email"><?php _e('E-mail','almost-spring'); ?> <?php if ($req) _e('(required)','almost-spring'); ?></label>
			</p>
			
			<p>
			<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="30" tabindex="3" />
			<label for="url"><abbr title="<?php _e('Uniform Resource Identifier','almost-spring'); ?>"><?php _e('URI','almost-spring'); ?></abbr></label>
			</p>

		<?php endif; ?>

		<p>
		<textarea name="comment" id="comment" cols="70" rows="10" tabindex="4"></textarea>
		</p>
	
		<p>
		<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment','almost-spring'); ?>" />
		<?php comment_id_fields(); ?>
		</p>
	
		<?php do_action('comment_form', $post->ID); ?>
	
		</form>

	<?php endif; // If registration required and not logged in ?>

<?php elseif ($comments) : ?>
	<p><?php _e('Comments are closed.','almost-spring'); ?></p>

<?php endif; // if you delete this the sky will fall on your head ?>
</div>