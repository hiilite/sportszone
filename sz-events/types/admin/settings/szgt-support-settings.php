<?php
/**
 * Bp add event type support file.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  SZ_Add_Event_Types
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="szgt-adming-setting">
	<div class="szgt-tab-header"><h3><?php esc_html_e( 'Have some questions?', 'sz-add-event-types' ); ?></h3></div>

	<div class="szgt-admin-settings-block">
		<div id="szgt-settings-tbl">
			<div class="szgt-admin-row">
				<div>
					<button class="szgt-accordion"><?php esc_html_e( 'What plugin does this plugin require?', 'sz-add-event-types' ); ?></button>
					<div class="szgt-panel">
						<p><?php esc_html_e( 'As the name of the plugin justifies, this plugin helps create Event Types for SportsZone Events, this plugin requires SportsZone plugin to be installed and active.', 'sz-add-event-types' ); ?></p>
						<p><?php esc_html_e( 'You\'ll also get an admin notice and the plugin will become ineffective if the required plugin will not be there.', 'sz-add-event-types' ); ?></p>
					</div>
				</div>
			</div>
			<div class="szgt-admin-row">
				<div>
					<button class="szgt-accordion">
					<?php
					esc_html_e(
						'How does
                    Pre-select Event Types setting work provided in general setting section?', 'sz-add-event-types'
					);
?>
</button>
					<div class="szgt-panel">
						<p><?php esc_html_e( 'This setting will pre-select all the created event type while creating a new event.', 'sz-add-event-types' ); ?></p>
					</div>
				</div>
			</div>
			<div class="szgt-admin-row">
				<div>
					<button class="szgt-accordion">
					<?php
					esc_html_e(
						'How does
                    Enable Event Type Search setting work provided in general setting section?', 'sz-add-event-types'
					);
?>
</button>
					<div class="szgt-panel">
						<p><?php esc_html_e( 'This setting provide filter at domain.com/events page for searching events by created event types.', 'sz-add-event-types' ); ?></p>
					</div>
				</div>
			</div>
			<div class="szgt-admin-row">
				<div>
					<button class="szgt-accordion"><?php esc_html_e( 'How to a create event type?', 'sz-add-event-types' ); ?></button>
					<div class="szgt-panel">
						<p><?php esc_html_e( 'A new event type can be created with help of interface provided at plugin settings page under Event Types tab section.', 'sz-add-event-types' ); ?></p>
					</div>
				</div>
			</div>
			<div class="szgt-admin-row">
				<div>
					<button class="szgt-accordion"><?php esc_html_e( 'How to go for any custom development?', 'sz-add-event-types' ); ?></button>
					<div class="szgt-panel">
						<p><?php esc_html_e( 'If you need additional help you can contact us for <a href="https://wbcomdesigns.com/contact/" target="_blank" title="Custom Development by Wbcom Designs">Custom Development</a>.', 'sz-add-event-types' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
