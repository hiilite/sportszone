<?php
/**
 * SportsZone messages component Site-wide Notices admin screen.
 *
 *
 * @package SportsZone
 * @subpackage Messages
 * @since 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class SZ_Messages_Notices_Admin {

	/**
	 * The ID returned by `add_users_page()`.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	public $screen_id = '';

	/**
	 * The URL of the admin screen.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	public $url = '';

	/**
	 * The current instance of the SZ_Messages_Notices_List_Table class.
	 *
	 * @since 3.0.0
	 * @var object
	 */
	public $list_table = '';


	/**
     * Create a new instance or access the current instance of this class.
     *
     * @since 3.0.0
     */
	public static function register_notices_admin() {

		if ( ! is_admin() || ! sz_is_active( 'messages' ) || ! sz_current_user_can( 'sz_moderate' ) ) {
			return;
		}

		$sz = sportszone();

		if ( empty( $sz->messages->admin ) ) {
			$sz->messages->admin = new self;
		}

		return $sz->messages->admin;
	}

	/**
	 * Constructor method.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Populate the classs variables.
	 *
	 * @since 3.0.0
	 */
	protected function setup_globals() {
		$this->url = add_query_arg( array( 'page' => 'sz-notices' ), sz_get_admin_url( 'users.php' ) );
	}

	/**
	 * Add action hooks.
	 *
	 * @since 3.0.0
	 */
	protected function setup_actions() {
		add_action( sz_core_admin_hook(), array( $this, 'admin_menu' ) );
	}

	/**
	 * Add the 'Site Notices' admin menu item.
	 *
	 * @since 3.0.0
	 */
	public function admin_menu() {
		// Bail if current user cannot moderate community.
		if ( ! sz_current_user_can( 'sz_moderate' ) || ! sz_is_active( 'messages' ) ) {
			return false;
		}

		$this->screen_id = add_users_page(
			_x( 'Site Notices', 'Notices admin page title', 'sportszone' ),
			_x( 'Site Notices', 'Admin Users menu', 'sportszone' ),
			'manage_options',
			'sz-notices',
			array( $this, 'admin_index' )
		);

		add_action( 'load-' . $this->screen_id, array( $this, 'admin_load' ) );
	}

	/**
	 * Catch save/update requests or load the screen.
	 *
	 * @since 3.0.0
	 */
	public function admin_load() {
		$redirect_to = false;

		// Catch new notice saves.
		if ( ! empty( $_POST['sz_notice']['send'] ) ) {

			check_admin_referer( 'new-notice', 'ns-nonce' );

			$notice = wp_parse_args( $_POST['sz_notice'], array(
				'subject' => '',
				'content' => ''
			) );

			if ( messages_send_notice( $notice['subject'], $notice['content'] ) ) {
				$redirect_to = add_query_arg( 'success', 'create', $this->url );

			// Notice could not be sent.
			} else {
				$redirect_to = add_query_arg( 'error', 'create', $this->url );
			}
		}

		// Catch activation/deactivation/delete requests
		if ( ! empty( $_GET['notice_id'] ) && ! empty( $_GET['notice_action'] ) ) {
			$notice_id = absint( $_GET['notice_id'] );

			check_admin_referer( 'messages-' . $_GET['notice_action'] . '-notice-' . $notice_id );

			$success = false;
			switch ( $_GET['notice_action'] ) {
				case 'activate':
					$notice = new SZ_Messages_Notice( $notice_id );
					$success = $notice->activate();
					break;
				case 'deactivate':
					$notice = new SZ_Messages_Notice( $notice_id );
					$success = $notice->deactivate();
					break;
				case 'delete':
					$notice = new SZ_Messages_Notice( $notice_id );
					$success = $notice->delete();
					break;
			}
			if ( $success ) {
				$redirect_to = add_query_arg( 'success', 'update', $this->url );

			// Notice could not be updated.
			} else {
				$redirect_to = add_query_arg( 'error', 'update', $this->url );
			}

		}

		if ( $redirect_to ) {
			wp_safe_redirect( $redirect_to );
			exit();
		}

		$this->list_table = new SZ_Messages_Notices_List_Table( array( 'screen' => get_current_screen()->id ) );
	}

	/**
	 * Generate content for the sz-notices admin screen.
	 *
	 * @since 3.0.0
	 */
	public function admin_index() {
		$this->list_table->prepare_items();
		?>
		<div class="wrap">
			<?php if ( version_compare( $GLOBALS['wp_version'], '4.8', '>=' ) ) : ?>

				<h1 class="wp-heading-inline"><?php echo esc_html_x( 'Site Notices', 'Notices admin page title', 'sportszone' ); ?></h1>
				<hr class="wp-header-end">

			<?php else : ?>

				<h1><?php echo esc_html_x( 'Site Notices', 'Notices admin page title', 'sportszone' ); ?></h1>

			<?php endif; ?>

			<p class="sz-notice-about"><?php esc_html_e( 'Manage notices shown at front end of your site to all logged-in users.', 'sportszone' ); ?></p>

			<div class="sz-new-notice-panel">

				<h2 class="sz-new-notice"><?php esc_html_e( 'Add New Notice', 'sportszone' ); ?></h2>

				<form action="<?php echo esc_url( wp_nonce_url( $this->url, 'new-notice', 'ns-nonce' ) ); ?>" method="post">

					<div>
						<label for="sz_notice_subject"><?php esc_html_e( 'Subject', 'sportszone' ); ?></label>
						<input type="text" class="sz-panel-input" id="sz_notice_subject" name="sz_notice[subject]"/>

						<label for="sz_notice_content"><?php esc_html_e( 'Content', 'sportszone' ); ?></label>
						<textarea class="sz-panel-textarea" id="sz_notice_content" name="sz_notice[content]"></textarea>
					</div>

					<input type="submit" value="<?php esc_attr_e( 'Publish Notice', 'sportszone' ); ?>" name="sz_notice[send]" class="button button-primary save alignleft">

				</form>

			</div>

			<?php if ( isset( $_GET['success'] ) || isset( $_GET['error'] ) ) : ?>

				<div id="message" class="<?php echo isset( $_GET['success'] ) ? 'updated' : 'error'; ?>">

					<p>
						<?php
						if ( isset( $_GET['error'] ) ) {
							if ( 'create' === $_GET['error'] ) {
								esc_html_e( 'Notice was not created. Please try again.', 'sportszone' );
							} else {
								esc_html_e( 'Notice was not updated. Please try again.', 'sportszone' );
							}
						 } else {
							if ( 'create' === $_GET['success'] ) {
								esc_html_e( 'Notice successfully created.', 'sportszone' );
							} else {
								esc_html_e( 'Notice successfully updated.', 'sportszone' );
							}
						}
						?>
					</p>

				</div>

			<?php endif; ?>

			<h2 class="sz-notices-list"><?php esc_html_e( 'Notices List', 'sportszone' ); ?></h2>

			<?php $this->list_table->display(); ?>

		</div>
		<?php
	}
}
