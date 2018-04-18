<?php // Do not delete these lines


if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');
if ( post_password_required() ) {
?>
<p class="nocomments"><?php _e("This post is password protected. Enter the password to view comments."); ?></p>
<?php
	return;
}

function quentin_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);
?>
<li <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<div id="div-comment-<?php comment_ID() ?>">
	<div class="comment-author vcard">
		<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
		<cite class="comment-meta commentmetadata">On <?php comment_date() ?> at <?php comment_time() ?>
		<span class="fn"><?php comment_author_link() ?></span> Said: 
		<?php edit_comment_link(__("Edit This"), ' |'); ?>
	 </cite>
	 </div>
	 <?php if ($comment->comment_approved == '0') : ?>
		<em>Your comment is awaiting moderation.</em>
	<?php endif; ?>

	<?php comment_text(); ?>
	
	<div class="reply">
		<?php comment_reply_link(array_merge( $args, array('add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
	</div>
<?php
}

?>
<?php if ( pings_open() || have_comments() || comments_open() ) { ?>
<div id="trackback">
<?php if (pings_open()) { ?>
<p><?php _e("The <acronym title=\"Uniform Resource Identifier\">URI</acronym> to TrackBack this entry is:"); ?> <em><?php trackback_url() ?></em></p>
<?php } ?>
<?php if ( have_comments() || comments_open() ) { ?>
<p><?php comments_rss_link(__("<abbr title=\"Really Simple Syndication\">RSS</abbr> feed for comments on this post.")); ?></p>
<?php } ?>
</div>
<?php } ?>


<?php if (have_comments()) : ?>
	<h2 id="comments"><?php comments_number(__("Comments"), __("One Comment"), __("% Comments")); ?> 
<?php if (comments_open()) { ?>
<a href="#postcomment" title="<?php _e("Leave a comment"); ?>"><span>Leave a comment.</span></a>
<?php } ?></h2> 


	<ol id="commentlist">
	<?php wp_list_comments(array('callback'=>'quentin_comment')); ?>
	</ol>
	
	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
	<br />

  <?php if (!comments_open()) : ?> 
	<p class="nocomments">Comments are closed.</p>
  <?php endif; ?>
<?php endif; ?>


<?php if (comments_open()) : ?>
<div id="respond">
<div id="commentf">
<h2 id="postcomment"><?php comment_form_title( 'Leave a Comment', 'Leave a Comment to %s' ); ?></h2>
<div id="cancel-comment-reply"><small><?php cancel_comment_reply_link() ?></small></div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">logged in</a> to post a comment.</p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>">Logout &raquo;</a></p>

<?php else : ?>

<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
<label for="author"><small>Name <?php if ($req) _e('(required)'); ?></small></label></p>

<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
<label for="email"><small>E-mail (will not be published) <?php if ($req) _e('(required)'); ?></small></label></p>

<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<label for="url"><small>Website</small></label></p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small></p>-->

<p><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

<p><input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
<?php comment_id_fields(); ?>
</p>
<?php do_action('comment_form', $post->ID); ?>

</form>
<?php endif; // If registration required and not logged in ?>
</div>
</div>
<?php endif; // if you delete this the sky will fall on your head ?>
