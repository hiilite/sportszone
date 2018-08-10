<?php
/**
 * SportsZone - Groups Create
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_groups_create_hook( 'before', 'page' ); ?>

	<h2 class="sz-subhead"><?php esc_html_e( 'Create A New Group', 'sportszone' ); ?></h2>

	<?php sz_nouveau_groups_create_hook( 'before', 'content_template' ); ?>

	<?php if ( 'group-invites' !== sz_get_groups_current_create_step() ) : ?>
		<form action="<?php sz_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">
	<?php else : ?>
		<div id="create-group-form" class="standard-form">
	<?php endif; ?>

		<?php sz_nouveau_groups_create_hook( 'before' ); ?>

		<?php sz_nouveau_template_notices(); ?>

		<div class="item-body" id="group-create-body">

			<nav class="<?php sz_nouveau_groups_create_steps_classes(); ?>" id="group-create-tabs" role="navigation" aria-label="<?php esc_attr_e( 'Group creation menu', 'sportszone' ); ?>">
				<ol class="group-create-buttons button-tabs">

					<?php sz_group_creation_tabs(); ?>

				</ol>
			</nav>

			<?php sz_nouveau_group_creation_screen(); ?>

		</div><!-- .item-body -->

		<?php sz_nouveau_groups_create_hook( 'after' ); ?>

	<?php if ( 'group-invites' !== sz_get_groups_current_create_step() ) : ?>
		</form><!-- #create-group-form -->
	<?php else : ?>
		</div><!-- #create-group-form -->
	<?php endif; ?>

	<?php sz_nouveau_groups_create_hook( 'after', 'content_template' ); ?>

<?php
sz_nouveau_groups_create_hook( 'after', 'page' );
