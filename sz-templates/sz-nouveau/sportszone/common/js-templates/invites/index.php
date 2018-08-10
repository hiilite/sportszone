<?php
/**
 * BP Nouveau Invites main template.
 *
 * This template is used to inject the SportsZone Backbone views
 * dealing with invites.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<?php if ( sz_is_group_create() ) : ?>

	<h3 class="sz-screen-title creation-step-name">
		<?php esc_html_e( 'Invite Members', 'sportszone' ); ?>
	</h3>

<?php else : ?>

	<h2 class="sz-screen-title">
		<?php esc_html_e( 'Invite Members', 'sportszone' ); ?>
	</h2>

<?php endif; ?>

<div id="group-invites-container">

	<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Group invitations menu', 'sportszone' ); ?>"></nav>

	<div class="group-invites-column">
		<div class="subnav-filters group-subnav-filters sz-invites-filters"></div>
		<div class="sz-invites-feedback"></div>
		<div class="members sz-invites-content"></div>
	</div>

</div>

<script type="text/html" id="tmpl-sz-group-invites-feedback">
	<div class="sz-feedback {{data.type}}">
		<span class="sz-icon" aria-hidden="true"></span>
		<p>{{{data.message}}}</p>
	</div>
</script>

<script type="text/html" id="tmpl-sz-invites-nav">
	<a href="{{data.href}}" class="sz-invites-nav-item" data-nav="{{data.id}}">{{data.name}}</a>
</script>

<script type="text/html" id="tmpl-sz-invites-users">
	<div class="item-avatar">
		<img src="{{data.avatar}}" class="avatar" alt="">
	</div>

	<div class="item">
		<div class="list-title member-name">
			{{data.name}}
		</div>

		<# if ( undefined !== data.is_sent ) { #>
			<div class="item-meta">

				<# if ( undefined !== data.invited_by ) { #>
					<ul class="group-inviters">
						<li><?php esc_html_e( 'Invited by:', 'sportszone' ); ?></li>
						<# for ( i in data.invited_by ) { #>
							<li><a href="{{data.invited_by[i].user_link}}" class="sz-tooltip" data-sz-tooltip="{{data.invited_by[i].user_name}}"><img src="{{data.invited_by[i].avatar}}" width="30px" class="avatar mini" alt="{{data.invited_by[i].user_name}}"></a></li>
						<# } #>
					</ul>
				<# } #>

				<p class="status">
					<# if ( false === data.is_sent ) { #>
						<?php esc_html_e( 'The invite has not been sent yet.', 'sportszone' ); ?>
					<# } else { #>
						<?php esc_html_e( 'The invite has been sent.', 'sportszone' ); ?>
					<# } #>
				</p>

			</div>
		<# } #>
	</div>

	<div class="action">
		<# if ( undefined === data.is_sent || ( false === data.is_sent && true === data.can_edit ) ) { #>
			<button type="button" class="button invite-button group-add-remove-invite-button sz-tooltip sz-icons<# if ( data.selected ) { #> selected<# } #>" data-sz-tooltip="<# if ( data.selected ) { #><?php esc_attr_e( 'Cancel invitation', 'sportszone' ); ?><# } else { #><?php echo esc_attr_x( 'Invite', 'button', 'sportszone' ); ?><# } #>">
				<span class="icons" aria-hidden="true"></span>
				<span class="sz-screen-reader-text">
					<# if ( data.selected ) { #>
						<?php echo esc_html_x( 'Cancel invitation', 'button', 'sportszone' ); ?>
					<# } else { #>
						<?php echo esc_html_x( 'Invite', 'button', 'sportszone' ); ?>
					<# } #>
				</span>
			</button>
		<# } #>

		<# if ( undefined !== data.can_edit && true === data.can_edit ) { #>
			<button type="button" class="button invite-button group-remove-invite-button sz-tooltip sz-icons" data-sz-tooltip="<?php echo esc_attr_x( 'Cancel invitation', 'button', 'sportszone' ); ?>">
				<span class=" icons" aria-hidden="true"></span>
				<span class="sz-screen-reader-text"><?php echo esc_attr_x( 'Cancel invitation', 'button', 'sportszone' ); ?></span>
			</button>
		<# } #>
	</div>

</script>

<script type="text/html" id="tmpl-sz-invites-selection">
	<a href="#uninvite-user-{{data.id}}" class="sz-tooltip" data-sz-tooltip="{{data.uninviteTooltip}}" aria-label="{{data.uninviteTooltip}}">
		<img src="{{data.avatar}}" class="avatar" alt=""/>
	</a>
</script>

<script type="text/html" id="tmpl-sz-invites-form">

	<label for="send-invites-control"><?php esc_html_e( 'Optional: add a message to your invite.', 'sportszone' ); ?></label>
	<textarea id="send-invites-control" class="sz-faux-placeholder-label"></textarea>

	<div class="action">
		<button type="button" id="sz-invites-reset" class="button sz-secondary-action"><?php echo esc_html_x( 'Cancel', 'button', 'sportszone' ); ?></button>
		<button type="button" id="sz-invites-send" class="button sz-primary-action"><?php echo esc_html_x( 'Send', 'button', 'sportszone' ); ?></button>
	</div>
</script>

<script type="text/html" id="tmpl-sz-invites-filters">
	<div class="group-invites-search subnav-search clearfix" role="search" >
		<div class="sz-search">
			<form action="" method="get" id="group_invites_search_form" class="sz-invites-search-form" data-sz-search="{{data.scope}}">
				<label for="group_invites_search" class="sz-screen-reader-text"><?php sz_nouveau_search_default_text( _x( 'Search Members', 'heading', 'sportszone' ), false ); ?></label>
				<input type="search" id="group_invites_search" placeholder="<?php echo esc_attr_x( 'Search', 'search placeholder text', 'sportszone' ); ?>"/>

				<button type="submit" id="group_invites_search_submit" class="nouveau-search-submit">
					<span class="dashicons dashicons-search" aria-hidden="true"></span>
					<span id="button-text" class="sz-screen-reader-text"><?php echo esc_html_x( 'Search', 'button', 'sportszone' ); ?></span>
				</button>
			</form>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-sz-invites-paginate">
	<# if ( 1 !== data.page ) { #>
		<a href="#previous-page" id="sz-invites-prev-page" class="button invite-button sz-tooltip" data-sz-tooltip="<?php echo esc_attr_x( 'Previous page', 'link', 'sportszone' ); ?>">
			<span class="dashicons dashicons-arrow-left" aria-hidden="true"></span>
			<span class="sz-screen-reader-text"><?php echo esc_html_x( 'Previous page', 'link', 'sportszone' ); ?></span>
		</a>
	<# } #>

	<# if ( data.total_page !== data.page ) { #>
		<a href="#next-page" id="sz-invites-next-page" class="button invite-button sz-tooltip" data-sz-tooltip="<?php echo esc_attr_x( 'Next page', 'link', 'sportszone' ); ?>">
			<span class="sz-screen-reader-text"><?php echo esc_html_x( 'Next page', 'link', 'sportszone' ); ?></span>
			<span class="dashicons dashicons-arrow-right" aria-hidden="true"></span>
		</button>
	<# } #>
</script>
