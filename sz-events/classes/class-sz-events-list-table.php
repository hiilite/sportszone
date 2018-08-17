<?php
/**
 * SportsZone Events admin list table class.
 *
 * Props to WordPress core for the Comments admin screen, and its contextual
 * help text, on which this implementation is heavily based.
 *
 * @package SportsZone
 * @subpackage Events
 * @since 1.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * List table class for the Events component admin page.
 *
 * @since 1.7.0
 */
class SZ_Events_List_Table extends WP_List_Table {

	/**
	 * The type of view currently being displayed.
	 *
	 * E.g. "All", "Pending", "Approved", "Spam"...
	 *
	 * @since 1.7.0
	 * @var string
	 */
	public $view = 'all';

	/**
	 * Event counts for each event type.
	 *
	 * @since 1.7.0
	 * @var int
	 */
	public $event_counts = 0;

	/**
	 * Multidimensional array of event visibility (status) types and their events.
	 *
	 * @link https://sportszone.trac.wordpress.org/ticket/6277
	 * @var array
	 */
	public $event_type_ids = array();

	/**
	 * Constructor
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		// Define singular and plural labels, as well as whether we support AJAX.
		parent::__construct( array(
			'ajax'     => false,
			'plural'   => 'events',
			'singular' => 'event',
		) );

		// Add Event Type column and bulk change controls.
		if ( sz_events_get_event_types() ) {
			// Add Event Type column.
			add_filter( 'sz_events_list_table_get_columns',        array( $this, 'add_type_column' )                  );
			add_filter( 'sz_events_admin_get_event_custom_column', array( $this, 'column_content_event_type' ), 10, 3 );
			// Add the bulk change select.
			add_action( 'sz_events_list_table_after_bulk_actions', array( $this, 'add_event_type_bulk_change_select' ) );
		}
	}

	/**
	 * Set up items for display in the list table.
	 *
	 * Handles filtering of data, sorting, pagination, and any other data
	 * manipulation required prior to rendering.
	 *
	 * @since 1.7.0
	 */
	public function prepare_items() {
		global $events_template;

		$screen = get_current_screen();

		// Option defaults.
		$include_id   = false;
		$search_terms = false;

		// Set current page.
		$page = $this->get_pagenum();

		// Set per page from the screen options.
		$per_page = $this->get_items_per_page( str_replace( '-', '_', "{$screen->id}_per_page" ) );

		// Sort order.
		$order = 'DESC';
		if ( !empty( $_REQUEST['order'] ) ) {
			$order = ( 'desc' == strtolower( $_REQUEST['order'] ) ) ? 'DESC' : 'ASC';
		}

		// Order by - default to newest.
		$orderby = 'last_activity';
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			switch ( $_REQUEST['orderby'] ) {
				case 'name' :
					$orderby = 'name';
					break;
				case 'id' :
					$orderby = 'date_created';
					break;
				case 'members' :
					$orderby = 'total_member_count';
					break;
				case 'last_active' :
					$orderby = 'last_activity';
					break;
			}
		}

		// Are we doing a search?
		if ( !empty( $_REQUEST['s'] ) )
			$search_terms = $_REQUEST['s'];

		// Check if user has clicked on a specific event (if so, fetch only that event).
		if ( !empty( $_REQUEST['gid'] ) )
			$include_id = (int) $_REQUEST['gid'];

		// Set the current view.
		if ( isset( $_GET['event_status'] ) && in_array( $_GET['event_status'], array( 'public', 'private', 'hidden' ) ) ) {
			$this->view = $_GET['event_status'];
		}

		// We'll use the ids of event status types for the 'include' param.
		$this->event_type_ids = SZ_Events_Event::get_event_type_ids();

		// Pass a dummy array if there are no events of this type.
		$include = false;
		if ( 'all' != $this->view && isset( $this->event_type_ids[ $this->view ] ) ) {
			$include = ! empty( $this->event_type_ids[ $this->view ] ) ? $this->event_type_ids[ $this->view ] : array( 0 );
		}

		// Get event type counts for display in the filter tabs.
		$this->event_counts = array();
		foreach ( $this->event_type_ids as $event_type => $event_ids ) {
			$this->event_counts[ $event_type ] = count( $event_ids );
		}

		// Event types
		$event_type = false;
		if ( isset( $_GET['sz-event-type'] ) && null !== sz_events_get_event_type_object( $_GET['sz-event-type'] ) ) {
			$event_type = $_GET['sz-event-type'];
		}

		// If we're viewing a specific event, flatten all activities into a single array.
		if ( $include_id ) {
			$events = array( (array) events_get_event( $include_id ) );
		} else {
			$events_args = array(
				'include'  => $include,
				'per_page' => $per_page,
				'page'     => $page,
				'orderby'  => $orderby,
				'order'    => $order
			);

			if ( $event_type ) {
				$events_args['event_type'] = $event_type;
			}

			$events = array();
			if ( sz_has_events( $events_args ) ) {
				while ( sz_events() ) {
					sz_the_event();
					$events[] = (array) $events_template->event;
				}
			}
		}

		// Set raw data to display.
		$this->items = $events;

		// Store information needed for handling table pagination.
		$this->set_pagination_args( array(
			'per_page'    => $per_page,
			'total_items' => $events_template->total_event_count,
			'total_pages' => ceil( $events_template->total_event_count / $per_page )
		) );
	}

	/**
	 * Get an array of all the columns on the page.
	 *
	 * @since 1.7.0
	 *
	 * @return array Array of column headers.
	 */
	public function get_column_info() {
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
			$this->get_default_primary_column_name(),
		);

		return $this->_column_headers;
	}

	/**
	 * Get name of default primary column
	 *
	 * @since 2.3.3
	 *
	 * @return string
	 */
	protected function get_default_primary_column_name() {
		// Comment column is mapped to Event's name.
		return 'comment';
	}

	/**
	 * Display a message on screen when no items are found ("No events found").
	 *
	 * @since 1.7.0
	 */
	public function no_items() {
		_e( 'No events found.', 'sportszone' );
	}

	/**
	 * Output the Events data table.
	 *
	 * @since 1.7.0
	 */
	public function display() {
		$this->display_tablenav( 'top' ); ?>

		<h2 class="screen-reader-text"><?php
			/* translators: accessibility text */
			_e( 'Events list', 'sportszone' );
		?></h2>

		<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>

			<tbody id="the-comment-list">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

			<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
			</tfoot>
		</table>
		<?php

		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 2.7.0
	 * @access protected
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		/**
		 * Fires just after the bulk action controls in the WP Admin events list table.
		 *
		 * @since 2.7.0
		 *
		 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
		 */
		do_action( 'sz_events_list_table_after_bulk_actions', $which );
	}

	/**
	 * Generate content for a single row of the table.
	 *
	 * @since 1.7.0
	 *
	 * @param object|array $item The current event item in the loop.
	 */
	public function single_row( $item = array() ) {
		static $even = false;

		$row_classes = array();

		if ( $even ) {
			$row_classes = array( 'even' );
		} else {
			$row_classes = array( 'alternate', 'odd' );
		}

		/**
		 * Filters the classes applied to a single row in the events list table.
		 *
		 * @since 1.9.0
		 *
		 * @param array  $row_classes Array of classes to apply to the row.
		 * @param string $value       ID of the current event being displayed.
		 */
		$row_classes = apply_filters( 'sz_events_admin_row_class', $row_classes, $item['id'] );
		$row_class = ' class="' . implode( ' ', $row_classes ) . '"';

		echo '<tr' . $row_class . ' id="event-' . esc_attr( $item['id'] ) . '" data-parent_id="' . esc_attr( $item['id'] ) . '" data-root_id="' . esc_attr( $item['id'] ) . '">';
		echo $this->single_row_columns( $item );
		echo '</tr>';

		$even = ! $even;
	}

	/**
	 * Get the list of views available on this table (e.g. "all", "public").
	 *
	 * @since 1.7.0
	 */
	public function get_views() {
		$url_base = sz_get_admin_url( 'admin.php?page=sz-events' ); ?>

		<h2 class="screen-reader-text"><?php
			/* translators: accessibility text */
			_e( 'Filter events list', 'sportszone' );
		?></h2>

		<ul class="subsubsub">
			<li class="all"><a href="<?php echo esc_url( $url_base ); ?>" class="<?php if ( 'all' == $this->view ) echo 'current'; ?>"><?php _e( 'All', 'sportszone' ); ?></a> |</li>
			<li class="public"><a href="<?php echo esc_url( add_query_arg( 'event_status', 'public', $url_base ) ); ?>" class="<?php if ( 'public' == $this->view ) echo 'current'; ?>"><?php printf( _n( 'Public <span class="count">(%s)</span>', 'Public <span class="count">(%s)</span>', $this->event_counts['public'], 'sportszone' ), number_format_i18n( $this->event_counts['public'] ) ); ?></a> |</li>
			<li class="private"><a href="<?php echo esc_url( add_query_arg( 'event_status', 'private', $url_base ) ); ?>" class="<?php if ( 'private' == $this->view ) echo 'current'; ?>"><?php printf( _n( 'Private <span class="count">(%s)</span>', 'Private <span class="count">(%s)</span>', $this->event_counts['private'], 'sportszone' ), number_format_i18n( $this->event_counts['private'] ) ); ?></a> |</li>
			<li class="hidden"><a href="<?php echo esc_url( add_query_arg( 'event_status', 'hidden', $url_base ) ); ?>" class="<?php if ( 'hidden' == $this->view ) echo 'current'; ?>"><?php printf( _n( 'Hidden <span class="count">(%s)</span>', 'Hidden <span class="count">(%s)</span>', $this->event_counts['hidden'], 'sportszone' ), number_format_i18n( $this->event_counts['hidden'] ) ); ?></a></li>

			<?php

			/**
			 * Fires inside listing of views so plugins can add their own.
			 *
			 * @since 1.7.0
			 *
			 * @param string $url_base Current URL base for view.
			 * @param string $view     Current view being displayed.
			 */
			do_action( 'sz_events_list_table_get_views', $url_base, $this->view ); ?>
		</ul>
	<?php
	}

	/**
	 * Get bulk actions for single event row.
	 *
	 * @since 1.7.0
	 *
	 * @return array Key/value pairs for the bulk actions dropdown.
	 */
	public function get_bulk_actions() {

		/**
		 * Filters the list of bulk actions to display on a single event row.
		 *
		 * @since 1.7.0
		 *
		 * @param array $value Array of bulk actions to display.
		 */
		return apply_filters( 'sz_events_list_table_get_bulk_actions', array(
			'delete' => __( 'Delete', 'sportszone' )
		) );
	}

	/**
	 * Get the table column titles.
	 *
	 * @since 1.7.0
	 *
	 * @see WP_List_Table::single_row_columns()
	 *
	 * @return array Array of column titles.
	 */
	public function get_columns() {

		/**
		 * Filters the titles for the columns for the events list table.
		 *
		 * @since 2.0.0
		 *
		 * @param array $value Array of slugs and titles for the columns.
		 */
		return apply_filters( 'sz_events_list_table_get_columns', array(
			'cb'          => '<input name type="checkbox" />',
			'comment'     => _x( 'Name', 'Events admin Event Name column header',               'sportszone' ),
			'description' => _x( 'Description', 'Events admin Event Description column header', 'sportszone' ),
			'status'      => _x( 'Status', 'Events admin Privacy Status column header',         'sportszone' ),
			'members'     => _x( 'Members', 'Events admin Members column header',               'sportszone' ),
			'last_active' => _x( 'Last Active', 'Events admin Last Active column header',       'sportszone' )
		) );
	}

	/**
	 * Get the column names for sortable columns.
	 *
	 * Note: It's not documented in WP, but the second item in the
	 * nested arrays below is $desc_first. Normally, we would set
	 * last_active to be desc_first (since you're generally interested in
	 * the *most* recently active event, not the *least*). But because
	 * the default sort for the Events admin screen is DESC by last_active,
	 * we want the first click on the Last Active column header to switch
	 * the sort order - ie, to make it ASC. Thus last_active is set to
	 * $desc_first = false.
	 *
	 * @since 1.7.0
	 *
	 * @return array Array of sortable column names.
	 */
	public function get_sortable_columns() {
		return array(
			'gid'         => array( 'gid', false ),
			'comment'     => array( 'name', false ),
			'members'     => array( 'members', false ),
			'last_active' => array( 'last_active', false ),
		);
	}

	/**
	 * Override WP_List_Table::row_actions().
	 *
	 * Basically a duplicate of the row_actions() method, but removes the
	 * unnecessary <button> addition.
	 *
	 * @since 2.3.3
	 * @since 2.3.4 Visibility set to public for compatibility with WP < 4.0.0.
	 *
	 * @param array $actions        The list of actions.
	 * @param bool  $always_visible Whether the actions should be always visible.
	 * @return string
	 */
	public function row_actions( $actions, $always_visible = false ) {
		$action_count = count( $actions );
		$i = 0;

		if ( !$action_count )
			return '';

		$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		return $out;
	}

	/**
	 * Markup for the Checkbox column.
	 *
	 * @since 1.7.0
	 *
	 * @see WP_List_Table::single_row_columns()
	 *
	 * @param array $item A singular item (one full row).
	 */
	public function column_cb( $item = array() ) {
		/* translators: accessibility text */
		printf( '<label class="screen-reader-text" for="gid-%1$d">' . __( 'Select event %1$d', 'sportszone' ) . '</label><input type="checkbox" name="gid[]" value="%1$d" id="gid-%1$d" />', $item['id'] );
	}

	/**
	 * Markup for the Event ID column.
	 *
	 * @since 1.7.0
	 *
	 * @see WP_List_Table::single_row_columns()
	 *
	 * @param array $item A singular item (one full row).
	 */
	public function column_gid( $item = array() ) {
		echo '<strong>' . absint( $item['id'] ) . '</strong>';
	}

	/**
	 * Name column, and "quick admin" rollover actions.
	 *
	 * Called "comment" in the CSS so we can re-use some WP core CSS.
	 *
	 * @since 1.7.0
	 *
	 * @see WP_List_Table::single_row_columns()
	 *
	 * @param array $item A singular item (one full row).
	 */
	public function column_comment( $item = array() ) {

		// Preorder items: Edit | Delete | View.
		$actions = array(
			'edit'   => '',
			'delete' => '',
			'view'   => '',
		);

		// We need the event object for some BP functions.
		$item_obj = (object) $item;

		// Build actions URLs.
		$base_url   = sz_get_admin_url( 'admin.php?page=sz-events&amp;gid=' . $item['id'] );
		$delete_url = wp_nonce_url( $base_url . "&amp;action=delete", 'sz-events-delete' );
		$edit_url   = $base_url . '&amp;action=edit';
		$view_url   = sz_get_event_permalink( $item_obj );

		/**
		 * Filters the event name for a event's column content.
		 *
		 * @since 1.7.0
		 *
		 * @param string $value Name of the event being rendered.
		 * @param array  $item  Array for the current event item.
		 */
		$event_name = apply_filters_ref_array( 'sz_get_event_name', array( $item['name'], $item ) );

		// Rollover actions.
		// Edit.
		$actions['edit']   = sprintf( '<a href="%s">%s</a>', esc_url( $edit_url   ), __( 'Edit',   'sportszone' ) );

		// Delete.
		$actions['delete'] = sprintf( '<a href="%s">%s</a>', esc_url( $delete_url ), __( 'Delete', 'sportszone' ) );

		// Visit.
		$actions['view']   = sprintf( '<a href="%s">%s</a>', esc_url( $view_url   ), __( 'View',   'sportszone' ) );

		/**
		 * Filters the actions that will be shown for the column content.
		 *
		 * @since 1.7.0
		 *
		 * @param array $value Array of actions to be displayed for the column content.
		 * @param array $item  The current event item in the loop.
		 */
		$actions = apply_filters( 'sz_events_admin_comment_row_actions', array_filter( $actions ), $item );

		// Get event name and avatar.
		$avatar = '';

		if ( sportszone()->avatar->show_avatars ) {
			$avatar  = sz_core_fetch_avatar( array(
				'item_id'    => $item['id'],
				'object'     => 'event',
				'type'       => 'thumb',
				'avatar_dir' => 'event-avatars',
				'alt'        => sprintf( __( 'Event logo of %s', 'sportszone' ), $event_name ),
				'width'      => '32',
				'height'     => '32',
				'title'      => $event_name
			) );
		}

		$content = sprintf( '<strong><a href="%s">%s</a></strong>', esc_url( $edit_url ), $event_name );

		echo $avatar . ' ' . $content . ' ' . $this->row_actions( $actions );
	}

	/**
	 * Markup for the Description column.
	 *
	 * @since 1.7.0
	 *
	 * @param array $item Information about the current row.
	 */
	public function column_description( $item = array() ) {

		/**
		 * Filters the markup for the Description column.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Markup for the Description column.
		 * @param array  $item  The current event item in the loop.
		 */
		echo apply_filters_ref_array( 'sz_get_event_description', array( $item['description'], $item ) );
	}

	/**
	 * Markup for the Status column.
	 *
	 * @since 1.7.0
	 *
	 * @param array $item Information about the current row.
	 */
	public function column_status( $item = array() ) {
		$status      = $item['status'];
		$status_desc = '';

		// @todo This should be abstracted out somewhere for the whole
		// Events component.
		switch ( $status ) {
			case 'public' :
				$status_desc = __( 'Public', 'sportszone' );
				break;
			case 'private' :
				$status_desc = __( 'Private', 'sportszone' );
				break;
			case 'hidden' :
				$status_desc = __( 'Hidden', 'sportszone' );
				break;
		}

		/**
		 * Filters the markup for the Status column.
		 *
		 * @since 1.7.0
		 *
		 * @param string $status_desc Markup for the Status column.
		 * @parma array  $item        The current event item in the loop.
		 */
		echo apply_filters_ref_array( 'sz_events_admin_get_event_status', array( $status_desc, $item ) );
	}

	/**
	 * Markup for the Number of Members column.
	 *
	 * @since 1.7.0
	 *
	 * @param array $item Information about the current row.
	 */
	public function column_members( $item = array() ) {
		$count = events_get_eventmeta( $item['id'], 'total_member_count' );

		/**
		 * Filters the markup for the number of Members column.
		 *
		 * @since 1.7.0
		 *
		 * @param int   $count Markup for the number of Members column.
		 * @parma array $item  The current event item in the loop.
		 */
		echo apply_filters_ref_array( 'sz_events_admin_get_event_member_count', array( (int) $count, $item ) );
	}

	/**
	 * Markup for the Last Active column.
	 *
	 * @since 1.7.0
	 *
	 * @param array $item Information about the current row.
	 */
	public function column_last_active( $item = array() ) {
		$last_active = events_get_eventmeta( $item['id'], 'last_activity' );

		/**
		 * Filters the markup for the Last Active column.
		 *
		 * @since 1.7.0
		 *
		 * @param string $last_active Markup for the Last Active column.
		 * @parma array  $item        The current event item in the loop.
		 */
		echo apply_filters_ref_array( 'sz_events_admin_get_event_last_active', array( $last_active, $item ) );
	}

	/**
	 * Allow plugins to add their custom column.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $item        Information about the current row.
	 * @param string $column_name The column name.
	 * @return string
	 */
	public function column_default( $item = array(), $column_name = '' ) {

		/**
		 * Filters a string to allow plugins to add custom column content.
		 *
		 * @since 2.0.0
		 *
		 * @param string $value       Empty string.
		 * @param string $column_name Name of the column being rendered.
		 * @param array  $item        The current event item in the loop.
		 */
		return apply_filters( 'sz_events_admin_get_event_custom_column', '', $column_name, $item );
	}

	// Event Types

	/**
	 * Add event type column to the WordPress admin events list table.
	 *
	 * @since 2.7.0
	 *
	 * @param array $columns Events table columns.
	 *
	 * @return array $columns
	 */
	public function add_type_column( $columns = array() ) {
		$columns['sz_event_type'] = _x( 'Event Type', 'Label for the WP events table event type column', 'sportszone' );

		return $columns;
	}

	/**
	 * Markup for the Event Type column.
	 *
	 * @since 2.7.0
	 *
	 * @param string $retval      Empty string.
	 * @param string $column_name Name of the column being rendered.
	 * @param array  $item        The current event item in the loop.
	 * @return string
	 */
	public function column_content_event_type( $retval = '', $column_name, $item ) {
		if ( 'sz_event_type' !== $column_name ) {
			return $retval;
		}

		add_filter( 'sz_get_event_type_directory_permalink', array( $this, 'event_type_permalink_use_admin_filter' ), 10, 2 );
		$retval = sz_get_event_type_list( $item['id'], array(
			'parent_element' => '',
			'label_element'  => '',
			'label'          => '',
			'show_all'       => true
		) );
		remove_filter( 'sz_get_event_type_directory_permalink', array( $this, 'event_type_permalink_use_admin_filter' ), 10 );

		/**
		 * Filters the markup for the Event Type column.
		 *
		 * @since 2.7.0
		 *
		 * @param string $retval Markup for the Event Type column.
		 * @parma array  $item   The current event item in the loop.
		 */
		echo apply_filters_ref_array( 'sz_events_admin_get_event_type_column', array( $retval, $item ) );
	}

	/**
	 * Filters the event type list permalink in the Event Type column.
	 *
	 * Changes the event type permalink to use the admin URL.
	 *
	 * @since 2.7.0
	 *
	 * @param  string $retval Current event type permalink.
	 * @param  object $type   Event type object.
	 * @return string
	 */
	public function event_type_permalink_use_admin_filter( $retval, $type ) {
		return add_query_arg( array( 'sz-event-type' => urlencode( $type->name ) ) );
	}

	/**
	 * Markup for the Event Type bulk change select.
	 *
	 * @since 2.7.0
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 */
	public function add_event_type_bulk_change_select( $which ) {
		// `$which` is only passed in WordPress 4.6+. Avoid duplicating controls in earlier versions.
		static $displayed = false;
		if ( version_compare( sz_get_major_wp_version(), '4.6', '<' ) && $displayed ) {
			return;
		}
		$displayed = true;
		$id_name = 'bottom' === $which ? 'sz_change_type2' : 'sz_change_type';

		$types = sz_events_get_event_types( array(), 'objects' );
		?>
		<div class="alignleft actions">
			<label class="screen-reader-text" for="<?php echo $id_name; ?>"><?php _e( 'Change event type to&hellip;', 'sportszone' ) ?></label>
			<select name="<?php echo $id_name; ?>" id="<?php echo $id_name; ?>" style="display:inline-block;float:none;">
				<option value=""><?php _e( 'Change event type to&hellip;', 'sportszone' ) ?></option>

				<?php foreach( $types as $type ) : ?>

					<option value="<?php echo esc_attr( $type->name ); ?>"><?php echo esc_html( $type->labels['singular_name'] ); ?></option>

				<?php endforeach; ?>

				<option value="remove_event_type"><?php _e( 'No Event Type', 'sportszone' ) ?></option>

			</select>
			<?php
			wp_nonce_field( 'sz-bulk-events-change-type-' . sz_loggedin_user_id(), 'sz-bulk-events-change-type-nonce' );
			submit_button( __( 'Change', 'sportszone' ), 'button', 'sz_change_event_type', false );
		?>
		</div>
		<?php
	}
}
