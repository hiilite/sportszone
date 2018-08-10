<?php
/**
 * BP Nouveau Messages main template.
 *
 * This template is used to inject the SportsZone Backbone views
 * dealing with user's private messages.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>
<div class="subnav-filters filters user-subnav sz-messages-filters" id="subsubnav"></div>

<div class="sz-messages-feedback"></div>
<div class="sz-messages-content"></div>

<script type="text/html" id="tmpl-sz-messages-feedback">
	<div class="sz-feedback {{data.type}}">
		<span class="sz-icon" aria-hidden="true"></span>
		<p>{{{data.message}}}</p>
	</div>
</script>

<?php
/**
 * This view is used to inject hooks buffer
 */
?>
<script type="text/html" id="tmpl-sz-messages-hook">
	{{{data.extraContent}}}
</script>

<script type="text/html" id="tmpl-sz-messages-form">
	<?php sz_nouveau_messages_hook( 'before', 'compose_content' ); ?>

	<label for="send-to-input"><?php esc_html_e( 'Send @Username', 'sportszone' ); ?></label>
	<input type="text" name="send_to" class="send-to-input" id="send-to-input" />

	<label for="subject"><?php _e( 'Subject', 'sportszone' ); ?></label>
	<input type="text" name="subject" id="subject"/>

	<div id="sz-message-content"></div>

	<?php sz_nouveau_messages_hook( 'after', 'compose_content' ); ?>

	<div class="submit">
		<input type="button" id="sz-messages-send" class="button sz-primary-action" value="<?php echo esc_attr_x( 'Send', 'button', 'sportszone' ); ?>"/>
		<input type="button" id="sz-messages-reset" class="text-button small sz-secondary-action" value="<?php echo esc_attr_x( 'Reset', 'form reset button', 'sportszone' ); ?>"/>
	</div>
</script>

<script type="text/html" id="tmpl-sz-messages-editor">
	<?php
	// Add a temporary filter on editor buttons
	add_filter( 'mce_buttons', 'sz_nouveau_messages_mce_buttons', 10, 1 );

	wp_editor(
		'',
		'message_content',
		array(
			'textarea_name' => 'message_content',
			'teeny'         => false,
			'media_buttons' => false,
			'dfw'           => false,
			'tinymce'       => true,
			'quicktags'     => false,
			'tabindex'      => '3',
			'textarea_rows' => 5,
		)
	);

	// Remove the temporary filter on editor buttons
	remove_filter( 'mce_buttons', 'sz_nouveau_messages_mce_buttons', 10, 1 );
	?>
</script>

<script type="text/html" id="tmpl-sz-messages-paginate">
	<# if ( 1 !== data.page ) { #>
		<button id="sz-messages-prev-page"class="button messages-button">
			<span class="dashicons dashicons-arrow-left"></span>
			<span class="sz-screen-reader-text"><?php echo esc_html_x( 'Previous page', 'link', 'sportszone' ); ?></span>
		</button>
	<# } #>

	<# if ( data.total_page !== data.page ) { #>
		<button id="sz-messages-next-page"class="button messages-button">
			<span class="dashicons dashicons-arrow-right"></span>
			<span class="sz-screen-reader-text"><?php echo esc_html_x( 'Next page', 'link', 'sportszone' ); ?></span>
		</button>
	<# } #>
</script>

<script type="text/html" id="tmpl-sz-messages-filters">
	<li class="user-messages-search" role="search" data-sz-search="{{data.box}}">
		<div class="sz-search messages-search">
			<form action="" method="get" id="user_messages_search_form" class="sz-messages-search-form" data-sz-search="messages">
				<label for="user_messages_search" class="sz-screen-reader-text">
					<?php _e( 'Search Messages', 'sportszone' ); ?>
				</label>
				<input type="search" id="user_messages_search" placeholder="<?php echo esc_attr_x( 'Search', 'search placeholder text', 'sportszone' ); ?>"/>
				<button type="submit" id="user_messages_search_submit">
					<span class="dashicons dashicons-search" aria-hidden="true"></span>
					<span class="sz-screen-reader-text"><?php echo esc_html_x( 'Search', 'button', 'sportszone' ); ?></span>
				</button>
			</form>
		</div>
	</li>
	<li class="user-messages-bulk-actions"></li>
</script>

<script type="text/html" id="tmpl-sz-bulk-actions">
	<input type="checkbox" id="user_messages_select_all" value="1"/>
	<label for="user_messages_select_all"><?php esc_html_e( 'All Messages', 'sportszone' ); ?></label>
	<div class="bulk-actions-wrap sz-hide">
		<div class="bulk-actions select-wrap">
			<label for="user-messages-bulk-actions" class="sz-screen-reader-text">
				<?php esc_html_e( 'Select bulk action', 'sportszone' ); ?>
			</label>
			<select id="user-messages-bulk-actions">
				<# for ( i in data ) { #>
					<option value="{{data[i].value}}">{{data[i].label}}</option>
				<# } #>
			</select>
			<span class="select-arrow" aria-hidden="true"></span>
		</div>
		<button class="messages-button bulk-apply sz-tooltip" type="submit" data-sz-tooltip="<?php echo esc_attr_x( 'Apply', 'button', 'sportszone' ); ?>">
			<span class="dashicons dashicons-yes" aria-hidden="true"></span>
			<span class="sz-screen-reader-text"><?php echo esc_html_x( 'Apply', 'button', 'sportszone' ); ?></span>
		</button>
	</div>
</script>

<script type="text/html" id="tmpl-sz-messages-thread">
	<div class="thread-cb">
		<input class="message-check" type="checkbox" name="message_ids[]" id="sz-message-thread-{{data.id}}" value="{{data.id}}">
		<label for="sz-message-thread-{{data.id}}" class="sz-screen-reader-text"><?php esc_html_e( 'Select message:', 'sportszone' ); ?> {{data.subject}}</label>
	</div>

	<# if ( ! data.recipientsCount ) { #>
		<div class="thread-from">
			<a class="user-link" href="{{data.sender_link}}">
				<img class="avatar" src="{{data.sender_avatar}}" alt="" />
				<span class="sz-screen-reader-text"><?php esc_html_e( 'From:', 'sportszone' ); ?></span>
				<span class="user-name">{{data.sender_name}}</span>
			</a>
		</div>
	<# } else {
		var recipient = _.first( data.recipients );
		#>
		<div class="thread-to">
			<a class="user-link" href="{{recipient.user_link}}">
				<img class="avatar" src="{{recipient.avatar}}" alt="" />
				<span class="sz-screen-reader-text"><?php esc_html_e( 'To:', 'sportszone' ); ?></span>
				<span class="user-name">{{recipient.user_name}}</span>
			</a>

			<# if ( data.toOthers ) { #>
				<span class="num-recipients">{{data.toOthers}}</span>
			<# } #>
		</div>
	<# } #>

	<div class="thread-content" data-thread-id="{{data.id}}">
		<div class="thread-subject">
			<span class="thread-count">({{data.count}})</span>
			<a class="subject" href="../view/{{data.id}}/">{{data.subject}}</a>
		</div>
		<p class="excerpt">{{data.excerpt}}</p>
	</div>
	<div class="thread-date">
		<time datetime="{{data.date.toISOString()}}">{{data.display_date}}</time>
	</div>
</script>

<script type="text/html" id="tmpl-sz-messages-preview">
	<# if ( undefined !== data.content ) { #>

		<h2 class="message-title preview-thread-title"><?php esc_html_e( 'Active conversation:', 'sportszone' ); ?><span class="messages-title">{{{data.subject}}}</span></h2>
		<div class="preview-content">
			<header class="preview-pane-header">

				<# if ( undefined !== data.recipients ) { #>
					<dl class="thread-participants">
						<dt><?php esc_html_e( 'Participants:', 'sportszone' ); ?></dt>
						<dd>
							<ul class="participants-list">
								<# for ( i in data.recipients ) { #>
									<li><a href="{{data.recipients[i].user_link}}" class="sz-tooltip" data-sz-tooltip="{{data.recipients[i].user_name}}"><img class="avatar mini" src="{{data.recipients[i].avatar}}" alt="{{data.recipients[i].user_name}}" /></a></li>
								<# } #>
							</ul>
						</dd>
					</dl>
				<# } #>

				<div class="actions">

					<button type="button" class="message-action-delete sz-tooltip sz-icons" data-sz-action="delete" data-sz-tooltip="<?php esc_attr_e( 'Delete conversation.', 'sportszone' ); ?>">
						<span class="sz-screen-reader-text"><?php esc_html_e( 'Delete conversation.', 'sportszone' ); ?></span>
					</button>

					<# if ( undefined !== data.star_link ) { #>

						<# if ( false !== data.is_starred ) { #>
							<a role="button" class="message-action-unstar sz-tooltip sz-icons" href="{{data.star_link}}" data-sz-action="unstar" aria-pressed="true" data-sz-tooltip="<?php esc_attr_e( 'Unstar Conversation', 'sportszone' ); ?>">
								<span class="sz-screen-reader-text"><?php esc_html_e( 'Unstar Conversation', 'sportszone' ); ?></span>
							</a>
						<# } else { #>
							<a role="button" class="message-action-star sz-tooltip sz-icons" href="{{data.star_link}}" data-sz-action="star" aria-pressed="false" data-sz-tooltip="<?php esc_attr_e( 'Star Conversation', 'sportszone' ); ?>">
								<span class="sz-screen-reader-text"><?php esc_html_e( 'Star Conversation', 'sportszone' ); ?></span>
							</a>
						<# } #>

					<# } #>

					<a href="../view/{{data.id}}/" class="message-action-view sz-tooltip sz-icons" data-sz-action="view" data-sz-tooltip="<?php esc_attr_e( 'View full conversation and reply.', 'sportszone' ); ?>">
						<span class="sz-screen-reader-text"><?php esc_html_e( 'View full conversation and reply.', 'sportszone' ); ?></span>
					</a>

					<# if ( data.threadOptions ) { #>
						<span class="sz-messages-hook thread-options">
							{{{data.threadOptions}}}
						</span>
					<# } #>
				</div>
			</header>

			<div class='preview-message'>
				{{{data.content}}}
			</div>

			<# if ( data.inboxListItem ) { #>
				<table class="sz-messages-hook inbox-list-item">
					<tbody>
						<tr>{{{data.inboxListItem}}}</tr>
					</tbody>
				</table>
			<# } #>
		</div>
	<# } #>
</script>

<script type="text/html" id="tmpl-sz-messages-single-header">
	<h2 id="message-subject" class="message-title single-thread-title">{{{data.subject}}}</h2>
	<header class="single-message-thread-header">
		<# if ( undefined !== data.recipients ) { #>
			<dl class="thread-participants">
				<dt><?php esc_html_e( 'Participants:', 'sportszone' ); ?></dt>
				<dd>
					<ul class="participants-list">
						<# for ( i in data.recipients ) { #>
							<li><a href="{{data.recipients[i].user_link}}" class="sz-tooltip" data-sz-tooltip="{{data.recipients[i].user_name}}"><img class="avatar mini" src="{{data.recipients[i].avatar}}" alt="{{data.recipients[i].user_name}}" /></a></li>
						<# } #>
					</ul>
				</dd>
			</dl>
		<# } #>

		<div class="actions">
			<button type="button" class="message-action-delete sz-tooltip sz-icons" data-sz-action="delete" data-sz-tooltip="<?php esc_attr_e( 'Delete conversation.', 'sportszone' ); ?>">
				<span class="sz-screen-reader-text"><?php esc_html_e( 'Delete conversation.', 'sportszone' ); ?></span>
			</button>
		</div>
	</header>
</script>

<script type="text/html" id="tmpl-sz-messages-single-list">
	<div class="message-metadata">
		<# if ( data.beforeMeta ) { #>
			<div class="sz-messages-hook before-message-meta">{{{data.beforeMeta}}}</div>
		<# } #>

		<a href="{{data.sender_link}}" class="user-link">
			<img class="avatar" src="{{data.sender_avatar}}" alt="" />
			<strong>{{data.sender_name}}</strong>
		</a>

		<time datetime="{{data.date.toISOString()}}" class="activity">{{data.display_date}}</time>

		<div class="actions">
			<# if ( undefined !== data.star_link ) { #>

				<button type="button" class="message-action-unstar sz-tooltip sz-icons <# if ( false === data.is_starred ) { #>sz-hide<# } #>" data-sz-star-link="{{data.star_link}}" data-sz-action="unstar" data-sz-tooltip="<?php esc_attr_e( 'Unstar Message', 'sportszone' ); ?>">
					<span class="sz-screen-reader-text"><?php esc_html_e( 'Unstar Message', 'sportszone' ); ?></span>
				</button>

				<button type="button" class="message-action-star sz-tooltip sz-icons <# if ( false !== data.is_starred ) { #>sz-hide<# } #>" data-sz-star-link="{{data.star_link}}" data-sz-action="star" data-sz-tooltip="<?php esc_attr_e( 'Star Message', 'sportszone' ); ?>">
					<span class="sz-screen-reader-text"><?php esc_html_e( 'Star Message', 'sportszone' ); ?></span>
				</button>

			<# } #>
		</div>

		<# if ( data.afterMeta ) { #>
			<div class="sz-messages-hook after-message-meta">{{{data.afterMeta}}}</div>
		<# } #>
	</div>

	<# if ( data.beforeContent ) { #>
		<div class="sz-messages-hook before-message-content">{{{data.beforeContent}}}</div>
	<# } #>

	<div class="message-content">{{{data.content}}}</div>

	<# if ( data.afterContent ) { #>
		<div class="sz-messages-hook after-message-content">{{{data.afterContent}}}</div>
	<# } #>

</script>

<script type="text/html" id="tmpl-sz-messages-single">
	<?php sz_nouveau_messages_hook( 'before', 'thread_content' ); ?>

	<div id="sz-message-thread-header" class="message-thread-header"></div>

	<?php sz_nouveau_messages_hook( 'before', 'thread_list' ); ?>

	<ul id="sz-message-thread-list"></ul>

	<?php sz_nouveau_messages_hook( 'after', 'thread_list' ); ?>

	<?php sz_nouveau_messages_hook( 'before', 'thread_reply' ); ?>

	<form id="send-reply" class="standard-form send-reply">
		<div class="message-box">
			<div class="message-metadata">

				<?php sz_nouveau_messages_hook( 'before', 'reply_meta' ); ?>

				<div class="avatar-box">
					<?php sz_loggedin_user_avatar( 'type=thumb&height=30&width=30' ); ?>

					<strong><?php esc_html_e( 'Send a Reply', 'sportszone' ); ?></strong>
				</div>

				<?php sz_nouveau_messages_hook( 'after', 'reply_meta' ); ?>

			</div><!-- .message-metadata -->

			<div class="message-content">

				<?php sz_nouveau_messages_hook( 'before', 'reply_box' ); ?>

				<label for="message_content" class="sz-screen-reader-text"><?php _e( 'Reply to Message', 'sportszone' ); ?></label>
				<div id="sz-message-content"></div>

				<?php sz_nouveau_messages_hook( 'after', 'reply_box' ); ?>

				<div class="submit">
					<input type="submit" name="send" value="<?php echo esc_attr_x( 'Send Reply', 'button', 'sportszone' ); ?>" id="send_reply_button"/>
				</div>

			</div><!-- .message-content -->

		</div><!-- .message-box -->
	</form>

	<?php sz_nouveau_messages_hook( 'after', 'thread_reply' ); ?>

	<?php sz_nouveau_messages_hook( 'after', 'thread_content' ); ?>
</script>
