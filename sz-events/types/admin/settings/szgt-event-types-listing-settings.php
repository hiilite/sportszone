<?php
/**
 * Bp add event type search listing file.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  SZ_Add_Event_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $sz_evt_types;
$event_types = $sz_evt_types->event_types;
$spinner_src = includes_url( '/images/spinner.gif' );

// Search Event Types.
if ( filter_input( INPUT_POST, 'szgt-search', FILTER_SANITIZE_STRING ) !== null ) {
	$search_txt = sanitize_text_field( filter_input( INPUT_POST, 'event-type-search-input' ) );
	foreach ( $event_types as $key => $event_type ) {
		$name_pos = false;
		$slug_pos = false;
		$desc_pos = false;

		$name_pos = stripos( $event_type['name'], $search_txt );
		$slug_pos = stripos( $event_type['slug'], $search_txt );
		$desc_pos = stripos( $event_type['desc'], $search_txt );

		if ( false !== $name_pos || false !== $slug_pos || false !== $desc_pos ) {
			$result['event_types'][] = $event_type;
		}
	}
	if ( ! empty( $result['event_types'] ) ) {
		$event_types = $result['event_types'];
	}
}

$flag        = 0;
$szet_search = 'disabled';
if ( $event_types ) {
	$szet_search = '';
	$flag        = 1;
}
?>
<div class="wrap nosubsub">
	<form method="POST" action="">
		<p class="search-box">
			<label class="screen-reader-text" for="event-type-search-input"><?php esc_html_e( 'Search Event Types:', 'sz-add-event-types' ); ?></label>
			<input name="event-type-search-input" placeholder="<?php esc_html_e( 'Write here..', 'sz-add-event-types' ); ?>" type="text" <?php echo esc_attr( $szet_search ); ?> required value="<?php echo ( filter_input( INPUT_POST, 'event-type-search-input' ) ) ? esc_attr( $search_txt ) : ''; ?>">
			<input name="szgt-search" class="button button-secondary" value="<?php esc_html_e( 'Search', 'sz-add-event-types' ); ?>" type="submit" <?php echo esc_attr( $szet_search ); ?>>
		</p>
	</form>

	<div id="col-container" class="wp-clearfix">
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">
					<form action="" method="POST" enctype="multipart/form-data">
						<h2><?php esc_html_e( 'Add New Event Type', 'sz-add-event-types' ); ?></h2>
						<div class="form-field form-required term-name-wrap">
							<label for="event-type-name"><?php esc_html_e( 'Name', 'sz-add-event-types' ); ?></label>
							<input id="event-type-name" name="event-type-name" size="40" aria-required="true" type="text" required>
							<p><?php esc_html_e( 'The name is how it appears on your site.', 'sz-add-event-types' ); ?></p>
						</div>
						<div class="form-field term-slug-wrap">
							<label for="event-type-slug"><?php esc_html_e( 'Slug', 'sz-add-event-types' ); ?></label>
							<input name="event-type-slug" id="event-type-slug" size="40" type="text">
							<p><?php esc_html_e( 'The slug is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'sz-add-event-types' ); ?></p>
						</div>
						<div class="form-field term-description-wrap">
							<label for="event-type-description"><?php esc_html_e( 'Description', 'sz-add-event-types' ); ?></label>
							<textarea id="event-type-desc" name="event-type-desc" rows="5" cols="40"></textarea>
							<p><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.', 'sz-add-event-types' ); ?></p>
						</div>
						<p class="submit">
							<?php wp_nonce_field( 'szgt-event-types', 'szgt-add-event-types-nonce' ); ?>
							<input name="szgt-add-event-type" class="button button-primary" value="<?php esc_html_e( 'Add New Event Type', 'sz-add-event-types' ); ?>" type="submit">
						</p>
					</form>
				</div>
			</div>
		</div><!-- /col-left -->

		<div id="col-right">
			<div class="col-wrap">
				<div class="tablenav top">
					<div class="tablenav-pages one-page">
						<span class="displaying-num">
							<?php echo ( count( $event_types ) > 1 ) || empty( $event_types ) ? count( $event_types ) . ' items' : '1 item'; ?>
						</span>
					</div>
					<br class="clear">
				</div>
				<table class="wp-list-table widefat fixed striped event-types">
					<thead>
						<tr>
							<th scope="col" id="name" class="manage-column column-name column-primary sortable desc">
								<a href="javascript:void(0);">
									<span><?php esc_html_e( 'Name', 'sz-add-event-types' ); ?></span>
								</a>
							</th>
							<th scope="col" id="description" class="manage-column column-description sortable desc">

									<span><?php esc_html_e( 'Description', 'sz-add-event-types' ); ?></span>

							</th>
							<th scope="col" id="slug" class="manage-column column-slug sortable desc">
								<a href="javascript:void(0);">
									<span><?php esc_html_e( 'Slug', 'sz-add-event-types' ); ?></span>
								</a>
							</th>
						</tr>
					</thead>

					<tbody id="the-list" class="szgt-list">
						<?php if ( 0 === $flag ) { ?>
							<tr class="szgt-not-found">
								<td colspan="3"><?php esc_html_e( 'Event Types Not Found!', 'sz-add-event-types' ); ?></td>
							</tr>
						<?php } else { ?>
							<?php foreach ( $event_types as $event_type ) { ?>
								<tr class="szgt-<?php echo esc_attr( $event_type['slug'] ); ?>">
									<td class="name column-name has-row-actions column-primary">
										<strong>
											<a class="row-title" href="javascript:void(0);" id="name-<?php echo esc_attr( $event_type['slug'] ); ?>">
												<?php echo esc_html( $event_type['name'] ); ?>
											</a>
										</strong>
										<br>
										<div class="row-actions">
											<span class="edit">
												<a class="edit-szgt" href="javascript:void(0);" id="<?php echo esc_attr( $event_type['slug'] ); ?>">
													<?php esc_html_e( 'Edit', 'sz-add-event-types' ); ?>
												</a> |
											</span>
											<span class="delete">
												<a class="dlt-szgt" href="javascript:void(0);" id="<?php echo esc_attr( $event_type['slug'] ); ?>">
													<?php esc_html_e( 'Delete', 'sz-add-event-types' ); ?>
												</a>
											</span>
										</div>
									</td>
									<td class="column-description" id="desc-<?php echo esc_attr( $event_type['slug'] ); ?>"><?php echo esc_attr( $event_type['desc'] ); ?></td>
									<td class="column-slug" id="slug-<?php echo esc_attr( $event_type['slug'] ); ?>"><?php echo esc_attr( $event_type['slug'] ); ?></td>
									<!--<td class="column-posts">2</td>-->
								</tr>

								<!-- Row Editor -->

								<tr class="inline-edit-row szgt-editor" id="edit-szgt-<?php echo esc_attr( $event_type['slug'] ); ?>">
									<td colspan="3" class="colspanchange">
										<fieldset>
											<legend class="inline-edit-legend">
												<?php esc_html_e( 'Edit', 'sz-add-event-types' ); ?> <?php echo esc_attr( $event_type['name'] ); ?>
											</legend>
											<div class="inline-edit-col">
												<label>
													<span class="title"><?php esc_html_e( 'Name', 'sz-add-event-types' ); ?></span>
													<span class="input-text-wrap">
														<input id="<?php echo esc_attr( $event_type['slug'] ); ?>-name" class="ptitle" value="<?php echo esc_attr( $event_type['name'] ); ?>" type="text">
													</span>
												</label>
												<label>
													<span class="title"><?php esc_html_e( 'Slug', 'sz-add-event-types' ); ?></span>
													<span class="input-text-wrap">
														<input id="<?php echo esc_attr( $event_type['slug'] ); ?>-slug" class="ptitle" value="<?php echo esc_attr( $event_type['slug'] ); ?>" type="text">
													</span>
												</label>
												<label>
													<span class="title">
														<?php esc_html_e( 'Description', 'sz-add-event-types' ); ?>
													</span>
													<span class="input-text-wrap">
														<textarea id="<?php echo esc_attr( $event_type['slug'] ); ?>-desc"><?php echo esc_attr( $event_type['desc'] ); ?></textarea>
													</span>
												</label>
											</div>
										</fieldset>
										<p class="inline-edit-save submit">
											<input type="button" class="close button button-secondary alignleft" value="<?php esc_html_e( 'Cancel', 'sz-add-event-types' ); ?>">
											<input class="szgt-update button button-primary alignright" id="<?php echo esc_attr( $event_type['slug'] ); ?>" value="<?php esc_html_e( 'Update Event Type', 'sz-add-event-types' ); ?>" type="button">

											<span class="ajax-loader alignright" id="ajax-loader-for-<?php echo esc_attr( $event_type['slug'] ); ?>">
												<img src="<?php echo esc_url( $spinner_src ); ?>" alt="Loader" />
											</span>
											<br class="clear">
										</p>
									</td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div><!-- /col-right -->
	</div><!-- /col-container -->
</div>
