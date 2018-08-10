<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php if ( current_theme_supports( 'sz-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<?php sz_head(); ?>
		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?> id="sz-default">

		<?php do_action( 'sz_before_header' ); ?>

		<div id="header">
			<div id="search-bar" role="search">
				<div class="padder">
					<h1 id="logo" role="banner"><a href="<?php echo home_url(); ?>" title="<?php echo esc_attr_x( 'Home', 'Home page banner link title', 'sportszone' ); ?>"><?php sz_site_name(); ?></a></h1>

						<form action="<?php echo sz_search_form_action(); ?>" method="post" id="search-form">
							<label for="search-terms" class="accessibly-hidden"><?php _e( 'Search for:', 'sportszone' ); ?></label>
							<input type="text" id="search-terms" name="search-terms" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />

							<?php echo sz_search_form_type_select(); ?>

							<input type="submit" name="search-submit" id="search-submit" value="<?php esc_attr_e( 'Search', 'sportszone' ); ?>" />

							<?php wp_nonce_field( 'sz_search_form' ); ?>

						</form><!-- #search-form -->

				<?php do_action( 'sz_search_login_bar' ); ?>

				</div><!-- .padder -->
			</div><!-- #search-bar -->

			<div id="navigation" role="navigation">
				<?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'nav', 'theme_location' => 'primary', 'fallback_cb' => 'sz_dtheme_main_nav' ) ); ?>
			</div>

			<?php do_action( 'sz_header' ); ?>

		</div><!-- #header -->

		<?php do_action( 'sz_after_header'     ); ?>
		<?php do_action( 'sz_before_container' ); ?>

		<div id="container">
