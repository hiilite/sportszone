<?php
/**
 * SportsZone - Events Activity
 *
 * @since 3.0.0
 * @version 3.1.0
 */
global $groups_template;

if(isset($_GET['action']) && $_GET['action'] == 'team-select'):
	$user_id = sz_loggedin_user_id();
	$event_id = sz_get_event_id();
	$event_url = sz_get_event_permalink();
	
	$group_args = array(
	    'group_type' => array( 'team' ),
	    'user_id'	 => $user_id,
	);
	$groups = false;
	?>
	
		<?php
	if ( sz_has_groups( $group_args ) ) {
		?>
		<h3><?php echo __('Teams','sportszone'); ?></h3>
	<p>Select the team you would like to register for the event</p>
	<div class="card-deck">
		<?php
		$first = 1;
		while ( sz_groups() ) :
			sz_the_group();
			$selected = ($first == 1)?'checked=checked':'';
			echo "<div class='card'>
				<div class='card-body'>
					<label for='event-team-select-".sz_get_group_id()."' class='align-center'>
						<input type='radio' name='event-team-select' id='event-team-select-".sz_get_group_id()."' value='".sz_get_group_id()."' required $selected><br>";
						sz_group_avatar();
						sz_group_name();
			echo "</label></div>
			</div>";
			
		endwhile;
		$sz_event_cost = events_get_eventmeta( $event_id, 'sz_event_cost' );
		$sz_event_paypal_email = events_get_eventmeta( $event_id, 'sz_event_paypal_email' );
		$approved_teams = events_get_eventmeta( $event_id, 'approved_teams');
		
		?>
		</div>
		<h4>Purchase Total:	$<span id="event_purchase_total"><?php echo $sz_event_cost; ?></span></h4>
		<div id="paypal-button"></div>
		<button id="test_payment_send">Test Process</button>
		<script src="https://www.paypalobjects.com/api/checkout.js"></script>
		<script>
			jQuery('#test_payment_send').on('click', function(e) {
					jQuery.ajax( {
						type: "post",
						dataType: "json",
						url: ajaxurl,
						data: {
							action   : 'events_pay_event',
							item_id  : '<?php echo $event_id; ?>',
							_wpnonce : '<?php echo wp_create_nonce("events_pay_event"); ?>',
							nonce 	 : '<?php echo wp_create_nonce("events_pay_event"); ?>',
							event_url: '<?php echo $event_url; ?>',
							event_purchase_total: '<?php echo $sz_event_cost; ?>',
							event_paypal_email: '<?php echo $sz_event_paypal_email; ?>',
							event_team_select: jQuery('input[name=event-team-select]:checked').val(),
						}
					} ).done( function( response ) {
						console.log(response);
						if ( false === response.success ) {
							// return failed message
							console.log( response.data.feedback );
						} else {
							console.log( response.data );
							// TODO : Ajax create new transaction post in database to store all info.
					        
					        // Ajax add all members of team to event
					        
					        // Add teams to a "approved_teams" meta for selection by the event.
					        
					        // Send email to event creator that team has been added to event
					        
					        // Redirect to event main page
							window.location.href = '<?php echo $event_url; ?>';
						}
					} );
			});
			
				
		  paypal.Button.render({
		    // Configure environment
		    env: 'sandbox',
		    client: {
		      sandbox: 'AX4jnuxmviHa5fu2s1O4SHrBrrvzFY_cakKAaDvf73kOMvXyoJPm3clJGuONpursvKo9IH-5oLGRfZ60',
		      production: 'AfxN7KtCCAlUEE_gJrWJ5fkIVVl65D1DqzFnDncYYMR1UOqgAzmC7OS_u5HS2qxZ7Mfqn7Scl9-lwW85'
		    },
		    // Customize button (optional)
		    locale: 'en_US',
		    style: {
		      size: 'small',
		      color: 'gold',
		      shape: 'pill',
		    },
		
		    // Enable Pay Now checkout flow (optional)
		    commit: true,
		
		    // Set up a payment
		    payment: function(data, actions) {
		      return actions.payment.create({
		        transactions: [{
		          amount: {
		            total: '<?php echo $sz_event_cost; ?>',
		            currency: 'CAD'
		          }
		        }]
		      });
		    },
		    // Execute the payment
		    onAuthorize: function(data, actions) {
			    console.log(data, actions);
			    paypalData = data;
			    return actions.payment.execute().then(function() {
				    /*
					 // DATA RETURNED
					 
					intent: "sale"
					orderID: "EC-4B560280GA301331R"
					payerID: "YZG78PNE2JNAG"
					paymentID: "PAY-07231785W60059310LQXY3IY"
					paymentToken: "EC-4B560280GA301331R"
					returnUrl: "https://www.sandbox.paypal.com/?paymentId=PAY-07231785W60059310LQXY3IY&token=EC-4B560280GA301331R&PayerID=YZG78PNE2JNAG"   
					 */
			        // Show a confirmation message to the buyer
			        
			        // TODO : Ajax create new transaction post in database to store all info.
			        // Ajax add all members of team to event
			        // Redirect to event members page
			        jQuery.ajax( {
						type: "post",
						dataType: "json",
						url: ajaxurl,
						data: {
							action   : 'events_pay_event',
							item_id  : '<?php echo $event_id; ?>',
							_wpnonce : '<?php echo wp_create_nonce("events_pay_event"); ?>',
							nonce 	 : '<?php echo wp_create_nonce("events_pay_event"); ?>',
							event_url: '<?php echo $event_url; ?>',
							event_purchase_total: '<?php echo $sz_event_cost; ?>',
							event_paypal_email: '<?php echo $sz_event_paypal_email; ?>',
							event_team_select: jQuery('input[name=event-team-select]:checked').val(),
							orderID: paypalData.orderID,
							payerID: paypalData.payerID,
							paymentID: paypalData.paymentID,
							paymentToken: paypalData.paymentToken
						}
					} ).done( function( response ) { 
						console.log(response);
						if ( false === response.success ) {
							// return failed message
							console.log( response.data.feedback );
						} else {
							console.log( response.data );
							// TODO : Ajax create new transaction post in database to store all info.
					        
					        // Ajax add all members of team to event
					        
					        // Add teams to a "approved_teams" meta for selection by the event.
					        
					        // Send email to event creator that team has been added to event
					        
					        // Redirect to event main page
							window.location.href = '<?php echo $event_url; ?>';
						}
					} );
			     window.alert('Thank you for your purchase!');
		      });
		    }
		  }, '#paypal-button');
		
		</script>

		<?php
	} else {
		?>
		<h4>You must be a member of a team to register for this event.</h4>
		<?php
	}
	?>
	
	<?php
		
else:
	?>
	
	<h2 class="sz-screen-title<?php echo ( ! sz_is_event_home() ) ? ' sz-screen-reader-text' : ''; ?>">
		<?php esc_html_e( 'Event Activities', 'sportszone' ); ?>
	</h2>
	
	<?php sz_nouveau_events_activity_post_form(); ?>
	
	<div class="subnav-filters filters clearfix">
	
		<ul>
	
			<li class="feed"><a href="<?php sz_event_activity_feed_link(); ?>" class="sz-tooltip no-ajax" data-sz-tooltip="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>"><span class="sz-screen-reader-text"><?php esc_html_e( 'RSS', 'sportszone' ); ?></span></a></li>
	
			<li class="event-act-search"><?php sz_nouveau_search_form(); ?></li>
	
		</ul>
	
			<?php sz_get_template_part( 'common/filters/events-screens-filters' ); ?>
	</div><!-- // .subnav-filters -->
	
	<?php sz_nouveau_event_hook( 'before', 'activity_content' ); ?>
	
	<div id="activity-stream" class="activity single-event" data-sz-list="activity">
	
			<li id="sz-activity-ajax-loader"><?php sz_nouveau_user_feedback( 'event-activity-loading' ); ?></li>
	
	</div><!-- .activity -->
	
	<?php
	sz_nouveau_event_hook( 'after', 'activity_content' );
endif;