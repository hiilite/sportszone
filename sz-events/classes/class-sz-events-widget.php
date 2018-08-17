<?php
/**
 * SportsZone Events Widget.
 *
 * @package SportsZone
 * @subpackage EventsWidgets
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Events widget.
 *
 * @since 1.0.3
 */
class SZ_Events_Widget extends WP_Widget {

	/**
	 * Working as a event, we get things done better.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		$widget_ops = array(
			'description'                 => __( 'A dynamic list of recently active, popular, newest, or alphabetical events', 'sportszone' ),
			'classname'                   => 'widget_sz_events_widget sportszone widget',
			'customize_selective_refresh' => true,
		);
		parent::__construct( false, _x( '(SportsZone) Events', 'widget name', 'sportszone' ), $widget_ops );

		if ( is_customize_preview() || is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'sz_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 2.6.0
	 */
	public function enqueue_scripts() {
		$min = sz_core_get_minified_asset_suffix();
		wp_enqueue_script( 'events_widget_events_list-js', sportszone()->plugin_url . "sz-events/js/widget-events{$min}.js", array( 'jquery' ), sz_get_version() );
	}

	/**
	 * Extends our front-end output method.
	 *
	 * @since 1.0.3
	 *
	 * @param array $args     Array of arguments for the widget.
	 * @param array $instance Widget instance data.
	 */
	public function widget( $args, $instance ) {
		global $events_template;

		/**
		 * Filters the user ID to use with the widget instance.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value Empty user ID.
		 */
		$user_id = apply_filters( 'sz_event_widget_user_id', '0' );

		extract( $args );

		if ( empty( $instance['event_default'] ) ) {
			$instance['event_default'] = 'popular';
		}

		if ( empty( $instance['title'] ) ) {
			$instance['title'] = __( 'Events', 'sportszone' );
		}

		/**
		 * Filters the title of the Events widget.
		 *
		 * @since 1.8.0
		 * @since 2.3.0 Added 'instance' and 'id_base' to arguments passed to filter.
		 *
		 * @param string $title    The widget title.
		 * @param array  $instance The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		/**
		 * Filters the separator of the event widget links.
		 *
		 * @since 2.4.0
		 *
		 * @param string $separator Separator string. Default '|'.
		 */
		$separator = apply_filters( 'sz_events_widget_separator', '|' );

		echo $before_widget;

		$title = ! empty( $instance['link_title'] ) ? '<a href="' . sz_get_events_directory_permalink() . '">' . $title . '</a>' : $title;

		echo $before_title . $title . $after_title;

		$max_events = ! empty( $instance['max_events'] ) ? (int) $instance['max_events'] : 5;

		$event_args = array(
			'user_id'         => $user_id,
			'type'            => $instance['event_default'],
			'per_page'        => $max_events,
			'max'             => $max_events,
		);

		// Back up the global.
		$old_events_template = $events_template;

		?>

		<?php if ( sz_has_events( $event_args ) ) : ?>
			<div class="item-options" id="events-list-options">
				<a href="<?php sz_events_directory_permalink(); ?>" id="newest-events"<?php if ( $instance['event_default'] == 'newest' ) : ?> class="selected"<?php endif; ?>><?php _e("Newest", 'sportszone') ?></a>
				<span class="sz-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
				<a href="<?php sz_events_directory_permalink(); ?>" id="recently-active-events"<?php if ( $instance['event_default'] == 'active' ) : ?> class="selected"<?php endif; ?>><?php _e("Active", 'sportszone') ?></a>
				<span class="sz-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
				<a href="<?php sz_events_directory_permalink(); ?>" id="popular-events" <?php if ( $instance['event_default'] == 'popular' ) : ?> class="selected"<?php endif; ?>><?php _e("Popular", 'sportszone') ?></a>
				<span class="sz-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
				<a href="<?php sz_events_directory_permalink(); ?>" id="alphabetical-events" <?php if ( $instance['event_default'] == 'alphabetical' ) : ?> class="selected"<?php endif; ?>><?php _e("Alphabetical", 'sportszone') ?></a>
			</div>

			<ul id="events-list" class="item-list" aria-live="polite" aria-relevant="all" aria-atomic="true">
				<?php while ( sz_events() ) : sz_the_event(); ?>
					<li <?php sz_event_class(); ?>>
						<div class="item-avatar">
							<a href="<?php sz_event_permalink() ?>" class="sz-tooltip" data-sz-tooltip="<?php sz_event_name() ?>"><?php sz_event_avatar_thumb() ?></a>
						</div>

						<div class="item">
							<div class="item-title"><?php sz_event_link(); ?></div>
							<div class="item-meta">
								<span class="activity">
								<?php
									if ( 'newest' == $instance['event_default'] ) {
										printf( __( 'created %s', 'sportszone' ), sz_get_event_date_created() );
									} elseif ( 'popular' == $instance['event_default'] ) {
										sz_event_member_count();
									} else {
										printf( __( 'active %s', 'sportszone' ), sz_get_event_last_active() );
									}
								?>
								</span>
							</div>
						</div>
					</li>

				<?php endwhile; ?>
			</ul>
			<?php wp_nonce_field( 'events_widget_events_list', '_wpnonce-events' ); ?>
			<input type="hidden" name="events_widget_max" id="events_widget_max" value="<?php echo esc_attr( $max_events ); ?>" />

		<?php else: ?>

			<div class="widget-error">
				<?php _e('There are no events to display.', 'sportszone') ?>
			</div>

		<?php endif; ?>

		<?php echo $after_widget;

		// Restore the global.
		$events_template = $old_events_template;
	}

	/**
	 * Extends our update method.
	 *
	 * @since 1.0.3
	 *
	 * @param array $new_instance New instance data.
	 * @param array $old_instance Original instance data.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']         = strip_tags( $new_instance['title'] );
		$instance['max_events']    = strip_tags( $new_instance['max_events'] );
		$instance['event_default'] = strip_tags( $new_instance['event_default'] );
		$instance['link_title']    = (bool) $new_instance['link_title'];

		return $instance;
	}

	/**
	 * Extends our form method.
	 *
	 * @since 1.0.3
	 *
	 * @param array $instance Current instance.
	 * @return mixed
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'         => __( 'Events', 'sportszone' ),
			'max_events'    => 5,
			'event_default' => 'active',
			'link_title'    => false
		);
		$instance = sz_parse_args( (array) $instance, $defaults, 'events_widget_form' );

		$title 	       = strip_tags( $instance['title'] );
		$max_events    = strip_tags( $instance['max_events'] );
		$event_default = strip_tags( $instance['event_default'] );
		$link_title    = (bool) $instance['link_title'];
		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'sportszone'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

		<p><label for="<?php echo $this->get_field_id('link_title') ?>"><input type="checkbox" name="<?php echo $this->get_field_name('link_title') ?>" id="<?php echo $this->get_field_id('link_title') ?>" value="1" <?php checked( $link_title ) ?> /> <?php _e( 'Link widget title to Events directory', 'sportszone' ) ?></label></p>

		<p><label for="<?php echo $this->get_field_id( 'max_events' ); ?>"><?php _e('Max events to show:', 'sportszone'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_events' ); ?>" name="<?php echo $this->get_field_name( 'max_events' ); ?>" type="text" value="<?php echo esc_attr( $max_events ); ?>" style="width: 30%" /></label></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'event_default' ); ?>"><?php _e('Default events to show:', 'sportszone'); ?></label>
			<select name="<?php echo $this->get_field_name( 'event_default' ); ?>" id="<?php echo $this->get_field_id( 'event_default' ); ?>">
				<option value="newest" <?php selected( $event_default, 'newest' ); ?>><?php _e( 'Newest', 'sportszone' ) ?></option>
				<option value="active" <?php selected( $event_default, 'active' ); ?>><?php _e( 'Active', 'sportszone' ) ?></option>
				<option value="popular"  <?php selected( $event_default, 'popular' ); ?>><?php _e( 'Popular', 'sportszone' ) ?></option>
				<option value="alphabetical" <?php selected( $event_default, 'alphabetical' ); ?>><?php _e( 'Alphabetical', 'sportszone' ) ?></option>
			</select>
		</p>
	<?php
	}
}
