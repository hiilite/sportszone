<?php
/**
 * @package   HierarchicalEventsForSZ
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 */

class Hierarchical_Events_for_SZ extends SZ_Event_Extension {

	function __construct() {
		$nav_item_visibility = $this->nav_item_visibility();

		$args = array(
			'slug'              => hgsz_get_hierarchy_screen_slug(),
			'name'              => hgsz_get_hierarchy_nav_item_name(),
			'nav_item_position' => 61,
			'access'            => $nav_item_visibility,
			'show_tab'          => $nav_item_visibility,
			'screens'           => apply_filters( 'hgsz_event_extension_screens_param', array(
				'create' => array(
					'name' => _x( 'Hierarchy', 'Label for event management tab', 'hierarchical-events-for-sz' ),
				),
				'edit' => array(
					'name' => _x( 'Hierarchy', 'Label for event management tab', 'hierarchical-events-for-sz' ),
				),
				'admin' => array(
					'metabox_context' => 'side',
					'name' => _x( 'Hierarchy', 'Label for event management tab', 'hierarchical-events-for-sz' ),
				),
			) ),
		);
		parent::init( $args );
	}

	/**
	 * Output the code for the front-end screen for a single event.
	 *
	 * @since 1.0.0
	 */
	function display( $event_id = null ) {
		global $events_template;
		$parent_events_template = $events_template;

		/*
		 * events/single/subevents-loop is a shell that calls events-loop,
		 * to make it possible to override the subevents loop using the
		 * SportsZone template hierarchy.
		 * Note that events-loop will load events-loop-tree if
		 * 'use hierarchical template' is set to true.
		 */
		sz_get_template_part( 'events/single/subevents-loop' );

		/*
		 * Reset the $events_template global, so that the wrapper event
		 * is restored after the has_events() loop is completed.
		 */
		$events_template = $parent_events_template;
	}

	/**
	 * Output the code for the settings screen, the create step form
	 * and the wp-admin single event edit screen meta box.
	 *
	 * @since 1.0.0
	 */
	function settings_screen( $event_id = null ) {
		// On the create screen, the event_id isn't passed reliably.
		if ( empty( $event_id ) && ! empty( $_COOKIE['sz_new_event_id'] ) ) {
			$event_id = (int) $_COOKIE['sz_new_event_id'];
		}
		?>
		<label class="emphatic" for="parent-id"><?php _ex( 'Parent Event', 'Label for the parent event select on a single event manage screen', 'hierarchical-events-for-sz' ); ?></label>
		<?php
		$current_parent_event_id = hgsz_get_parent_event_id( $event_id );
		$possible_parent_events = hgsz_get_possible_parent_events( $event_id, sz_loggedin_user_id() );

		if ( ! $current_parent_event_id ) :
			?>
			<p class="info"><?php _e( 'This event is currently a top-level event.', 'hierarchical-events-for-sz' ); ?></p>
			<?php
		else :
			$parent_event = events_get_event( $current_parent_event_id );
			// The parent event could be a hidden event, so the current user may not be able to know about it. :\
			if ( 'hidden' == sz_get_event_status( $parent_event ) && ! events_is_user_member( sz_loggedin_user_id(), $parent_event->id ) ) :
				$current_parent_event_id = 'hidden-from-user';
				?>
				<p class="info"><?php _e( 'This event&rsquo;s current parent event is a hidden event, and you are not a member of that event.', 'hierarchical-events-for-sz' ); ?></p>
				<?php
			else :
				?>
				<p class="info"><?php esc_html( printf( __( 'This event&rsquo;s current parent event is %s.', 'hierarchical-events-for-sz' ), sz_get_event_name( $parent_event ) ) ); ?></p>
				<?php
			endif;
		endif; ?>
			<select id="parent-id" name="parent-id" autocomplete="off">
				<option value="no-change" <?php selected( 'hidden-from-user', $current_parent_event_id ); ?>><?php echo _x( 'Keep current parent event', 'The option to keep the current parent.', 'hierarchical-events-for-sz' ); ?></option>
				<option value="0" <?php selected( 0, $current_parent_event_id ); ?>><?php echo _x( 'No parent event', 'The option that sets a event to be a top-level event and have no parent.', 'hierarchical-events-for-sz' ); ?></option>
			<?php
			if ( $possible_parent_events ) {

				foreach ( $possible_parent_events as $possible_parent_event ) {
					?>
					<option value="<?php echo $possible_parent_event->id; ?>" <?php selected( $current_parent_event_id, $possible_parent_event->id ); ?>><?php echo esc_html( $possible_parent_event->name ); ?></option>
					<?php
				}
			}
			?>
			</select>
			<?php
		?>

		<fieldset class="hierarchy-allowed-subevent-creators radio">

			<legend><?php _e( 'Who is allowed to select this event as the parent event of another event?', 'hierarchical-events-for-sz' ); ?></legend>

			<?php
			$subevent_creators = hgsz_get_allowed_subevent_creators( $event_id );
			/*
			 * Don't include the loggedin option if this event is hidden--
			 * you have to be a member to even know about hidden events.
			 */
			if ( 'hidden' != sz_get_event_status( events_get_event( $event_id ) ) ) : ?>
				<label for="allowed-subevent-creators-loggedin"><input type="radio" name="allowed-subevent-creators" id="allowed-subevent-creators-loggedin" value="loggedin" <?php checked( $subevent_creators, 'loggedin' ); ?> /> <?php _e( 'Any logged-in site member', 'hierarchical-events-for-sz' ); ?></label>
			<?php endif; ?>

			<label for="allowed-subevent-creators-members"><input type="radio" name="allowed-subevent-creators" id="allowed-subevent-creators-members" value="member" <?php checked( $subevent_creators, 'member' ); ?> /> <?php _e( 'All event members', 'hierarchical-events-for-sz' ); ?></label>

			<label for="allowed-subevent-creators-mods"><input type="radio" name="allowed-subevent-creators" id="allowed-subevent-creators-mods" value="mod" <?php checked( $subevent_creators, 'mod' ); ?> /> <?php _e( 'Event admins and mods only', 'hierarchical-events-for-sz' ); ?></label>

			<label for="allowed-subevent-creators-admins"><input type="radio" name="allowed-subevent-creators" id="allowed-subevent-creators-admins" value="admin" <?php checked( $subevent_creators, 'admin' ); ?> /> <?php _e( 'Event admins only', 'hierarchical-events-for-sz' ); ?></label>

			<label for="allowed-subevent-creators-noone"><input type="radio" name="allowed-subevent-creators" id="allowed-subevent-creators-noone" value="noone" <?php checked( $subevent_creators, 'noone' ); ?> /> <?php _e( 'No one', 'hierarchical-events-for-sz' ); ?></label>
		</fieldset>

			<?php
			// Only display the syndication sections if the current user can change it.
			if ( sz_current_user_can( 'hgsz_change_include_activity' ) ) :
				$setting = events_get_eventmeta( $event_id, 'hgsz-include-activity-from-relatives' );
				if ( ! $setting ) {
					$setting = 'inherit';
				}
			?>
				<fieldset class="hierarchy-syndicate-activity radio">
					<legend><?php _e( 'Include activity from parent and child events in this event&rsquo;s activity stream.', 'hierarchical-events-for-sz' ); ?></legend>

					<label for="include-activity-from-parents"><input type="radio" id="include-activity-from-parents" name="hgsz-include-activity-from-relatives" value="include-from-parents"<?php checked( 'include-from-parents', $setting ); ?>> <?php _e( 'Include parent event activity.', 'hierarchical-events-for-sz' ); ?></label>

					<label for="include-activity-from-children"><input type="radio" id="include-activity-from-children" name="hgsz-include-activity-from-relatives" value="include-from-children"<?php checked( 'include-from-children', $setting ); ?>> <?php _e( 'Include child event activity.', 'hierarchical-events-for-sz' ); ?></label>

					<label for="include-activity-from-both"><input type="radio" id="include-activity-from-both" name="hgsz-include-activity-from-relatives" value="include-from-both"<?php checked( 'include-from-both', $setting ); ?>> <?php _e( 'Include parent and child event activity.', 'hierarchical-events-for-sz' ); ?></label>

					<label for="include-activity-from-none"><input type="radio" id="include-activity-from-none" name="hgsz-include-activity-from-relatives" value="include-from-none"<?php checked( 'include-from-none', $setting ); ?>> <?php _e( 'Do not include related event activity.', 'hierarchical-events-for-sz' ); ?></label>

					<label for="hgsz-include-activity-from-relatives-inherit"><input type="radio" name="hgsz-include-activity-from-relatives" id="hgsz-include-activity-from-relatives-inherit" value="inherit" <?php checked( 'inherit', $setting ); ?> /> <?php _e( 'Inherit global setting.', 'hierarchical-events-for-sz' ); ?></label>
				</fieldset>
			<?php endif; ?>
	<?php
	}

	/**
	 * Save parent association and subevent creators set on settings screen.
	 *
	 * @since 1.0.0
	 */
	function settings_screen_save( $event_id = null ) {
		$event_object = events_get_event( $event_id );

		// Save parent ID. Do nothing if value passed is "no-change".
		if ( isset( $_POST['parent-id'] ) && 'no-change' != $_POST['parent-id'] ) {
			$parent_id = $_POST['parent-id'] ? (int) $_POST['parent-id'] : 0;

			if ( $event_object->parent_id != $parent_id ) {
				$event_object->parent_id = $parent_id;
				$event_object->save();
			}
		}

		$allowed_creators = isset( $_POST['allowed-subevent-creators'] ) ? $_POST['allowed-subevent-creators'] : '';
		$allowed_creators = hgsz_sanitize_subevent_creators_setting( $allowed_creators );
		$subevent_creators = events_update_eventmeta( $event_id, 'hgsz-allowed-subevent-creators', $allowed_creators );

		// Syndication settings.
		if ( isset( $_POST['hgsz-include-activity-from-relatives'] ) ) {
			if ( 'inherit' == $_POST['hgsz-include-activity-from-relatives'] ) {
				// If "inherit", delete the event meta.
				$success = events_delete_eventmeta( $event_id, 'hgsz-include-activity-from-relatives' );
			} else {
				$setting = hgsz_sanitize_include_setting( $_POST['hgsz-include-activity-from-relatives'] );
				$success = events_update_eventmeta( $event_id, 'hgsz-include-activity-from-relatives', $setting );
			}
		}
	}

	/**
	 * Determine whether the event nav item should show up for the current user.
	 *
	 * @since 1.0.0
	 */
	function nav_item_visibility() {
		$nav_item_vis = 'noone';
		$event_id     = sz_get_current_event_id();

		// The nav item should only be enabled when the events loop would return subevents.
		if ( $event_id && ( hgsz_event_has_children( $event_id, sz_loggedin_user_id(), 'exclude_hidden' ) || hgsz_get_parent_event_id( $event_id, sz_loggedin_user_id(), 'normal' ) ) ) {
			// If this event is hidden, make the tab visible to members only.
			if ( 'hidden' == sz_get_event_status( events_get_event( $event_id ) ) ) {
				$nav_item_vis = 'member';
			} else {
				// Else, anyone can see how public and private events are related.
				$nav_item_vis = 'anyone';
			}
		}

		/**
		 * Fires before the calculated navigation item visibility is passed back to the constructor.
		 *
		 * @since 1.0.0
		 *
		 * @param string $nav_item_vis Access and visibility level.
		 * @param int    $event_id     ID of the current event.
		 */
		return apply_filters( 'hgsz_nav_item_visibility', $nav_item_vis, $event_id );
	}

}
sz_register_event_extension( 'Hierarchical_Events_for_SZ' );