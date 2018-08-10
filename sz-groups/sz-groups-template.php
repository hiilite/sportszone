<?php
/**
 * SportsZone Groups Template Functions.
 *
 * @package SportsZone
 * @subpackage GroupsTemplates
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Output the groups component slug.
 *
 * @since 1.5.0
 */
function sz_groups_slug() {
	echo sz_get_groups_slug();
}
	/**
	 * Return the groups component slug.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_groups_slug() {

		/**
		 * Filters the groups component slug.
		 *
		 * @since 1.5.0
		 *
		 * @param string $slug Groups component slug.
		 */
		return apply_filters( 'sz_get_groups_slug', sportszone()->groups->slug );
	}

/**
 * Output the groups component root slug.
 *
 * @since 1.5.0
 */
function sz_groups_root_slug() {
	echo sz_get_groups_root_slug();
}
	/**
	 * Return the groups component root slug.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_groups_root_slug() {

		/**
		 * Filters the groups component root slug.
		 *
		 * @since 1.5.0
		 *
		 * @param string $root_slug Groups component root slug.
		 */
		return apply_filters( 'sz_get_groups_root_slug', sportszone()->groups->root_slug );
	}

/**
 * Output the group type base slug.
 *
 * @since 2.7.0
 */
function sz_groups_group_type_base() {
	echo esc_url( sz_get_groups_group_type_base() );
}
	/**
	 * Get the group type base slug.
	 *
	 * The base slug is the string used as the base prefix when generating group
	 * type directory URLs. For example, in example.com/groups/type/foo/, 'foo' is
	 * the group type and 'type' is the base slug.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	function sz_get_groups_group_type_base() {
		/**
		 * Filters the group type URL base.
		 *
		 * @since 2.7.0
		 *
		 * @param string $base
		 */
		return apply_filters( 'sz_groups_group_type_base', _x( 'type', 'group type URL base', 'sportszone' ) );
	}

/**
 * Output group directory permalink.
 *
 * @since 1.5.0
 */
function sz_groups_directory_permalink() {
	echo esc_url( sz_get_groups_directory_permalink() );
}
	/**
	 * Return group directory permalink.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_groups_directory_permalink() {

		/**
		 * Filters the group directory permalink.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value Permalink for the group directory.
		 */
		return apply_filters( 'sz_get_groups_directory_permalink', trailingslashit( sz_get_root_domain() . '/' . sz_get_groups_root_slug() ) );
	}

/**
 * Output group type directory permalink.
 *
 * @since 2.7.0
 *
 * @param string $group_type Optional. Group type.
 */
function sz_group_type_directory_permalink( $group_type = '' ) {
	echo esc_url( sz_get_group_type_directory_permalink( $group_type ) );
}
	/**
	 * Return group type directory permalink.
	 *
	 * @since 2.7.0
	 *
	 * @param string $group_type Optional. Group type. Defaults to current group type.
	 * @return string Group type directory URL on success, an empty string on failure.
	 */
	function sz_get_group_type_directory_permalink( $group_type = '' ) {

		if ( $group_type ) {
			$_group_type = $group_type;
		} else {
			// Fall back on the current group type.
			$_group_type = sz_get_current_group_directory_type();
		}

		$type = sz_groups_get_group_type_object( $_group_type );

		// Bail when member type is not found or has no directory.
		if ( ! $type || ! $type->has_directory ) {
			return '';
		}

		/**
		 * Filters the group type directory permalink.
		 *
		 * @since 2.7.0
		 *
		 * @param string $value       Group type directory permalink.
		 * @param object $type        Group type object.
		 * @param string $member_type Group type name, as passed to the function.
		 */
		return apply_filters( 'sz_get_group_type_directory_permalink', trailingslashit( sz_get_groups_directory_permalink() . sz_get_groups_group_type_base() . '/' . $type->directory_slug ), $type, $group_type );
	}

/**
 * Output group type directory link.
 *
 * @since 2.7.0
 *
 * @param string $group_type Unique group type identifier as used in sz_groups_register_group_type().
 */
function sz_group_type_directory_link( $group_type = '' ) {
	echo sz_get_group_type_directory_link( $group_type );
}
	/**
	 * Return group type directory link.
	 *
	 * @since 2.7.0
	 *
	 * @param string $group_type Unique group type identifier as used in sz_groups_register_group_type().
	 * @return string
	 */
	function sz_get_group_type_directory_link( $group_type = '' ) {
		if ( empty( $group_type ) ) {
			return '';
		}

		return sprintf( '<a href="%s">%s</a>', esc_url( sz_get_group_type_directory_permalink( $group_type ) ), sz_groups_get_group_type_object( $group_type )->labels['name'] );
	}

/**
 * Output a comma-delimited list of group types.
 *
 * @since 2.7.0
 * @see   sz_get_group_type_list() for parameter documentation.
 */
function sz_group_type_list( $group_id = 0, $r = array() ) {
	echo sz_get_group_type_list( $group_id, $r );
}
	/**
	 * Return a comma-delimited list of group types.
	 *
	 * @since 2.7.0
	 *
	 * @param int $group_id Group ID. Defaults to current group ID if on a group page.
	 * @param array|string $r {
	 *     Array of parameters. All items are optional.
	 *     @type string $parent_element Element to wrap around the list. Defaults to 'p'.
	 *     @type array  $parent_attr    Element attributes for parent element. Defaults to
	 *                                  array( 'class' => 'sz-group-type-list' ).
	 *     @type string $label          Label to add before the list. Defaults to 'Group Types:'.
	 *     @type string $label_element  Element to wrap around the label. Defaults to 'strong'.
	 *     @type array  $label_attr     Element attributes for label element. Defaults to array().
	 *     @type bool   $show_all       Whether to show all registered group types. Defaults to 'false'. If
	 *                                 'false', only shows group types with the 'show_in_list' parameter set to
	 *                                  true. See sz_groups_register_group_type() for more info.
	 * }
	 * @return string
	 */
	function sz_get_group_type_list( $group_id = 0, $r = array() ) {
		if ( empty( $group_id ) ) {
			$group_id = sz_get_current_group_id();
		}

		$r = sz_parse_args( $r, array(
			'parent_element' => 'p',
			'parent_attr'    => array(
				 'class' => 'sz-group-type-list',
			),
			'label'          => __( 'Group Types:', 'sportszone' ),
			'label_element'  => 'strong',
			'label_attr'     => array(),
			'show_all'       => false,
		), 'group_type_list' );

		$retval = '';

		if ( $types = sz_groups_get_group_type( $group_id, false ) ) {
			// Make sure we can show the type in the list.
			if ( false === $r['show_all'] ) {
				$types = array_intersect( sz_groups_get_group_types( array( 'show_in_list' => true ) ), $types );
				if ( empty( $types ) ) {
					return $retval;
				}
			}

			$before = $after = $label = '';

			// Render parent element.
			if ( ! empty( $r['parent_element'] ) ) {
				$parent_elem = new SZ_Core_HTML_Element( array(
					'element' => $r['parent_element'],
					'attr'    => $r['parent_attr']
				) );

				// Set before and after.
				$before = $parent_elem->get( 'open_tag' );
				$after  = $parent_elem->get( 'close_tag' );
			}

			// Render label element.
			if ( ! empty( $r['label_element'] ) ) {
				$label = new SZ_Core_HTML_Element( array(
					'element'    => $r['label_element'],
					'attr'       => $r['label_attr'],
					'inner_html' => esc_html( $r['label'] )
				) );
				$label = $label->contents() . ' ';

			// No element, just the label.
			} else {
				$label = esc_html( $r['label'] );
			}

			// Comma-delimit each type into the group type directory link.
			$label .= implode( ', ', array_map( 'sz_get_group_type_directory_link', $types ) );

			// Retval time!
			$retval = $before . $label . $after;
		}

		return $retval;
	}

/**
 * Start the Groups Template Loop.
 *
 * @since 1.0.0
 * @since 2.6.0 Added `$group_type`, `$group_type__in`, and `$group_type__not_in` parameters.
 * @since 2.7.0 Added `$update_admin_cache` parameter.
 *
 * @param array|string $args {
 *     Array of parameters. All items are optional.
 *     @type string       $type               Shorthand for certain orderby/order combinations. 'newest', 'active',
 *                                            'popular', 'alphabetical', 'random'. When present, will override
 *                                            orderby and order params. Default: null.
 *     @type string       $order              Sort order. 'ASC' or 'DESC'. Default: 'DESC'.
 *     @type string       $orderby            Property to sort by. 'date_created', 'last_activity',
 *                                            'total_member_count', 'name', 'random'. Default: 'last_activity'.
 *     @type int          $page               Page offset of results to return. Default: 1 (first page of results).
 *     @type int          $per_page           Number of items to return per page of results. Default: 20.
 *     @type int          $max                Does NOT affect query. May change the reported number of total groups
 *                                            found, but not the actual number of found groups. Default: false.
 *     @type bool         $show_hidden        Whether to include hidden groups in results. Default: false.
 *     @type string       $page_arg           Query argument used for pagination. Default: 'grpage'.
 *     @type int          $user_id            If provided, results will be limited to groups of which the specified
 *                                            user is a member. Default: value of sz_displayed_user_id().
 *     @type string       $slug               If provided, only the group with the matching slug will be returned.
 *                                            Default: false.
 *     @type string       $search_terms       If provided, only groups whose names or descriptions match the search
 *                                            terms will be returned. Default: value of `$_REQUEST['groups_search']` or
 *                                            `$_REQUEST['s']`, if present. Otherwise false.
 *     @type array|string $group_type         Array or comma-separated list of group types to limit results to.
 *     @type array|string $group_type__in     Array or comma-separated list of group types to limit results to.
 *     @type array|string $group_type__not_in Array or comma-separated list of group types that will be
 *                                            excluded from results.
 *     @type array        $meta_query         An array of meta_query conditions.
 *                                            See {@link WP_Meta_Query::queries} for description.
 *     @type array|string $include            Array or comma-separated list of group IDs. Results will be limited
 *                                            to groups within the list. Default: false.
 *     @type array|string $exclude            Array or comma-separated list of group IDs. Results will exclude
 *                                            the listed groups. Default: false.
 *     @type array|string $parent_id          Array or comma-separated list of group IDs. Results will include only
 *                                            child groups of the listed groups. Default: null.
 *     @type bool         $update_meta_cache  Whether to fetch groupmeta for queried groups. Default: true.
 *     @type bool         $update_admin_cache Whether to pre-fetch group admins for queried groups.
 *                                            Defaults to true when on a group directory, where this
 *                                            information is needed in the loop. Otherwise false.
 * }
 * @return bool True if there are groups to display that match the params
 */
function sz_has_groups( $args = '' ) {
	global $groups_template;

	/*
	 * Defaults based on the current page & overridden by parsed $args
	 */
	$slug         = false;
	$type         = '';
	$search_terms = false;

	// When looking your own groups, check for two action variables.
	if ( sz_is_current_action( 'my-groups' ) ) {
		if ( sz_is_action_variable( 'most-popular', 0 ) ) {
			$type = 'popular';
		} elseif ( sz_is_action_variable( 'alphabetically', 0 ) ) {
			$type = 'alphabetical';
		}

	// When looking at invites, set type to invites.
	} elseif ( sz_is_current_action( 'invites' ) ) {
		$type = 'invites';

	// When looking at a single group, set the type and slug.
	} elseif ( sz_get_current_group_slug() ) {
		$type = 'single-group';
		$slug = sz_get_current_group_slug();
	}

	$group_type = sz_get_current_group_directory_type();
	if ( ! $group_type && ! empty( $_GET['group_type'] ) ) {
		if ( is_array( $_GET['group_type'] ) ) {
			$group_type = $_GET['group_type'];
		} else {
			// Can be a comma-separated list.
			$group_type = explode( ',', $_GET['group_type'] );
		}
	}

	// Default search string (too soon to escape here).
	$search_query_arg = sz_core_get_component_search_query_arg( 'groups' );
	if ( ! empty( $_REQUEST[ $search_query_arg ] ) ) {
		$search_terms = stripslashes( $_REQUEST[ $search_query_arg ] );
	} elseif ( ! empty( $_REQUEST['group-filter-box'] ) ) {
		$search_terms = $_REQUEST['group-filter-box'];
	} elseif ( !empty( $_REQUEST['s'] ) ) {
		$search_terms = $_REQUEST['s'];
	}

	// Parse defaults and requested arguments.
	$r = sz_parse_args( $args, array(
		'type'               => $type,
		'order'              => 'DESC',
		'orderby'            => 'last_activity',
		'page'               => 1,
		'per_page'           => 20,
		'max'                => false,
		'show_hidden'        => false,
		'page_arg'           => 'grpage',
		'user_id'            => sz_displayed_user_id(),
		'slug'               => $slug,
		'search_terms'       => $search_terms,
		'group_type'         => $group_type,
		'group_type__in'     => '',
		'group_type__not_in' => '',
		'meta_query'         => false,
		'include'            => false,
		'exclude'            => false,
		'parent_id'          => null,
		'update_meta_cache'  => true,
		'update_admin_cache' => sz_is_groups_directory() || sz_is_user_groups(),
	), 'has_groups' );

	// Setup the Groups template global.
	$groups_template = new SZ_Groups_Template( array(
		'type'               => $r['type'],
		'order'              => $r['order'],
		'orderby'            => $r['orderby'],
		'page'               => (int) $r['page'],
		'per_page'           => (int) $r['per_page'],
		'max'                => (int) $r['max'],
		'show_hidden'        => $r['show_hidden'],
		'page_arg'           => $r['page_arg'],
		'user_id'            => (int) $r['user_id'],
		'slug'               => $r['slug'],
		'search_terms'       => $r['search_terms'],
		'group_type'         => $r['group_type'],
		'group_type__in'     => $r['group_type__in'],
		'group_type__not_in' => $r['group_type__not_in'],
		'meta_query'         => $r['meta_query'],
		'include'            => $r['include'],
		'exclude'            => $r['exclude'],
		'parent_id'          => $r['parent_id'],
		'update_meta_cache'  => (bool) $r['update_meta_cache'],
		'update_admin_cache' => (bool) $r['update_admin_cache'],
	) );

	/**
	 * Filters whether or not there are groups to iterate over for the groups loop.
	 *
	 * @since 1.1.0
	 *
	 * @param bool               $value           Whether or not there are groups to iterate over.
	 * @param SZ_Groups_Template $groups_template SZ_Groups_Template object based on parsed arguments.
	 * @param array              $r               Array of parsed arguments for the query.
	 */
	return apply_filters( 'sz_has_groups', $groups_template->has_groups(), $groups_template, $r );
}

/**
 * Check whether there are more groups to iterate over.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function sz_groups() {
	global $groups_template;
	return $groups_template->groups();
}

/**
 * Set up the current group inside the loop.
 *
 * @since 1.0.0
 *
 * @return object
 */
function sz_the_group() {
	global $groups_template;
	return $groups_template->the_group();
}

/**
 * Is the group accessible to the currently logged-in user?
 * Despite the name of the function, it has historically checked
 * whether a user has access to a group.
 * In BP 2.9, a property was added to the SZ_Groups_Group class,
 * `is_visible`, that describes whether a user can know the group exists.
 * If you wish to check that property, use the check:
 * sz_current_user_can( 'groups_see_group' ).
 *
 * @since 1.0.0
 *
 * @param SZ_Groups_Group|null $group Optional. Group object. Default: current group in loop.
 * @return bool
 */
function sz_group_is_visible( $group = null ) {
	global $groups_template;

	if ( sz_current_user_can( 'sz_moderate' ) ) {
		return true;
	}

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	return sz_current_user_can( 'groups_access_group', array( 'group_id' => $group->id ) );
}

/**
 * Output the ID of the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object. Default: current group in loop.
 */
function sz_group_id( $group = false ) {
	echo sz_get_group_id( $group );
}
	/**
	 * Get the ID of the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return int
	 */
	function sz_get_group_id( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the ID of the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param int    $id    ID of the current group in the loop.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_id', $group->id, $group );
	}

/**
 * Output the row class of the current group in the loop.
 *
 * @since 1.7.0
 *
 * @param array $classes Array of custom classes.
 */
function sz_group_class( $classes = array() ) {
	echo sz_get_group_class( $classes );
}
	/**
	 * Get the row class of the current group in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param array $classes Array of custom classes.
	 * @return string Row class of the group.
	 */
	function sz_get_group_class( $classes = array() ) {
		global $groups_template;

		// Add even/odd classes, but only if there's more than 1 group.
		if ( $groups_template->group_count > 1 ) {
			$pos_in_loop = (int) $groups_template->current_group;
			$classes[]   = ( $pos_in_loop % 2 ) ? 'even' : 'odd';

		// If we've only one group in the loop, don't bother with odd and even.
		} else {
			$classes[] = 'sz-single-group';
		}

		// Group type - public, private, hidden.
		$classes[] = sanitize_key( $groups_template->group->status );

		// Add current group types.
		if ( $group_types = sz_groups_get_group_type( sz_get_group_id(), false ) ) {
			foreach ( $group_types as $group_type ) {
				$classes[] = sprintf( 'group-type-%s', esc_attr( $group_type ) );
			}
		}

		// User's group role.
		if ( sz_is_user_active() ) {

			// Admin.
			if ( sz_group_is_admin() ) {
				$classes[] = 'is-admin';
			}

			// Moderator.
			if ( sz_group_is_mod() ) {
				$classes[] = 'is-mod';
			}

			// Member.
			if ( sz_group_is_member() ) {
				$classes[] = 'is-member';
			}
		}

		// Whether a group avatar will appear.
		if ( sz_disable_group_avatar_uploads() || ! sportszone()->avatar->show_avatars ) {
			$classes[] = 'group-no-avatar';
		} else {
			$classes[] = 'group-has-avatar';
		}

		/**
		 * Filters classes that will be applied to row class of the current group in the loop.
		 *
		 * @since 1.7.0
		 *
		 * @param array $classes Array of determined classes for the row.
		 */
		$classes = apply_filters( 'sz_get_group_class', $classes );
		$classes = array_merge( $classes, array() );
		$retval = 'class="' . join( ' ', $classes ) . '"';

		return $retval;
	}

/**
 * Output the name of the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_name( $group = false ) {
	echo sz_get_group_name( $group );
}
	/**
	 * Get the name of the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_name( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the name of the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $name  Name of the current group in the loop.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_name', $group->name, $group );
	}

/**
 * Output the type of the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_type( $group = false ) {
	echo sz_get_group_type( $group );
}

/**
 * Get the type of the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 * @return string
 */
function sz_get_group_type( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( 'public' == $group->status ) {
		$type = __( "Public Group", 'sportszone' );
	} elseif ( 'hidden' == $group->status ) {
		$type = __( "Hidden Group", 'sportszone' );
	} elseif ( 'private' == $group->status ) {
		$type = __( "Private Group", 'sportszone' );
	} else {
		$type = ucwords( $group->status ) . ' ' . __( 'Group', 'sportszone' );
	}

	/**
	 * Filters the type for the current group in the loop.
	 *
	 * @since 1.0.0
	 * @since 2.5.0 Added the `$group` parameter.
	 *
	 * @param string $type  Type for the current group in the loop.
	 * @param object $group Group object.
	 */
	return apply_filters( 'sz_get_group_type', $type, $group );
}
/**
 * Output the status of the current group in the loop.
 *
 * @since 1.1.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_status( $group = false ) {
	echo sz_get_group_status( $group );
}
	/**
	 * Get the status of the current group in the loop.
	 *
	 * @since 1.1.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_status( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the status of the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $status Status of the current group in the loop.
		 * @param object $group  Group object.
		 */
		return apply_filters( 'sz_get_group_status', $group->status, $group );
	}

/**
 * Output the group avatar while in the groups loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *      See {@link sz_get_group_avatar()} for description of arguments.
 * }
 */
function sz_group_avatar( $args = '' ) {
	echo sz_get_group_avatar( $args );
}
	/**
	 * Get a group's avatar.
	 *
	 * @since 1.0.0
	 *
	 * @see sz_core_fetch_avatar() For a description of arguments and return values.
	 *
	 * @param array|string $args {
	 *     Arguments are listed here with an explanation of their defaults.
	 *     For more information about the arguments, see {@link sz_core_fetch_avatar()}.
	 *
	 *     @type string   $alt     Default: 'Group logo of [group name]'.
	 *     @type string   $class   Default: 'avatar'.
	 *     @type string   $type    Default: 'full'.
	 *     @type int|bool $width   Default: false.
	 *     @type int|bool $height  Default: false.
	 *     @type bool     $id      Passed to `$css_id` parameter.
	 * }
	 * @return string Group avatar string.
	 */
	function sz_get_group_avatar( $args = '' ) {
		global $groups_template;

		// Bail if avatars are turned off.
		if ( sz_disable_group_avatar_uploads() || ! sportszone()->avatar->show_avatars ) {
			return false;
		}

		// Parse the arguments.
		$r = sz_parse_args( $args, array(
			'type'   => 'full',
			'width'  => false,
			'height' => false,
			'class'  => 'avatar',
			'id'     => false,
			'alt'    => sprintf( __( 'Group logo of %s', 'sportszone' ), $groups_template->group->name )
		) );

		// Fetch the avatar from the folder.
		$avatar = sz_core_fetch_avatar( array(
			'item_id'    => $groups_template->group->id,
			'avatar_dir' => 'group-avatars',
			'object'     => 'group',
			'type'       => $r['type'],
			'alt'        => $r['alt'],
			'css_id'     => $r['id'],
			'class'      => $r['class'],
			'width'      => $r['width'],
			'height'     => $r['height'],
		) );

		// If No avatar found, provide some backwards compatibility.
		if ( empty( $avatar ) ) {
			$avatar = '<img src="' . esc_url( $groups_template->group->avatar_thumb ) . '" class="avatar" alt="' . esc_attr( $groups_template->group->name ) . '" />';
		}

		/**
		 * Filters the group avatar while in the groups loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $avatar HTML image element holding the group avatar.
		 * @param array  $r      Array of parsed arguments for the group avatar.
		 */
		return apply_filters( 'sz_get_group_avatar', $avatar, $r );
	}

/**
 * Output the group avatar thumbnail while in the groups loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_avatar_thumb( $group = false ) {
	echo sz_get_group_avatar_thumb( $group );
}
	/**
	 * Return the group avatar thumbnail while in the groups loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_avatar_thumb( $group = false ) {
		return sz_get_group_avatar( array(
			'type' => 'thumb',
			'id'   => ! empty( $group->id ) ? $group->id : false
		) );
	}

/**
 * Output the miniature group avatar thumbnail while in the groups loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_avatar_mini( $group = false ) {
	echo sz_get_group_avatar_mini( $group );
}
	/**
	 * Return the miniature group avatar thumbnail while in the groups loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_avatar_mini( $group = false ) {
		return sz_get_group_avatar( array(
			'type'   => 'thumb',
			'width'  => 30,
			'height' => 30,
			'id'     => ! empty( $group->id ) ? $group->id : false
		) );
	}

/** Group cover image *********************************************************/

/**
 * Should we use the group's cover image header.
 *
 * @since 2.4.0
 *
 * @return bool True if the displayed user has a cover image,
 *              False otherwise
 */
function sz_group_use_cover_image_header() {
	return (bool) sz_is_active( 'groups', 'cover_image' ) && ! sz_disable_group_cover_image_uploads() && sz_attachments_is_wp_version_supported();
}

/**
 * Output the 'last active' string for the current group in the loop.
 *
 * @since 1.0.0
 * @since 2.7.0 Added $args as a parameter.
 *
 * @param object|bool  $group Optional. Group object. Default: current group in loop.
 * @param array|string $args Optional. {@see sz_get_group_last_active()}.
 */
function sz_group_last_active( $group = false, $args = array() ) {
	echo sz_get_group_last_active( $group, $args );
}
	/**
	 * Return the 'last active' string for the current group in the loop.
	 *
	 * @since 1.0.0
	 * @since 2.7.0 Added $args as a parameter.
	 *
	 * @param object|bool  $group Optional. Group object. Default: current group in loop.
	 * @param array|string $args {
	 *     Array of optional parameters.
	 *
	 *     @type bool $relative Optional. If true, returns relative activity date. eg. active 5 months ago.
	 *                          If false, returns active date value from database. Default: true.
	 * }
	 * @return string
	 */
	function sz_get_group_last_active( $group = false, $args = array() ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		$r = sz_parse_args( $args, array(
			'relative' => true,
		), 'group_last_active' );

		$last_active = $group->last_activity;
		if ( ! $last_active ) {
			$last_active = groups_get_groupmeta( $group->id, 'last_activity' );
		}

		// We do not want relative time, so return now.
		// @todo Should the 'sz_get_group_last_active' filter be applied here?
		if ( ! $r['relative'] ) {
			return esc_attr( $last_active );
		}

		if ( empty( $last_active ) ) {
			return __( 'not yet active', 'sportszone' );
		} else {

			/**
			 * Filters the 'last active' string for the current group in the loop.
			 *
			 * @since 1.0.0
			 * @since 2.5.0 Added the `$group` parameter.
			 *
			 * @param string $value Determined last active value for the current group.
			 * @param object $group Group object.
			 */
			return apply_filters( 'sz_get_group_last_active', sz_core_time_since( $last_active ), $group );
		}
	}

/**
 * Output the permalink for the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param SZ_Groups_Group|null $group Optional. Group object. Default: current group in loop.
 */
function sz_group_permalink( $group = null ) {
	echo sz_get_group_permalink( $group );
}
	/**
	 * Return the permalink for the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param SZ_Groups_Group|null $group Optional. Group object. Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_permalink( $group = null ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the permalink for the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Permalink for the current group in the loop.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_permalink', trailingslashit( sz_get_groups_directory_permalink() . sz_get_group_slug( $group ) . '/' ), $group );
	}

/**
 * Output an HTML-formatted link for the current group in the loop.
 *
 * @since 2.9.0
 *
 * @param SZ_Groups_Group|null $group Optional. Group object.
 *                                    Default: current group in loop.
 */
function sz_group_link( $group = null ) {
	echo sz_get_group_link( $group );
}
	/**
	 * Return an HTML-formatted link for the current group in the loop.
	 *
	 * @since 2.9.0
	 *
	 * @param SZ_Groups_Group|null $group Optional. Group object.
	 *                                    Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_link( $group = null ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		$link = sprintf(
			'<a href="%s" class="sz-group-home-link %s-home-link">%s</a>',
			esc_url( sz_get_group_permalink( $group ) ),
			esc_attr( sz_get_group_slug( $group ) ),
			esc_html( sz_get_group_name( $group ) )
		);

		/**
		 * Filters the HTML-formatted link for the current group in the loop.
		 *
		 * @since 2.9.0
		 *
		 * @param string          $value HTML-formatted link for the
		 *                               current group in the loop.
		 * @param SZ_Groups_Group $group The current group object.
		 */
		return apply_filters( 'sz_get_group_link', $link, $group );
	}

/**
 * Output the permalink for the admin section of the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_admin_permalink( $group = false ) {
	echo sz_get_group_admin_permalink( $group );
}
	/**
	 * Return the permalink for the admin section of the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_admin_permalink( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the permalink for the admin section of the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Permalink for the admin section of the current group in the loop.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_admin_permalink', trailingslashit( sz_get_group_permalink( $group ) . 'admin' ), $group );
	}

/**
 * Return the slug for the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_slug( $group = false ) {
	echo sz_get_group_slug( $group );
}
	/**
	 * Return the slug for the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_slug( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the slug for the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $slug  Slug for the current group in the loop.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_slug', $group->slug, $group );
	}

/**
 * Output the description for the current group in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_description( $group = false ) {
	echo sz_get_group_description( $group );
}
	/**
	 * Return the description for the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_description( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the description for the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Description for the current group.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_description', stripslashes( $group->description ), $group );
	}

/**
 * Output the description for the current group in the loop, for use in a textarea.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_description_editable( $group = false ) {
	echo sz_get_group_description_editable( $group );
}
	/**
	 * Return the permalink for the current group in the loop, for use in a textarea.
	 *
	 * 'sz_get_group_description_editable' does not have the formatting
	 * filters that 'sz_get_group_description' has, which makes it
	 * appropriate for "raw" editing.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_description_editable( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the permalink for the current group in the loop, for use in a textarea.
		 *
		 * 'sz_get_group_description_editable' does not have the formatting filters that
		 * 'sz_get_group_description' has, which makes it appropriate for "raw" editing.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $description Description for the current group in the loop.
		 * @param object $group       Group object.
		 */
		return apply_filters( 'sz_get_group_description_editable', $group->description, $group );
	}

/**
 * Output an excerpt of the group description.
 *
 * @since 1.0.0
 *
 * @param object|bool $group  Optional. The group being referenced.
 *                            Defaults to the group currently being
 *                            iterated on in the groups loop.
 * @param int         $length Optional. Length of returned string, including ellipsis.
 *                            Default: 225.
 */
function sz_group_description_excerpt( $group = false, $length = 225 ) {
	echo sz_get_group_description_excerpt( $group, $length );
}
	/**
	 * Get an excerpt of a group description.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group  Optional. The group being referenced.
	 *                            Defaults to the group currently being
	 *                            iterated on in the groups loop.
	 * @param int         $length Optional. Length of returned string, including ellipsis.
	 *                            Default: 225.
	 * @return string Excerpt.
	 */
	function sz_get_group_description_excerpt( $group = false, $length = 225 ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the excerpt of a group description.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Excerpt of a group description.
		 * @param object $group Object for group whose description is made into an excerpt.
		 */
		return apply_filters( 'sz_get_group_description_excerpt', sz_create_excerpt( $group->description, $length ), $group );
	}

/**
 * Output the status of the current group in the loop.
 *
 * Either 'Public' or 'Private'.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_public_status( $group = false ) {
	echo sz_get_group_public_status( $group );
}
	/**
	 * Return the status of the current group in the loop.
	 *
	 * Either 'Public' or 'Private'.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_public_status( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		if ( $group->is_public ) {
			return __( 'Public', 'sportszone' );
		} else {
			return __( 'Private', 'sportszone' );
		}
	}

/**
 * Output whether the current group in the loop is public.
 *
 * No longer used in SportsZone.
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_is_public( $group = false ) {
	echo sz_get_group_is_public( $group );
}
	/**
	 * Return whether the current group in the loop is public.
	 *
	 * No longer used in SportsZone.
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return mixed
	 */
	function sz_get_group_is_public( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters whether the current group in the loop is public.
		 *
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param bool   $public True if the group is public.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_is_public', $group->is_public, $group );
	}

/**
 * Output the created date of the current group in the loop.
 *
 * @since 1.0.0
 * @since 2.7.0 Added $args as a parameter.
 *
 * @param object|bool  $group Optional. Group object. Default: current group in loop.
 * @param array|string $args  {@see sz_get_group_date_created()}.
 */
function sz_group_date_created( $group = false, $args = array() ) {
	echo sz_get_group_date_created( $group, $args );
}
	/**
	 * Return the created date of the current group in the loop.
	 *
	 * @since 1.0.0
	 * @since 2.7.0 Added $args as a parameter.
	 *
	 * @param object|bool  $group Optional. Group object. Default: current group in loop.
	 * @param array|string $args {
	 *     Array of optional parameters.
	 *
	 *     @type bool $relative Optional. If true, returns relative created date. eg. active 5 months ago.
	 *                          If false, returns created date value from database. Default: true.
	 * }
	 * @return string
	 */
	function sz_get_group_date_created( $group = false, $args = array() ) {
		global $groups_template;

		$r = sz_parse_args( $args, array(
			'relative' => true,
		), 'group_date_created' );

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		// We do not want relative time, so return now.
		// @todo Should the 'sz_get_group_date_created' filter be applied here?
		if ( ! $r['relative'] ) {
			return esc_attr( $group->date_created );
		}

		/**
		 * Filters the created date of the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Created date for the current group.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_date_created', sz_core_time_since( $group->date_created ), $group );
	}

/**
 * Output the username of the creator of the current group in the loop.
 *
 * @since 1.7.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_creator_username( $group = false ) {
	echo sz_get_group_creator_username( $group );
}
	/**
	 * Return the username of the creator of the current group in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_creator_username( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the username of the creator of the current group in the loop.
		 *
		 * @since 1.7.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Username of the group creator.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_creator_username', sz_core_get_user_displayname( $group->creator_id ), $group );
	}

/**
 * Output the user ID of the creator of the current group in the loop.
 *
 * @since 1.7.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_creator_id( $group = false ) {
	echo sz_get_group_creator_id( $group );
}
	/**
	 * Return the user ID of the creator of the current group in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return int
	 */
	function sz_get_group_creator_id( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the user ID of the creator of the current group in the loop.
		 *
		 * @since 1.7.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param int $creator_id User ID of the group creator.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_creator_id', $group->creator_id, $group );
	}

/**
 * Output the permalink of the creator of the current group in the loop.
 *
 * @since 1.7.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_creator_permalink( $group = false ) {
	echo sz_get_group_creator_permalink( $group );
}
	/**
	 * Return the permalink of the creator of the current group in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_creator_permalink( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the permalink of the creator of the current group in the loop.
		 *
		 * @since 1.7.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Permalink of the group creator.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_creator_permalink', sz_core_get_user_domain( $group->creator_id ), $group );
	}

/**
 * Determine whether a user is the creator of the current group in the loop.
 *
 * @since 1.7.0
 *
 * @param SZ_Groups_Group|null $group   Optional. Group object. Default: current group in loop.
 * @param int                  $user_id ID of the user.
 * @return bool
 */
function sz_is_group_creator( $group = null, $user_id = 0 ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( empty( $user_id ) ) {
		$user_id = sz_loggedin_user_id();
	}

	return (bool) ( $group->creator_id == $user_id );
}

/**
 * Output the avatar of the creator of the current group in the loop.
 *
 * @since 1.7.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 * @param array       $args {
 *     Array of optional arguments. See {@link sz_get_group_creator_avatar()}
 *     for description.
 * }
 */
function sz_group_creator_avatar( $group = false, $args = array() ) {
	echo sz_get_group_creator_avatar( $group, $args );
}
	/**
	 * Return the avatar of the creator of the current group in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @param array       $args {
	 *     Array of optional arguments. See {@link sz_core_fetch_avatar()}
	 *     for detailed description of arguments.
	 *     @type string $type   Default: 'full'.
	 *     @type int    $width  Default: false.
	 *     @type int    $height Default: false.
	 *     @type int    $class  Default: 'avatar'.
	 *     @type string $id     Passed to 'css_id'. Default: false.
	 *     @type string $alt    Alt text. Default: 'Group creator profile
	 *                          photo of [user display name]'.
	 * }
	 * @return string
	 */
	function sz_get_group_creator_avatar( $group = false, $args = array() ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		$r = sz_parse_args( $args, array(
			'type'   => 'full',
			'width'  => false,
			'height' => false,
			'class'  => 'avatar',
			'id'     => false,
			'alt'    => sprintf( __( 'Group creator profile photo of %s', 'sportszone' ),  sz_core_get_user_displayname( $group->creator_id ) )
		), 'group_creator_avatar' );
		extract( $r, EXTR_SKIP );

		$avatar = sz_core_fetch_avatar( array( 'item_id' => $group->creator_id, 'type' => $type, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height, 'alt' => $alt ) );

		/**
		 * Filters the avatar of the creator of the current group in the loop.
		 *
		 * @since 1.7.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $avatar Avatar of the group creator.
		 * @param object $group  Group object.
		 */
		return apply_filters( 'sz_get_group_creator_avatar', $avatar, $group );
	}

/**
 * Determine whether the current user is the admin of the current group.
 *
 * Alias of {@link sz_is_item_admin()}.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function sz_group_is_admin() {
	return sz_is_item_admin();
}

/**
 * Determine whether the current user is a mod of the current group.
 *
 * Alias of {@link sz_is_item_mod()}.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function sz_group_is_mod() {
	return sz_is_item_mod();
}

/**
 * Output markup listing group admins.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_list_admins( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( ! empty( $group->admins ) ) { ?>
		<ul id="group-admins">
			<?php foreach( (array) $group->admins as $admin ) { ?>
				<li>
					<a href="<?php echo sz_core_get_user_domain( $admin->user_id, $admin->user_nicename, $admin->user_login ) ?>" class="sz-tooltip" data-sz-tooltip="<?php printf( ('%s'),  sz_core_get_user_displayname( $admin->user_id ) ); ?>"><?php echo sz_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'email' => $admin->user_email, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $admin->user_id ) ) ) ) ?></a>
				</li>
			<?php } ?>
		</ul>
	<?php } else { ?>
		<span class="activity"><?php _e( 'No Admins', 'sportszone' ) ?></span>
	<?php } ?>
<?php
}

/**
 * Output markup listing group mod.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in loop.
 */
function sz_group_list_mods( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( ! empty( $group->mods ) ) : ?>

		<ul id="group-mods">

			<?php foreach( (array) $group->mods as $mod ) { ?>

				<li>
					<a href="<?php echo sz_core_get_user_domain( $mod->user_id, $mod->user_nicename, $mod->user_login ) ?>" class="sz-tooltip" data-sz-tooltip="<?php printf( ('%s'),  sz_core_get_user_displayname( $mod->user_id ) ); ?>"><?php echo sz_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'email' => $mod->user_email, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $mod->user_id ) ) ) ) ?></a>
				</li>

			<?php } ?>

		</ul>

<?php else : ?>

		<span class="activity"><?php _e( 'No Mods', 'sportszone' ) ?></span>

<?php endif;

}

/**
 * Return a list of user IDs for a group's admins.
 *
 * @since 1.5.0
 *
 * @param SZ_Groups_Group|bool $group     Optional. The group being queried. Defaults
 *                                        to the current group in the loop.
 * @param string               $format    Optional. 'string' to get a comma-separated string,
 *                                        'array' to get an array.
 * @return mixed               $admin_ids A string or array of user IDs.
 */
function sz_group_admin_ids( $group = false, $format = 'string' ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	$admin_ids = array();

	if ( $group->admins ) {
		foreach( $group->admins as $admin ) {
			$admin_ids[] = $admin->user_id;
		}
	}

	if ( 'string' == $format ) {
		$admin_ids = implode( ',', $admin_ids );
	}

	/**
	 * Filters a list of user IDs for a group's admins.
	 *
	 * This filter may return either an array or a comma separated string.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 Added the `$group` parameter.
	 *
	 * @param array|string $admin_ids List of user IDs for a group's admins.
	 * @param object       $group     Group object.
	 */
	return apply_filters( 'sz_group_admin_ids', $admin_ids, $group );
}

/**
 * Return a list of user IDs for a group's moderators.
 *
 * @since 1.5.0
 *
 * @param SZ_Groups_Group|bool $group   Optional. The group being queried.
 *                                      Defaults to the current group in the loop.
 * @param string               $format  Optional. 'string' to get a comma-separated string,
 *                                      'array' to get an array.
 * @return mixed               $mod_ids A string or array of user IDs.
 */
function sz_group_mod_ids( $group = false, $format = 'string' ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	$mod_ids = array();

	if ( $group->mods ) {
		foreach( $group->mods as $mod ) {
			$mod_ids[] = $mod->user_id;
		}
	}

	if ( 'string' == $format ) {
		$mod_ids = implode( ',', $mod_ids );
	}

	/**
	 * Filters a list of user IDs for a group's moderators.
	 *
	 * This filter may return either an array or a comma separated string.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 Added the `$group` parameter.
	 *
	 * @param array|string $admin_ids List of user IDs for a group's moderators.
	 * @param object       $group     Group object.
	 */
	return apply_filters( 'sz_group_mod_ids', $mod_ids, $group );
}

/**
 * Output the permalink of the current group's Members page.
 *
 * @since 1.0.0
 */
function sz_group_all_members_permalink() {
	echo sz_get_group_all_members_permalink();
}
	/**
	 * Return the permalink of the Members page of the current group in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_all_members_permalink( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the permalink of the Members page for the current group in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Permalink of the Members page for the current group.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_all_members_permalink', trailingslashit( sz_get_group_permalink( $group ) . 'members' ), $group );
	}

/**
 * Display a Groups search form.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_group_search_form() {

	$action = sz_displayed_user_domain() . sz_get_groups_slug() . '/my-groups/search/';
	$label = __('Filter Groups', 'sportszone');
	$name = 'group-filter-box';

	$search_form_html = '<form action="' . $action . '" id="group-search-form" method="post">
		<label for="'. $name .'" id="'. $name .'-label">'. $label .'</label>
		<input type="search" name="'. $name . '" id="'. $name .'" value="'. $value .'"'.  $disabled .' />

		'. wp_nonce_field( 'group-filter-box', '_wpnonce_group_filter', true, false ) .'
		</form>';

	echo apply_filters( 'sz_group_search_form', $search_form_html );
}

/**
 * Determine whether the displayed user has no groups.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 *
 * @return bool True if the displayed user has no groups, otherwise false.
 */
function sz_group_show_no_groups_message() {
	if ( !groups_total_groups_for_user( sz_displayed_user_id() ) ) {
		return true;
	}

	return false;
}

/**
 * Determine whether the current page is a group activity permalink.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 *
 * @return bool True if this is a group activity permalink, otherwise false.
 */
function sz_group_is_activity_permalink() {

	if ( !sz_is_single_item() || !sz_is_groups_component() || !sz_is_current_action( sz_get_activity_slug() ) ) {
		return false;
	}

	return true;
}

/**
 * Output the pagination HTML for a group loop.
 *
 * @since 1.2.0
 */
function sz_groups_pagination_links() {
	echo sz_get_groups_pagination_links();
}
	/**
	 * Get the pagination HTML for a group loop.
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	function sz_get_groups_pagination_links() {
		global $groups_template;

		/**
		 * Filters the pagination HTML for a group loop.
		 *
		 * @since 1.2.0
		 *
		 * @param string $pag_links HTML markup for the pagination links.
		 */
		return apply_filters( 'sz_get_groups_pagination_links', $groups_template->pag_links );
	}

/**
 * Output the "Viewing x-y of z groups" pagination message.
 *
 * @since 1.2.0
 */
function sz_groups_pagination_count() {
	echo sz_get_groups_pagination_count();
}
	/**
	 * Generate the "Viewing x-y of z groups" pagination message.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_groups_pagination_count() {
		global $groups_template;

		$start_num = intval( ( $groups_template->pag_page - 1 ) * $groups_template->pag_num ) + 1;
		$from_num  = sz_core_number_format( $start_num );
		$to_num    = sz_core_number_format( ( $start_num + ( $groups_template->pag_num - 1 ) > $groups_template->total_group_count ) ? $groups_template->total_group_count : $start_num + ( $groups_template->pag_num - 1 ) );
		$total     = sz_core_number_format( $groups_template->total_group_count );

		if ( 1 == $groups_template->total_group_count ) {
			$message = __( 'Viewing 1 group', 'sportszone' );
		} else {
			$message = sprintf( _n( 'Viewing %1$s - %2$s of %3$s group', 'Viewing %1$s - %2$s of %3$s groups', $groups_template->total_group_count, 'sportszone' ), $from_num, $to_num, $total );
		}

		/**
		 * Filters the "Viewing x-y of z groups" pagination message.
		 *
		 * @since 1.5.0
		 *
		 * @param string $message  "Viewing x-y of z groups" text.
		 * @param string $from_num Total amount for the low value in the range.
		 * @param string $to_num   Total amount for the high value in the range.
		 * @param string $total    Total amount of groups found.
		 */
		return apply_filters( 'sz_get_groups_pagination_count', $message, $from_num, $to_num, $total );
	}

/**
 * Determine whether groups auto-join is enabled.
 *
 * "Auto-join" is the toggle that determines whether users are joined to a
 * public group automatically when creating content in that group.
 *
 * @since 1.2.6
 *
 * @return bool
 */
function sz_groups_auto_join() {

	/**
	 * Filters whether groups auto-join is enabled.
	 *
	 * @since 1.2.6
	 *
	 * @param bool $value Enabled status.
	 */
	return apply_filters( 'sz_groups_auto_join', (bool) sportszone()->groups->auto_join );
}

/**
 * Output the total member count for a group.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object. Default: current group in loop.
 */
function sz_group_total_members( $group = false ) {
	echo sz_get_group_total_members( $group );
}
	/**
	 * Get the total member count for a group.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return int
	 */
	function sz_get_group_total_members( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the total member count for a group.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param int    $total_member_count Total member count for a group.
		 * @param object $group              Group object.
		 */
		return apply_filters( 'sz_get_group_total_members', $group->total_member_count, $group );
	}

/**
 * Output the "x members" count string for a group.
 *
 * @since 1.2.0
 */
function sz_group_member_count() {
	echo sz_get_group_member_count();
}
	/**
	 * Generate the "x members" count string for a group.
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	function sz_get_group_member_count() {
		global $groups_template;

		if ( isset( $groups_template->group->total_member_count ) ) {
			$count = (int) $groups_template->group->total_member_count;
		} else {
			$count = 0;
		}

		$count_string = sprintf( _n( '%s member', '%s members', $count, 'sportszone' ), sz_core_number_format( $count ) );

		/**
		 * Filters the "x members" count string for a group.
		 *
		 * @since 1.2.0
		 *
		 * @param string $count_string The "x members" count string for a group.
		 */
		return apply_filters( 'sz_get_group_member_count', $count_string );
	}

/**
 * Output the URL of the Forum page of the current group in the loop.
 *
 * @since 1.0.0
 */
function sz_group_forum_permalink() {
	echo sz_get_group_forum_permalink();
}
	/**
	 * Generate the URL of the Forum page of a group.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in loop.
	 * @return string
	 */
	function sz_get_group_forum_permalink( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the URL of the Forum page of a group.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value URL permalink for the Forum Page.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_forum_permalink', trailingslashit( sz_get_group_permalink( $group ) . 'forum' ), $group );
	}

/**
 * Determine whether forums are enabled for a group.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object. Default: current group in loop.
 * @return bool
 */
function sz_group_is_forum_enabled( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( ! empty( $group->enable_forum ) ) {
		return true;
	}

	return false;
}

/**
 * Output the 'checked' attribute for the group forums settings UI.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object. Default: current group in loop.
 */
function sz_group_show_forum_setting( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( $group->enable_forum ) {
		echo ' checked="checked"';
	}
}

/**
 * Output the 'checked' attribute for a given status in the settings UI.
 *
 * @since 1.0.0
 *
 * @param string      $setting Group status. 'public', 'private', 'hidden'.
 * @param object|bool $group   Optional. Group object. Default: current group in loop.
 */
function sz_group_show_status_setting( $setting, $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( $setting == $group->status ) {
		echo ' checked="checked"';
	}
}

/**
 * Output the 'checked' value, if needed, for a given invite_status on the group create/admin screens
 *
 * @since 1.5.0
 *
 * @param string      $setting The setting you want to check against ('members',
 *                             'mods', or 'admins').
 * @param object|bool $group   Optional. Group object. Default: current group in loop.
 */
function sz_group_show_invite_status_setting( $setting, $group = false ) {
	$group_id = isset( $group->id ) ? $group->id : false;

	$invite_status = sz_group_get_invite_status( $group_id );

	if ( $setting == $invite_status ) {
		echo ' checked="checked"';
	}
}

/**
 * Get the invite status of a group.
 *
 * 'invite_status' became part of SportsZone in BP 1.5. In order to provide
 * backward compatibility with earlier installations, groups without a status
 * set will default to 'members', ie all members in a group can send
 * invitations. Filter 'sz_group_invite_status_fallback' to change this
 * fallback behavior.
 *
 * This function can be used either in or out of the loop.
 *
 * @since 1.5.0
 *
 * @param int|bool $group_id Optional. The ID of the group whose status you want to
 *                           check. Default: the displayed group, or the current group
 *                           in the loop.
 * @return bool|string Returns false when no group can be found. Otherwise
 *                     returns the group invite status, from among 'members',
 *                     'mods', and 'admins'.
 */
function sz_group_get_invite_status( $group_id = false ) {
	global $groups_template;

	if ( !$group_id ) {
		$sz = sportszone();

		if ( isset( $sz->groups->current_group->id ) ) {
			// Default to the current group first.
			$group_id = $sz->groups->current_group->id;
		} elseif ( isset( $groups_template->group->id ) ) {
			// Then see if we're in the loop.
			$group_id = $groups_template->group->id;
		} else {
			return false;
		}
	}

	$invite_status = groups_get_groupmeta( $group_id, 'invite_status' );

	// Backward compatibility. When 'invite_status' is not set, fall back to a default value.
	if ( !$invite_status ) {
		$invite_status = apply_filters( 'sz_group_invite_status_fallback', 'members' );
	}

	/**
	 * Filters the invite status of a group.
	 *
	 * Invite status in this case means who from the group can send invites.
	 *
	 * @since 1.5.0
	 *
	 * @param string $invite_status Membership level needed to send an invite.
	 * @param int    $group_id      ID of the group whose status is being checked.
	 */
	return apply_filters( 'sz_group_get_invite_status', $invite_status, $group_id );
}

/**
 * Can a user send invitations in the specified group?
 *
 * @since 1.5.0
 * @since 2.2.0 Added the $user_id parameter.
 *
 * @param int $group_id The group ID to check.
 * @param int $user_id  The user ID to check.
 * @return bool
 */
function sz_groups_user_can_send_invites( $group_id = 0, $user_id = 0 ) {
	$can_send_invites = false;
	$invite_status    = false;

	// If $user_id isn't specified, we check against the logged-in user.
	if ( ! $user_id ) {
		$user_id = sz_loggedin_user_id();
	}

	// If $group_id isn't specified, use existing one if available.
	if ( ! $group_id ) {
		$group_id = sz_get_current_group_id();
	}

	if ( $user_id ) {
		$can_send_invites = sz_user_can( $user_id, 'groups_send_invitation', array( 'group_id' => $group_id ) );
	}

	/**
	 * Filters whether a user can send invites in a group.
	 *
	 * @since 1.5.0
	 * @since 2.2.0 Added the $user_id parameter.
	 *
	 * @param bool $can_send_invites Whether the user can send invites
	 * @param int  $group_id         The group ID being checked
	 * @param bool $invite_status    The group's current invite status
	 * @param int  $user_id          The user ID being checked
	 */
	return apply_filters( 'sz_groups_user_can_send_invites', $can_send_invites, $group_id, $invite_status, $user_id );
}

/**
 * Since SportsZone 1.0, this generated the group settings admin/member screen.
 * As of SportsZone 1.5 (r4489), and because this function outputs HTML, it was moved into /sz-default/groups/single/admin.php.
 *
 * @deprecated 1.5
 * @deprecated No longer used.
 * @since 1.0.0
 * @todo Remove in 1.4
 *
 * @param bool $admin_list
 * @param bool $group
 */
function sz_group_admin_memberlist( $admin_list = false, $group = false ) {
	global $groups_template;

	_deprecated_function( __FUNCTION__, '1.5', 'No longer used. See /sz-default/groups/single/admin.php' );

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}


	if ( $admins = groups_get_group_admins( $group->id ) ) : ?>

		<ul id="admins-list" class="item-list<?php if ( !empty( $admin_list ) ) : ?> single-line<?php endif; ?>">

		<?php foreach ( (array) $admins as $admin ) { ?>

			<?php if ( !empty( $admin_list ) ) : ?>

			<li>

				<?php echo sz_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $admin->user_id ) ) ) ) ?>

				<h5>

					<?php echo sz_core_get_userlink( $admin->user_id ); ?>

					<span class="small">
						<a class="button confirm admin-demote-to-member" href="<?php sz_group_member_demote_link($admin->user_id) ?>"><?php _e( 'Demote to Member', 'sportszone' ) ?></a>
					</span>
				</h5>
			</li>

			<?php else : ?>

			<li>

				<?php echo sz_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'type' => 'thumb', 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $admin->user_id ) ) ) ) ?>

				<h5><?php echo sz_core_get_userlink( $admin->user_id ) ?></h5>
				<span class="activity">
					<?php echo sz_core_get_last_activity( strtotime( $admin->date_modified ), __( 'joined %s', 'sportszone') ); ?>
				</span>

				<?php if ( sz_is_active( 'friends' ) ) : ?>

					<div class="action">

						<?php sz_add_friend_button( $admin->user_id ); ?>

					</div>

				<?php endif; ?>

			</li>

			<?php endif;
		} ?>

		</ul>

	<?php else : ?>

		<div id="message" class="info">
			<p><?php _e( 'This group has no administrators', 'sportszone' ); ?></p>
		</div>

	<?php endif;
}

/**
 * Generate the HTML for a list of group moderators.
 *
 * No longer used.
 *
 * @todo Deprecate.
 *
 * @param bool $admin_list
 * @param bool $group
 */
function sz_group_mod_memberlist( $admin_list = false, $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( $group_mods = groups_get_group_mods( $group->id ) ) { ?>

		<ul id="mods-list" class="item-list<?php if ( $admin_list ) { ?> single-line<?php } ?>">

		<?php foreach ( (array) $group_mods as $mod ) { ?>

			<?php if ( !empty( $admin_list ) ) { ?>

			<li>

				<?php echo sz_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $mod->user_id ) ) ) ) ?>

				<h5>
					<?php echo sz_core_get_userlink( $mod->user_id ); ?>

					<span class="small">
						<a href="<?php sz_group_member_promote_admin_link( array( 'user_id' => $mod->user_id ) ) ?>" class="button confirm mod-promote-to-admin"><?php _e( 'Promote to Admin', 'sportszone' ); ?></a>
						<a class="button confirm mod-demote-to-member" href="<?php sz_group_member_demote_link($mod->user_id) ?>"><?php _e( 'Demote to Member', 'sportszone' ) ?></a>
					</span>
				</h5>
			</li>

			<?php } else { ?>

			<li>

				<?php echo sz_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'type' => 'thumb', 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $mod->user_id ) ) ) ) ?>

				<h5><?php echo sz_core_get_userlink( $mod->user_id ) ?></h5>

				<span class="activity"><?php echo sz_core_get_last_activity( strtotime( $mod->date_modified ), __( 'joined %s', 'sportszone') ); ?></span>

				<?php if ( sz_is_active( 'friends' ) ) : ?>

					<div class="action">
						<?php sz_add_friend_button( $mod->user_id ) ?>
					</div>

				<?php endif; ?>

			</li>

			<?php } ?>
		<?php } ?>

		</ul>

	<?php } else { ?>

		<div id="message" class="info">
			<p><?php _e( 'This group has no moderators', 'sportszone' ); ?></p>
		</div>

	<?php }
}

/**
 * Determine whether a group has moderators.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object. Default: current group in loop.
 * @return array Info about group admins (user_id + date_modified).
 */
function sz_group_has_moderators( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	/**
	 * Filters whether a group has moderators.
	 *
	 * @since 1.0.0
	 * @since 2.5.0 Added the `$group` parameter.
	 *
	 * @param array  $value Array of user IDs who are a moderator of the provided group.
	 * @param object $group Group object.
	 */
	return apply_filters( 'sz_group_has_moderators', groups_get_group_mods( $group->id ), $group );
}

/**
 * Output a URL for promoting a user to moderator.
 *
 * @since 1.1.0
 *
 * @param array|string $args See {@link sz_get_group_member_promote_mod_link()}.
 */
function sz_group_member_promote_mod_link( $args = '' ) {
	echo sz_get_group_member_promote_mod_link( $args );
}
	/**
	 * Generate a URL for promoting a user to moderator.
	 *
	 * @since 1.1.0
	 *
	 * @param array|string $args {
	 *     @type int    $user_id ID of the member to promote. Default:
	 *                           current member in a group member loop.
	 *     @type object $group   Group object. Default: current group.
	 * }
	 * @return string
	 */
	function sz_get_group_member_promote_mod_link( $args = '' ) {
		global $members_template, $groups_template;

		$r = sz_parse_args( $args, array(
			'user_id' => $members_template->member->user_id,
			'group'   => &$groups_template->group
		), 'group_member_promote_mod_link' );
		extract( $r, EXTR_SKIP );

		/**
		 * Filters a URL for promoting a user to moderator.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value URL to use for promoting a user to moderator.
		 */
		return apply_filters( 'sz_get_group_member_promote_mod_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'admin/manage-members/promote/mod/' . $user_id ), 'groups_promote_member' ) );
	}

/**
 * Output a URL for promoting a user to admin.
 *
 * @since 1.1.0
 *
 * @param array|string $args See {@link sz_get_group_member_promote_admin_link()}.
 */
function sz_group_member_promote_admin_link( $args = '' ) {
	echo sz_get_group_member_promote_admin_link( $args );
}
	/**
	 * Generate a URL for promoting a user to admin.
	 *
	 * @since 1.1.0
	 *
	 * @param array|string $args {
	 *     @type int    $user_id ID of the member to promote. Default:
	 *                           current member in a group member loop.
	 *     @type object $group   Group object. Default: current group.
	 * }
	 * @return string
	 */
	function sz_get_group_member_promote_admin_link( $args = '' ) {
		global $members_template, $groups_template;

		$r = sz_parse_args( $args, array(
			'user_id' => !empty( $members_template->member->user_id ) ? $members_template->member->user_id : false,
			'group'   => &$groups_template->group
		), 'group_member_promote_admin_link' );
		extract( $r, EXTR_SKIP );

		/**
		 * Filters a URL for promoting a user to admin.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value URL to use for promoting a user to admin.
		 */
		return apply_filters( 'sz_get_group_member_promote_admin_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'admin/manage-members/promote/admin/' . $user_id ), 'groups_promote_member' ) );
	}

/**
 * Output a URL for demoting a user to member.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the member to demote. Default: current member in
 *                     a member loop.
 */
function sz_group_member_demote_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id ) {
		$user_id = $members_template->member->user_id;
	}

	echo sz_get_group_member_demote_link( $user_id );
}
	/**
	 * Generate a URL for demoting a user to member.
	 *
	 * @since 1.0.0
	 *
	 * @param int         $user_id ID of the member to demote. Default: current
	 *                             member in a member loop.
	 * @param object|bool $group   Optional. Group object. Default: current group.
	 * @return string
	 */
	function sz_get_group_member_demote_link( $user_id = 0, $group = false ) {
		global $members_template, $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		if ( !$user_id ) {
			$user_id = $members_template->member->user_id;
		}

		/**
		 * Filters a URL for demoting a user to member.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value URL to use for demoting a user to member.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_member_demote_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'admin/manage-members/demote/' . $user_id ), 'groups_demote_member' ), $group );
	}

/**
 * Output a URL for banning a member from a group.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the member to ban.
 *                     Default: current member in a member loop.
 */
function sz_group_member_ban_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id ) {
		$user_id = $members_template->member->user_id;
	}

	echo sz_get_group_member_ban_link( $user_id );
}
	/**
	 * Generate a URL for banning a member from a group.
	 *
	 * @since 1.0.0
	 *
	 * @param int         $user_id ID of the member to ban.
	 *                             Default: current member in a member loop.
	 * @param object|bool $group   Optional. Group object. Default: current group.
	 * @return string
	 */
	function sz_get_group_member_ban_link( $user_id = 0, $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters a URL for banning a member from a group.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value URL to use for banning a member.
		 */
		return apply_filters( 'sz_get_group_member_ban_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'admin/manage-members/ban/' . $user_id ), 'groups_ban_member' ) );
	}

/**
 * Output a URL for unbanning a member from a group.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the member to unban.
 *                     Default: current member in a member loop.
 */
function sz_group_member_unban_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id ) {
		$user_id = $members_template->member->user_id;
	}

	echo sz_get_group_member_unban_link( $user_id );
}
	/**
	 * Generate a URL for unbanning a member from a group.
	 *
	 * @since 1.0.0
	 *
	 * @param int         $user_id ID of the member to unban.
	 *                             Default: current member in a member loop.
	 * @param object|bool $group   Optional. Group object. Default: current group.
	 * @return string
	 */
	function sz_get_group_member_unban_link( $user_id = 0, $group = false ) {
		global $members_template, $groups_template;

		if ( !$user_id ) {
			$user_id = $members_template->member->user_id;
		}

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters a URL for unbanning a member from a group.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value URL to use for unbanning a member.
		 */
		return apply_filters( 'sz_get_group_member_unban_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'admin/manage-members/unban/' . $user_id ), 'groups_unban_member' ) );
	}

/**
 * Output a URL for removing a member from a group.
 *
 * @since 1.2.6
 *
 * @param int $user_id ID of the member to remove.
 *                     Default: current member in a member loop.
 */
function sz_group_member_remove_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id ) {
		$user_id = $members_template->member->user_id;
	}

	echo sz_get_group_member_remove_link( $user_id );
}
	/**
	 * Generate a URL for removing a member from a group.
	 *
	 * @since 1.2.6
	 *
	 * @param int         $user_id ID of the member to remove.
	 *                             Default: current member in a member loop.
	 * @param object|bool $group   Optional. Group object. Default: current group.
	 * @return string
	 */
	function sz_get_group_member_remove_link( $user_id = 0, $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters a URL for removing a member from a group.
		 *
		 * @since 1.2.6
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value URL to use for removing a member.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_member_remove_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'admin/manage-members/remove/' . $user_id ), 'groups_remove_member' ), $group );
	}

/**
 * HTML admin subnav items for group pages.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in the loop.
 */
function sz_group_admin_tabs( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group = ( $groups_template->group ) ? $groups_template->group : groups_get_current_group();
	}

	$css_id = 'manage-members';

	if ( 'private' == $group->status ) {
		$css_id = 'membership-requests';
	}

	add_filter( "sz_get_options_nav_{$css_id}", 'sz_group_admin_tabs_backcompat', 10, 3 );

	sz_get_options_nav( $group->slug . '_manage' );

	remove_filter( "sz_get_options_nav_{$css_id}", 'sz_group_admin_tabs_backcompat', 10 );
}

/**
 * BackCompat for plugins/themes directly hooking groups_admin_tabs
 * without using the Groups Extension API.
 *
 * @since 2.2.0
 *
 * @param  string $subnav_output Subnav item output.
 * @param  string $subnav_item   subnav item params.
 * @param  string $selected_item Surrent selected tab.
 * @return string HTML output
 */
function sz_group_admin_tabs_backcompat( $subnav_output = '', $subnav_item = '', $selected_item = '' ) {
	if ( ! has_action( 'groups_admin_tabs' ) ) {
		return $subnav_output;
	}

	$group = groups_get_current_group();

	ob_start();

	do_action( 'groups_admin_tabs', $selected_item, $group->slug );

	$admin_tabs_backcompat = trim( ob_get_contents() );
	ob_end_clean();

	if ( ! empty( $admin_tabs_backcompat ) ) {
		_doing_it_wrong( "do_action( 'groups_admin_tabs' )", __( 'This action should not be used directly. Please use the SportsZone Group Extension API to generate Manage tabs.', 'sportszone' ), '2.2.0' );
		$subnav_output .= $admin_tabs_backcompat;
	}

	return $subnav_output;
}

/**
 * Output the group count for the displayed user.
 *
 * @since 1.1.0
 */
function sz_group_total_for_member() {
	echo sz_get_group_total_for_member();
}
	/**
	 * Get the group count for the displayed user.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	function sz_get_group_total_for_member() {

		/**
		 * FIlters the group count for a displayed user.
		 *
		 * @since 1.1.0
		 *
		 * @param int $value Total group count for a displayed user.
		 */
		return apply_filters( 'sz_get_group_total_for_member', SZ_Groups_Member::total_group_count() );
	}

/**
 * Output the 'action' attribute for a group form.
 *
 * @since 1.0.0
 *
 * @param string $page Page slug.
 */
function sz_group_form_action( $page ) {
	echo sz_get_group_form_action( $page );
}
	/**
	 * Generate the 'action' attribute for a group form.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $page  Page slug.
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in the loop.
	 * @return string
	 */
	function sz_get_group_form_action( $page, $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the 'action' attribute for a group form.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Action attribute for a group form.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_group_form_action', trailingslashit( sz_get_group_permalink( $group ) . $page ), $group );
	}

/**
 * Output the 'action' attribute for a group admin form.
 *
 * @since 1.0.0
 *
 * @param string|bool $page Optional. Page slug.
 */
function sz_group_admin_form_action( $page = false ) {
	echo sz_get_group_admin_form_action( $page );
}
	/**
	 * Generate the 'action' attribute for a group admin form.
	 *
	 * @since 1.0.0
	 *
	 * @param string|bool $page  Optional. Page slug.
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in the loop.
	 * @return string
	 */
	function sz_get_group_admin_form_action( $page = false, $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		if ( empty( $page ) ) {
			$page = sz_action_variable( 0 );
		}

		/**
		 * Filters the 'action' attribute for a group admin form.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Action attribute for a group admin form.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_group_admin_form_action', trailingslashit( sz_get_group_permalink( $group ) . 'admin/' . $page ), $group );
	}

/**
 * Determine whether the logged-in user has requested membership to a group.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in the loop.
 * @return bool
 */
function sz_group_has_requested_membership( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( groups_check_for_membership_request( sz_loggedin_user_id(), $group->id ) ) {
		return true;
	}

	return false;
}

/**
 * Check if current user is member of a group.
 *
 * @since 1.0.0
 *
 * @global object $groups_template
 *
 * @param object|bool $group Optional. Group to check is_member.
 *                           Default: current group in the loop.
 * @return bool If user is member of group or not.
 */
function sz_group_is_member( $group = false ) {
	global $groups_template;

	// Site admins always have access.
	if ( sz_current_user_can( 'sz_moderate' ) ) {
		return true;
	}

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	/**
	 * Filters whether current user is member of a group.
	 *
	 * @since 1.2.4
	 * @since 2.5.0 Added the `$group` parameter.
	 *
	 * @param bool   $is_member If user is a member of group or not.
	 * @param object $group     Group object.
	 */
	return apply_filters( 'sz_group_is_member', ! empty( $group->is_member ), $group );
}

/**
 * Check whether the current user has an outstanding invite to the current group in the loop.
 *
 * @since 2.1.0
 *
 * @param object|bool $group Optional. Group data object.
 *                           Default: the current group in the groups loop.
 * @return bool True if the user has an outstanding invite, otherwise false.
 */
function sz_group_is_invited( $group = false ) {
	global $groups_template;

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	/**
	 * Filters whether current user has an outstanding invite to current group in loop.
	 *
	 * @since 2.1.0
	 * @since 2.5.0 Added the `$group` parameter.
	 *
	 * @param bool   $is_invited If user has an outstanding group invite.
	 * @param object $group      Group object.
	 */
	return apply_filters( 'sz_group_is_invited', ! empty( $group->is_invited ), $group );
}

/**
 * Check if a user is banned from a group.
 *
 * If this function is invoked inside the groups template loop, then we check
 * $groups_template->group->is_banned instead of using {@link groups_is_user_banned()}
 * and making another SQL query.
 *
 * In SportsZone 2.1, to standardize this function, we are defaulting the
 * return value to a boolean.  In previous versions, using this function would
 * return either a string of the integer (0 or 1) or null if a result couldn't
 * be found from the database.  If the logged-in user had the 'sz_moderate'
 * capability, the return value would be boolean false.
 *
 * @since 1.5.0
 *
 * @global SZ_Groups_Template $groups_template Group template loop object.
 *
 * @param SZ_Groups_Group|bool $group   Group to check if user is banned.
 * @param int                  $user_id The user ID to check.
 * @return bool True if user is banned.  False if user isn't banned.
 */
function sz_group_is_user_banned( $group = false, $user_id = 0 ) {
	global $groups_template;

	// Site admins always have access.
	if ( sz_current_user_can( 'sz_moderate' ) ) {
		return false;
	}

	// Check groups loop first
	// @see SZ_Groups_Group::get_group_extras().
	if ( ! empty( $groups_template->in_the_loop ) && isset( $groups_template->group->is_banned ) ) {
		$retval = $groups_template->group->is_banned;

	// Not in loop.
	} else {
		// Default to not banned.
		$retval = false;

		if ( empty( $group ) ) {
			$group = $groups_template->group;
		}

		if ( empty( $user_id ) ) {
			$user_id = sz_loggedin_user_id();
		}

		if ( ! empty( $user_id ) && ! empty( $group->id ) ) {
			$retval = groups_is_user_banned( $user_id, $group->id );
		}
	}

	/**
	 * Filters whether current user has been banned from current group in loop.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 Added the `$group` parameter.
	 *
	 * @param bool   $is_invited If user has been from current group.
	 * @param object $group      Group object.
	 */
	return (bool) apply_filters( 'sz_group_is_user_banned', $retval, $group );
}

/**
 * Output the URL for accepting an invitation to the current group in the loop.
 *
 * @since 1.0.0
 */
function sz_group_accept_invite_link() {
	echo sz_get_group_accept_invite_link();
}
	/**
	 * Generate the URL for accepting an invitation to a group.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: Current group in the loop.
	 * @return string
	 */
	function sz_get_group_accept_invite_link( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		$sz = sportszone();

		/**
		 * Filters the URL for accepting an invitation to a group.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value URL for accepting an invitation to a group.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_accept_invite_link', wp_nonce_url( trailingslashit( sz_loggedin_user_domain() . sz_get_groups_slug() . '/invites/accept/' . $group->id ), 'groups_accept_invite' ), $group );
	}

/**
 * Output the URL for accepting an invitation to the current group in the loop.
 *
 * @since 1.0.0
 */
function sz_group_reject_invite_link() {
	echo sz_get_group_reject_invite_link();
}
	/**
	 * Generate the URL for rejecting an invitation to a group.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: Current group in the loop.
	 * @return string
	 */
	function sz_get_group_reject_invite_link( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		$sz = sportszone();

		/**
		 * Filters the URL for rejecting an invitation to a group.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value URL for rejecting an invitation to a group.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_reject_invite_link', wp_nonce_url( trailingslashit( sz_loggedin_user_domain() . sz_get_groups_slug() . '/invites/reject/' . $group->id ), 'groups_reject_invite' ), $group );
	}

/**
 * Output the URL for confirming a request to leave a group.
 *
 * @since 1.0.0
 */
function sz_group_leave_confirm_link() {
	echo sz_get_group_leave_confirm_link();
}
	/**
	 * Generate the URL for confirming a request to leave a group.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: Current group in the loop.
	 * @return string
	 */
	function sz_get_group_leave_confirm_link( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the URL for confirming a request to leave a group.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value URL for confirming a request to leave a group.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_group_leave_confirm_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'leave-group/yes' ), 'groups_leave_group' ), $group );
	}

/**
 * Output the URL for rejecting a request to leave a group.
 *
 * @since 1.0.0
 */
function sz_group_leave_reject_link() {
	echo sz_get_group_leave_reject_link();
}
	/**
	 * Generate the URL for rejecting a request to leave a group.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: Current group in the loop.
	 * @return string
	 */
	function sz_get_group_leave_reject_link( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the URL for rejecting a request to leave a group.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value URL for rejecting a request to leave a group.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_get_group_leave_reject_link', sz_get_group_permalink( $group ), $group );
	}

/**
 * Output the 'action' attribute for a group send invite form.
 *
 * @since 1.0.0
 */
function sz_group_send_invite_form_action() {
	echo sz_get_group_send_invite_form_action();
}
	/**
	 * Output the 'action' attribute for a group send invite form.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Optional. Group object.
	 *                           Default: current group in the loop.
	 * @return string
	 */
	function sz_get_group_send_invite_form_action( $group = false ) {
		global $groups_template;

		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		/**
		 * Filters the 'action' attribute for a group send invite form.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$group` parameter.
		 *
		 * @param string $value Action attribute for a group send invite form.
		 * @param object $group Group object.
		 */
		return apply_filters( 'sz_group_send_invite_form_action', trailingslashit( sz_get_group_permalink( $group ) . 'send-invites/send' ), $group );
	}

/**
 * Determine whether the current user has friends to invite to a group.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Optional. Group object.
 *                           Default: current group in the loop.
 * @return bool
 */
function sz_has_friends_to_invite( $group = false ) {
	global $groups_template;

	if ( !sz_is_active( 'friends' ) ) {
		return false;
	}

	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	if ( !friends_check_user_has_friends( sz_loggedin_user_id() ) || !friends_count_invitable_friends( sz_loggedin_user_id(), $group->id ) ) {
		return false;
	}

	return true;
}

/**
 * Output button to join a group.
 *
 * @since 1.0.0
 *
 * @param object|bool $group Single group object.
 */
function sz_group_join_button( $group = false ) {
	echo sz_get_group_join_button( $group );
}
	/**
	 * Return button to join a group.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $group Single group object.
	 * @return false|string
	 */
	function sz_get_group_join_button( $group = false ) {
		global $groups_template;

		// Set group to current loop group if none passed.
		if ( empty( $group ) ) {
			$group =& $groups_template->group;
		}

		// Don't show button if not logged in or previously banned.
		if ( ! is_user_logged_in() || sz_group_is_user_banned( $group ) ) {
			return false;
		}

		// Group creation was not completed or status is unknown.
		if ( empty( $group->status ) ) {
			return false;
		}

		// Already a member.
		if ( ! empty( $group->is_member ) ) {

			// Stop sole admins from abandoning their group.
			$group_admins = groups_get_group_admins( $group->id );
			if ( ( 1 == count( $group_admins ) ) && ( sz_loggedin_user_id() === (int) $group_admins[0]->user_id ) ) {
				return false;
			}

			// Setup button attributes.
			$button = array(
				'id'                => 'leave_group',
				'component'         => 'groups',
				'must_be_logged_in' => true,
				'block_self'        => false,
				'wrapper_class'     => 'group-button ' . $group->status,
				'wrapper_id'        => 'groupbutton-' . $group->id,
				'link_href'         => wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'leave-group' ), 'groups_leave_group' ),
				'link_text'         => __( 'Leave Group', 'sportszone' ),
				'link_class'        => 'group-button leave-group',
			);

		// Not a member.
		} else {

			// Show different buttons based on group status.
			switch ( $group->status ) {
				case 'hidden' :
					return false;

				case 'public':
					$button = array(
						'id'                => 'join_group',
						'component'         => 'groups',
						'must_be_logged_in' => true,
						'block_self'        => false,
						'wrapper_class'     => 'group-button ' . $group->status,
						'wrapper_id'        => 'groupbutton-' . $group->id,
						'link_href'         => wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'join' ), 'groups_join_group' ),
						'link_text'         => __( 'Join Group', 'sportszone' ),
						'link_class'        => 'group-button join-group',
					);
					break;

				case 'private' :

					// Member has outstanding invitation -
					// show an "Accept Invitation" button.
					if ( $group->is_invited ) {
						$button = array(
							'id'                => 'accept_invite',
							'component'         => 'groups',
							'must_be_logged_in' => true,
							'block_self'        => false,
							'wrapper_class'     => 'group-button ' . $group->status,
							'wrapper_id'        => 'groupbutton-' . $group->id,
							'link_href'         => add_query_arg( 'redirect_to', sz_get_group_permalink( $group ), sz_get_group_accept_invite_link( $group ) ),
							'link_text'         => __( 'Accept Invitation', 'sportszone' ),
							'link_class'        => 'group-button accept-invite',
						);

					// Member has requested membership but request is pending -
					// show a "Request Sent" button.
					} elseif ( $group->is_pending ) {
						$button = array(
							'id'                => 'membership_requested',
							'component'         => 'groups',
							'must_be_logged_in' => true,
							'block_self'        => false,
							'wrapper_class'     => 'group-button pending ' . $group->status,
							'wrapper_id'        => 'groupbutton-' . $group->id,
							'link_href'         => sz_get_group_permalink( $group ),
							'link_text'         => __( 'Request Sent', 'sportszone' ),
							'link_class'        => 'group-button pending membership-requested',
						);

					// Member has not requested membership yet -
					// show a "Request Membership" button.
					} else {
						$button = array(
							'id'                => 'request_membership',
							'component'         => 'groups',
							'must_be_logged_in' => true,
							'block_self'        => false,
							'wrapper_class'     => 'group-button ' . $group->status,
							'wrapper_id'        => 'groupbutton-' . $group->id,
							'link_href'         => wp_nonce_url( trailingslashit( sz_get_group_permalink( $group ) . 'request-membership' ), 'groups_request_membership' ),
							'link_text'         => __( 'Request Membership', 'sportszone' ),
							'link_class'        => 'group-button request-membership',
						);
					}

					break;
			}
		}

		/**
		 * Filters the HTML button for joining a group.
		 *
		 * @since 1.2.6
		 * @since 2.4.0 Added $group parameter to filter args.
		 *
		 * @param string $button HTML button for joining a group.
		 * @param object $group SportsZone group object
		 */
		return sz_get_button( apply_filters( 'sz_get_group_join_button', $button, $group ) );
	}

/**
 * Output the Create a Group button.
 *
 * @since 2.0.0
 */
function sz_group_create_button() {
	echo sz_get_group_create_button();
}
	/**
	 * Get the Create a Group button.
	 *
	 * @since 2.0.0
	 *
	 * @return false|string
	 */
	function sz_get_group_create_button() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! sz_user_can_create_groups() ) {
			return false;
		}

		$button_args = array(
			'id'         => 'create_group',
			'component'  => 'groups',
			'link_text'  => __( 'Create a Group', 'sportszone' ),
			'link_class' => 'group-create no-ajax',
			'link_href'  => trailingslashit( sz_get_groups_directory_permalink() . 'create' ),
			'wrapper'    => false,
			'block_self' => false,
		);

		/**
		 * Filters the HTML button for creating a group.
		 *
		 * @since 2.0.0
		 *
		 * @param string $button HTML button for creating a group.
		 */
		return sz_get_button( apply_filters( 'sz_get_group_create_button', $button_args ) );
	}

/**
 * Output the Create a Group nav item.
 *
 * @since 2.2.0
 */
function sz_group_create_nav_item() {
	echo sz_get_group_create_nav_item();
}

	/**
	 * Get the Create a Group nav item.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	function sz_get_group_create_nav_item() {
		// Get the create a group button.
		$create_group_button = sz_get_group_create_button();

		// Make sure the button is available.
		if ( empty( $create_group_button ) ) {
			return;
		}

		$output = '<li id="group-create-nav">' . $create_group_button . '</li>';

		/**
		 * Filters the Create a Group nav item.
		 *
		 * @since 2.2.0
		 *
		 * @param string $output HTML output for nav item.
		 */
		return apply_filters( 'sz_get_group_create_nav_item', $output );
	}

/**
 * Checks if a specific theme is still filtering the Groups directory title
 * if so, transform the title button into a Groups directory nav item.
 *
 * @since 2.2.0
 *
 * @return string|null HTML Output
 */
function sz_group_backcompat_create_nav_item() {
	// Bail if the Groups nav item is already used by sz-legacy.
	if ( has_action( 'sz_groups_directory_group_filter', 'sz_legacy_theme_group_create_nav', 999 ) ) {
		return;
	}

	// Bail if the theme is not filtering the Groups directory title.
	if ( ! has_filter( 'sz_groups_directory_header' ) ) {
		return;
	}

	sz_group_create_nav_item();
}
add_action( 'sz_groups_directory_group_filter', 'sz_group_backcompat_create_nav_item', 1000 );

/**
 * Prints a message if the group is not visible to the current user (it is a
 * hidden or private group, and the user does not have access).
 *
 * @since 1.0.0
 *
 * @global SZ_Groups_Template $groups_template Groups template object.
 *
 * @param object|null $group Group to get status message for. Optional; defaults to current group.
 */
function sz_group_status_message( $group = null ) {
	global $groups_template;

	// Group not passed so look for loop.
	if ( empty( $group ) ) {
		$group =& $groups_template->group;
	}

	// Group status is not set (maybe outside of group loop?).
	if ( empty( $group->status ) ) {
		$message = __( 'This group is not currently accessible.', 'sportszone' );

	// Group has a status.
	} else {
		switch( $group->status ) {

			// Private group.
			case 'private' :
				if ( ! sz_group_has_requested_membership( $group ) ) {
					if ( is_user_logged_in() ) {
						if ( sz_group_is_invited( $group ) ) {
							$message = __( 'You must accept your pending invitation before you can access this private group.', 'sportszone' );
						} else {
							$message = __( 'This is a private group and you must request group membership in order to join.', 'sportszone' );
						}
					} else {
						$message = __( 'This is a private group. To join you must be a registered site member and request group membership.', 'sportszone' );
					}
				} else {
					$message = __( 'This is a private group. Your membership request is awaiting approval from the group administrator.', 'sportszone' );
				}

				break;

			// Hidden group.
			case 'hidden' :
			default :
				$message = __( 'This is a hidden group and only invited members can join.', 'sportszone' );
				break;
		}
	}

	/**
	 * Filters a message if the group is not visible to the current user.
	 *
	 * This will be true if it is a hidden or private group, and the user does not have access.
	 *
	 * @since 1.6.0
	 *
	 * @param string $message Message to display to the current user.
	 * @param object $group   Group to get status message for.
	 */
	echo apply_filters( 'sz_group_status_message', $message, $group );
}

/**
 * Output hidden form fields for group.
 *
 * This function is no longer used, but may still be used by older themes.
 *
 * @since 1.0.0
 */
function sz_group_hidden_fields() {
	$query_arg = sz_core_get_component_search_query_arg( 'groups' );

	if ( isset( $_REQUEST[ $query_arg ] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . esc_attr( $_REQUEST[ $query_arg ] ) . '" name="search_terms" />';
	}

	if ( isset( $_REQUEST['letter'] ) ) {
		echo '<input type="hidden" id="selected_letter" value="' . esc_attr( $_REQUEST['letter'] ) . '" name="selected_letter" />';
	}

	if ( isset( $_REQUEST['groups_search'] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . esc_attr( $_REQUEST['groups_search'] ) . '" name="search_terms" />';
	}
}

/**
 * Output the total number of groups.
 *
 * @since 1.0.0
 */
function sz_total_group_count() {
	echo sz_get_total_group_count();
}
	/**
	 * Return the total number of groups.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	function sz_get_total_group_count() {

		/**
		 * Filters the total number of groups.
		 *
		 * @since 1.0.0
		 *
		 * @param int $value Total number of groups found.
		 */
		return apply_filters( 'sz_get_total_group_count', groups_get_total_group_count() );
	}

/**
 * Output the total number of groups a user belongs to.
 *
 * @since 1.0.0
 *
 * @param int $user_id User ID to get group membership count.
 */
function sz_total_group_count_for_user( $user_id = 0 ) {
	echo sz_get_total_group_count_for_user( $user_id );
}
	/**
	 * Return the total number of groups a user belongs to.
	 *
	 * Filtered by `sz_core_number_format()` by default
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id User ID to get group membership count.
	 * @return string
	 */
	function sz_get_total_group_count_for_user( $user_id = 0 ) {
		$count = groups_total_groups_for_user( $user_id );

		/**
		 * Filters the total number of groups a user belongs to.
		 *
		 * @since 1.2.0
		 *
		 * @param int $count   Total number of groups for the user.
		 * @param int $user_id ID of the user being checked.
		 */
		return apply_filters( 'sz_get_total_group_count_for_user', $count, $user_id );
	}

/* Group Members *************************************************************/

/**
 * Initialize a group member query loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *     An array of optional arguments.
 *     @type int      $group_id           ID of the group whose members are being queried.
 *                                        Default: current group ID.
 *     @type int      $page               Page of results to be queried. Default: 1.
 *     @type int      $per_page           Number of items to return per page of results.
 *                                        Default: 20.
 *     @type int      $max                Optional. Max number of items to return.
 *     @type array    $exclude            Optional. Array of user IDs to exclude.
 *     @type bool|int $exclude_admin_mods True (or 1) to exclude admins and mods from results.
 *                                        Default: 1.
 *     @type bool|int $exclude_banned     True (or 1) to exclude banned users from results.
 *                                        Default: 1.
 *     @type array    $group_role         Optional. Array of group roles to include.
 *     @type string   $type               Optional. Sort order of results. 'last_joined',
 *                                        'first_joined', or any of the $type params available in
 *                                        {@link SZ_User_Query}. Default: 'last_joined'.
 *     @type string   $search_terms       Optional. Search terms to match. Pass an
 *                                        empty string to force-disable search, even in
 *                                        the presence of $_REQUEST['s']. Default: false.
 * }
 *
 * @return bool
 */
function sz_group_has_members( $args = '' ) {
	global $members_template;

	$exclude_admins_mods = 1;

	if ( sz_is_group_members() ) {
		$exclude_admins_mods = 0;
	}

	/*
	 * Use false as the search_terms default so that SZ_User_Query
	 * doesn't add a search clause.
	 */
	$search_terms_default = false;
	$search_query_arg = sz_core_get_component_search_query_arg( 'members' );
	if ( ! empty( $_REQUEST[ $search_query_arg ] ) ) {
		$search_terms_default = stripslashes( $_REQUEST[ $search_query_arg ] );
	}

	$r = sz_parse_args( $args, array(
		'group_id'            => sz_get_current_group_id(),
		'page'                => 1,
		'per_page'            => 20,
		'max'                 => false,
		'exclude'             => false,
		'exclude_admins_mods' => $exclude_admins_mods,
		'exclude_banned'      => 1,
		'group_role'          => false,
		'search_terms'        => $search_terms_default,
		'type'                => 'last_joined',
	), 'group_has_members' );

	/*
	 * If an empty search_terms string has been passed,
	 * the developer is force-disabling search.
	 */
	if ( '' === $r['search_terms'] ) {
		// Set the search_terms to false for SZ_User_Query efficiency.
		$r['search_terms'] = false;
	} elseif ( ! empty( $_REQUEST['s'] ) ) {
		$r['search_terms'] = $_REQUEST['s'];
	}

	$members_template = new SZ_Groups_Group_Members_Template( $r );

	/**
	 * Filters whether or not a group member query has members to display.
	 *
	 * @since 1.1.0
	 *
	 * @param bool                             $value            Whether there are members to display.
	 * @param SZ_Groups_Group_Members_Template $members_template Object holding the member query results.
	 */
	return apply_filters( 'sz_group_has_members', $members_template->has_members(), $members_template );
}

/**
 * @since 1.0.0
 *
 * @return mixed
 */
function sz_group_members() {
	global $members_template;

	return $members_template->members();
}

/**
 * @since 1.0.0
 *
 * @return mixed
 */
function sz_group_the_member() {
	global $members_template;

	return $members_template->the_member();
}

/**
 * Output the group member avatar while in the groups members loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {@see sz_core_fetch_avatar()}.
 */
function sz_group_member_avatar( $args = '' ) {
	echo sz_get_group_member_avatar( $args );
}
	/**
	 * Return the group member avatar while in the groups members loop.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $args {@see sz_core_fetch_avatar()}.
	 * @return string
	 */
	function sz_get_group_member_avatar( $args = '' ) {
		global $members_template;

		$r = sz_parse_args( $args, array(
			'item_id' => $members_template->member->user_id,
			'type'    => 'full',
			'email'   => $members_template->member->user_email,
			'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), $members_template->member->display_name )
		) );

		/**
		 * Filters the group member avatar while in the groups members loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value HTML markup for group member avatar.
		 * @param array  $r     Parsed args used for the avatar query.
		 */
		return apply_filters( 'sz_get_group_member_avatar', sz_core_fetch_avatar( $r ), $r );
	}

/**
 * Output the group member avatar while in the groups members loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {@see sz_core_fetch_avatar()}.
 */
function sz_group_member_avatar_thumb( $args = '' ) {
	echo sz_get_group_member_avatar_thumb( $args );
}
	/**
	 * Return the group member avatar while in the groups members loop.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $args {@see sz_core_fetch_avatar()}.
	 * @return string
	 */
	function sz_get_group_member_avatar_thumb( $args = '' ) {
		global $members_template;

		$r = sz_parse_args( $args, array(
			'item_id' => $members_template->member->user_id,
			'type'    => 'thumb',
			'email'   => $members_template->member->user_email,
			'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), $members_template->member->display_name )
		) );

		/**
		 * Filters the group member avatar thumb while in the groups members loop.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value HTML markup for group member avatar thumb.
		 * @param array  $r     Parsed args used for the avatar query.
		 */
		return apply_filters( 'sz_get_group_member_avatar_thumb', sz_core_fetch_avatar( $r ), $r );
	}

/**
 * Output the group member avatar while in the groups members loop.
 *
 * @since 1.0.0
 *
 * @param int $width  Width of avatar to fetch.
 * @param int $height Height of avatar to fetch.
 */
function sz_group_member_avatar_mini( $width = 30, $height = 30 ) {
	echo sz_get_group_member_avatar_mini( $width, $height );
}
	/**
	 * Output the group member avatar while in the groups members loop.
	 *
	 * @since 1.0.0
	 *
	 * @param int $width  Width of avatar to fetch.
	 * @param int $height Height of avatar to fetch.
	 * @return string
	 */
	function sz_get_group_member_avatar_mini( $width = 30, $height = 30 ) {
		global $members_template;

		$r = sz_parse_args( array(), array(
			'item_id' => $members_template->member->user_id,
			'type'    => 'thumb',
			'email'   => $members_template->member->user_email,
			'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), $members_template->member->display_name ),
			'width'   => absint( $width ),
			'height'  => absint( $height )
		) );

		/**
		 * Filters the group member avatar mini while in the groups members loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value HTML markup for group member avatar mini.
		 * @param array  $r     Parsed args used for the avatar query.
		 */
		return apply_filters( 'sz_get_group_member_avatar_mini', sz_core_fetch_avatar( $r ), $r );
	}

/**
 * @since 1.0.0
 */
function sz_group_member_name() {
	echo sz_get_group_member_name();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_name() {
		global $members_template;

		/**
		 * Filters the group member display name of the current user in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $display_name Display name of the current user.
		 */
		return apply_filters( 'sz_get_group_member_name', $members_template->member->display_name );
	}

/**
 * @since 1.0.0
 */
function sz_group_member_url() {
	echo sz_get_group_member_url();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_url() {
		global $members_template;

		/**
		 * Filters the group member url for the current user in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value URL for the current user.
		 */
		return apply_filters( 'sz_get_group_member_url', sz_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) );
	}

/**
 * @since 1.0.0
 */
function sz_group_member_link() {
	echo sz_get_group_member_link();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_link() {
		global $members_template;

		/**
		 * Filters the group member HTML link for the current user in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value HTML link for the current user.
		 */
		return apply_filters( 'sz_get_group_member_link', '<a href="' . sz_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) . '">' . $members_template->member->display_name . '</a>' );
	}

/**
 * @since 1.2.0
 */
function sz_group_member_domain() {
	echo sz_get_group_member_domain();
}

	/**
	 * @since 1.2.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_domain() {
		global $members_template;

		/**
		 * Filters the group member domain for the current user in the loop.
		 *
		 * @since 1.2.0
		 *
		 * @param string $value Domain for the current user.
		 */
		return apply_filters( 'sz_get_group_member_domain', sz_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) );
	}

/**
 * @since 1.2.0
 */
function sz_group_member_is_friend() {
	echo sz_get_group_member_is_friend();
}

	/**
	 * @since 1.2.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_is_friend() {
		global $members_template;

		if ( !isset( $members_template->member->is_friend ) ) {
			$friend_status = 'not_friends';
		} else {
			$friend_status = ( 0 == $members_template->member->is_friend )
				? 'pending'
				: 'is_friend';
		}

		/**
		 * Filters the friendship status between current user and displayed user in group member loop.
		 *
		 * @since 1.2.0
		 *
		 * @param string $friend_status Current status of the friendship.
		 */
		return apply_filters( 'sz_get_group_member_is_friend', $friend_status );
	}

/**
 * @since 1.0.0
 */
function sz_group_member_is_banned() {
	echo sz_get_group_member_is_banned();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_is_banned() {
		global $members_template;

		/**
		 * Filters whether the member is banned from the current group.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $is_banned Whether or not the member is banned.
		 */
		return apply_filters( 'sz_get_group_member_is_banned', $members_template->member->is_banned );
	}

/**
 * @since 1.2.6
 */
function sz_group_member_css_class() {
	global $members_template;

	if ( $members_template->member->is_banned ) {

		/**
		 * Filters the class to add to the HTML if member is banned.
		 *
		 * @since 1.2.6
		 *
		 * @param string $value HTML class to add.
		 */
		echo apply_filters( 'sz_group_member_css_class', 'banned-user' );
	}
}

/**
 * Output the joined date for the current member in the group member loop.
 *
 * @since 1.0.0
 * @since 2.7.0 Added $args as a parameter.
 *
 * @param array|string $args {@see sz_get_group_member_joined_since()}
 * @return string|null
 */
function sz_group_member_joined_since( $args = array() ) {
	echo sz_get_group_member_joined_since( $args );
}
	/**
	 * Return the joined date for the current member in the group member loop.
	 *
	 * @since 1.0.0
	 * @since 2.7.0 Added $args as a parameter.
	 *
	 * @param array|string $args {
	 *     Array of optional parameters.
	 *
	 *     @type bool $relative Optional. If true, returns relative joined date. eg. joined 5 months ago.
	 *                          If false, returns joined date value from database. Default: true.
	 * }
	 * @return string
	 */
	function sz_get_group_member_joined_since( $args = array() ) {
		global $members_template;

		$r = sz_parse_args( $args, array(
			'relative' => true,
		), 'group_member_joined_since' );

		// We do not want relative time, so return now.
		// @todo Should the 'sz_get_group_member_joined_since' filter be applied here?
		if ( ! $r['relative'] ) {
			return esc_attr( $members_template->member->date_modified );
		}

		/**
		 * Filters the joined since time for the current member in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Joined since time.
		 */
		return apply_filters( 'sz_get_group_member_joined_since', sz_core_get_last_activity( $members_template->member->date_modified, __( 'joined %s', 'sportszone') ) );
	}

/**
 * @since 1.0.0
 */
function sz_group_member_id() {
	echo sz_get_group_member_id();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_id() {
		global $members_template;

		/**
		 * Filters the member's user ID for group members loop.
		 *
		 * @since 1.0.0
		 *
		 * @param int $user_id User ID of the member.
		 */
		return apply_filters( 'sz_get_group_member_id', $members_template->member->user_id );
	}

/**
 * @since 1.0.0
 *
 * @return bool
 */
function sz_group_member_needs_pagination() {
	global $members_template;

	if ( $members_template->total_member_count > $members_template->pag_num ) {
		return true;
	}

	return false;
}

/**
 * @since 1.0.0
 */
function sz_group_pag_id() {
	echo sz_get_group_pag_id();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_pag_id() {

		/**
		 * Filters the string to be used as the group pag id.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Value to use for the pag id.
		 */
		return apply_filters( 'sz_get_group_pag_id', 'pag' );
	}

/**
 * @since 1.0.0
 */
function sz_group_member_pagination() {
	echo sz_get_group_member_pagination();
	wp_nonce_field( 'sz_groups_member_list', '_member_pag_nonce' );
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_pagination() {
		global $members_template;

		/**
		 * Filters the HTML markup to be used for group member listing pagination.
		 *
		 * @since 1.0.0
		 *
		 * @param string $pag_links HTML markup for the pagination.
		 */
		return apply_filters( 'sz_get_group_member_pagination', $members_template->pag_links );
	}

/**
 * @since 1.0.0
 */
function sz_group_member_pagination_count() {
	echo sz_get_group_member_pagination_count();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_member_pagination_count() {
		global $members_template;

		$start_num = intval( ( $members_template->pag_page - 1 ) * $members_template->pag_num ) + 1;
		$from_num  = sz_core_number_format( $start_num );
		$to_num    = sz_core_number_format( ( $start_num + ( $members_template->pag_num - 1 ) > $members_template->total_member_count ) ? $members_template->total_member_count : $start_num + ( $members_template->pag_num - 1 ) );
		$total     = sz_core_number_format( $members_template->total_member_count );

		if ( 1 == $members_template->total_member_count ) {
			$message = __( 'Viewing 1 member', 'sportszone' );
		} else {
			$message = sprintf( _n( 'Viewing %1$s - %2$s of %3$s member', 'Viewing %1$s - %2$s of %3$s members', $members_template->total_member_count, 'sportszone' ), $from_num, $to_num, $total );
		}

		/**
		 * Filters the "Viewing x-y of z members" pagination message.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value    "Viewing x-y of z members" text.
		 * @param string $from_num Total amount for the low value in the range.
		 * @param string $to_num   Total amount for the high value in the range.
		 * @param string $total    Total amount of members found.
		 */
		return apply_filters( 'sz_get_group_member_pagination_count', $message, $from_num, $to_num, $total );
	}

/**
 * @since 1.0.0
 */
function sz_group_member_admin_pagination() {
	echo sz_get_group_member_admin_pagination();
	wp_nonce_field( 'sz_groups_member_admin_list', '_member_admin_pag_nonce' );
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	function sz_get_group_member_admin_pagination() {
		global $members_template;

		return $members_template->pag_links;
	}

/**
 * Output the contents of the current group's home page.
 *
 * You should only use this when on a single group page.
 *
 * @since 2.4.0
 */
function sz_groups_front_template_part() {
	$located = sz_groups_get_front_template();

	if ( false !== $located ) {
		$slug = str_replace( '.php', '', $located );

		/**
		 * Let plugins adding an action to sz_get_template_part get it from here
		 *
		 * @param string $slug Template part slug requested.
		 * @param string $name Template part name requested.
		 */
		do_action( 'get_template_part_' . $slug, $slug, false );

		load_template( $located, true );

	} else if ( sz_is_active( 'activity' ) ) {
		sz_get_template_part( 'groups/single/activity' );

	} else if ( sz_is_active( 'members'  ) ) {
		sz_groups_members_template_part();
	}

	return $located;
}

/**
 * Locate a custom group front template if it exists.
 *
 * @since 2.4.0
 * @since 2.6.0 Adds the Group Type to the front template hierarchy.
 *
 * @param  SZ_Groups_Group|null $group Optional. Falls back to current group if not passed.
 * @return string|bool                 Path to front template on success; boolean false on failure.
 */
function sz_groups_get_front_template( $group = null ) {
	if ( ! is_a( $group, 'SZ_Groups_Group' ) ) {
		$group = groups_get_current_group();
	}

	if ( ! isset( $group->id ) ) {
		return false;
	}

	if ( isset( $group->front_template ) ) {
		return $group->front_template;
	}

	$template_names = array(
		'groups/single/front-id-'     . sanitize_file_name( $group->id )     . '.php',
		'groups/single/front-slug-'   . sanitize_file_name( $group->slug )   . '.php',
	);

	if ( sz_groups_get_group_types() ) {
		$group_type = sz_groups_get_group_type( $group->id );
		if ( ! $group_type ) {
			$group_type = 'none';
		}

		$template_names[] = 'groups/single/front-group-type-' . sanitize_file_name( $group_type )   . '.php';
	}

	$template_names = array_merge( $template_names, array(
		'groups/single/front-status-' . sanitize_file_name( $group->status ) . '.php',
		'groups/single/front.php'
	) );

	/**
	 * Filters the hierarchy of group front templates corresponding to a specific group.
	 *
	 * @since 2.4.0
	 * @since 2.5.0 Added the `$group` parameter.
	 *
	 * @param array  $template_names Array of template paths.
	 * @param object $group          Group object.
	 */
	return sz_locate_template( apply_filters( 'sz_groups_get_front_template', $template_names, $group ), false, true );
}

/**
 * Output the Group members template
 *
 * @since 2.0.0
 */
function sz_groups_members_template_part() {
	?>
	<div class="item-list-tabs" id="subnav" aria-label="<?php esc_attr_e( 'Group secondary navigation', 'sportszone' ); ?>" role="navigation">
		<ul>
			<li class="groups-members-search" role="search">
				<?php sz_directory_members_search_form(); ?>
			</li>

			<?php sz_groups_members_filter(); ?>
			<?php

			/**
			 * Fires at the end of the group members search unordered list.
			 *
			 * Part of sz_groups_members_template_part().
			 *
			 * @since 1.5.0
			 */
			do_action( 'sz_members_directory_member_sub_types' ); ?>

		</ul>
	</div>

	<h2 class="sz-screen-reader-text"><?php
		/* translators: accessibility text */
		_e( 'Members', 'sportszone' );
	?></h2>

	<div id="members-group-list" class="group_members dir-list">

		<?php sz_get_template_part( 'groups/single/members' ); ?>

	</div>
	<?php
}

/**
 * Output the Group members filters
 *
 * @since 2.0.0
 */
function sz_groups_members_filter() {
	?>
	<li id="group_members-order-select" class="last filter">
		<label for="group_members-order-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
		<select id="group_members-order-by">
			<option value="last_joined"><?php _e( 'Newest', 'sportszone' ); ?></option>
			<option value="first_joined"><?php _e( 'Oldest', 'sportszone' ); ?></option>

			<?php if ( sz_is_active( 'activity' ) ) : ?>
				<option value="group_activity"><?php _e( 'Group Activity', 'sportszone' ); ?></option>
			<?php endif; ?>

			<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

			<?php

			/**
			 * Fires at the end of the Group members filters select input.
			 *
			 * Useful for plugins to add more filter options.
			 *
			 * @since 2.0.0
			 */
			do_action( 'sz_groups_members_order_options' ); ?>

		</select>
	</li>
	<?php
}

/*
 * Group Creation Process Template Tags
 */

/**
 * Determine if the current logged in user can create groups.
 *
 * @since 1.5.0
 *
 * @return bool True if user can create groups. False otherwise.
 */
function sz_user_can_create_groups() {

	// Super admin can always create groups.
	if ( sz_current_user_can( 'sz_moderate' ) ) {
		return true;
	}

	// Get group creation option, default to 0 (allowed).
	$restricted = (int) sz_get_option( 'sz_restrict_group_creation', 0 );

	// Allow by default.
	$can_create = true;

	// Are regular users restricted?
	if ( $restricted ) {
		$can_create = false;
	}

	/**
	 * Filters if the current logged in user can create groups.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $can_create Whether the person can create groups.
	 * @param int  $restricted Whether or not group creation is restricted.
	 */
	return apply_filters( 'sz_user_can_create_groups', $can_create, $restricted );
}

/**
 * @since 1.0.0
 *
 * @return bool
 */
function sz_group_creation_tabs() {
	$sz = sportszone();

	if ( !is_array( $sz->groups->group_creation_steps ) ) {
		return false;
	}

	if ( !sz_get_groups_current_create_step() ) {
		$keys = array_keys( $sz->groups->group_creation_steps );
		$sz->groups->current_create_step = array_shift( $keys );
	}

	$counter = 1;

	foreach ( (array) $sz->groups->group_creation_steps as $slug => $step ) {
		$is_enabled = sz_are_previous_group_creation_steps_complete( $slug ); ?>

		<li<?php if ( sz_get_groups_current_create_step() == $slug ) : ?> class="current"<?php endif; ?>><?php if ( $is_enabled ) : ?><a href="<?php sz_groups_directory_permalink(); ?>create/step/<?php echo $slug ?>/"><?php else: ?><span><?php endif; ?><?php echo $counter ?>. <?php echo $step['name'] ?><?php if ( $is_enabled ) : ?></a><?php else: ?></span><?php endif ?></li><?php
		$counter++;
	}

	unset( $is_enabled );

	/**
	 * Fires at the end of the creation of the group tabs.
	 *
	 * @since 1.0.0
	 */
	do_action( 'groups_creation_tabs' );
}

/**
 * @since 1.0.0
 */
function sz_group_creation_stage_title() {
	$sz = sportszone();

	/**
	 * Filters the group creation stage title.
	 *
	 * @since 1.1.0
	 *
	 * @param string $value HTML markup for the group creation stage title.
	 */
	echo apply_filters( 'sz_group_creation_stage_title', '<span>&mdash; ' . $sz->groups->group_creation_steps[sz_get_groups_current_create_step()]['name'] . '</span>' );
}

/**
 * @since 1.1.0
 */
function sz_group_creation_form_action() {
	echo sz_get_group_creation_form_action();
}

/**
 * @since 1.1.0
 *
 * @return mixed|void
 */
	function sz_get_group_creation_form_action() {
		$sz = sportszone();

		if ( !sz_action_variable( 1 ) ) {
			$keys = array_keys( $sz->groups->group_creation_steps );
			$sz->action_variables[1] = array_shift( $keys );
		}

		/**
		 * Filters the group creation form action.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Action to be used with group creation form.
		 */
		return apply_filters( 'sz_get_group_creation_form_action', trailingslashit( sz_get_groups_directory_permalink() . 'create/step/' . sz_action_variable( 1 ) ) );
	}

/**
 * @since 1.1.0
 *
 * @param string $step_slug
 *
 * @return bool
 */
function sz_is_group_creation_step( $step_slug ) {

	// Make sure we are in the groups component.
	if ( ! sz_is_groups_component() || ! sz_is_current_action( 'create' ) ) {
		return false;
	}

	$sz = sportszone();

	// If this the first step, we can just accept and return true.
	$keys = array_keys( $sz->groups->group_creation_steps );
	if ( !sz_action_variable( 1 ) && array_shift( $keys ) == $step_slug ) {
		return true;
	}

	// Before allowing a user to see a group creation step we must make sure
	// previous steps are completed.
	if ( !sz_is_first_group_creation_step() ) {
		if ( !sz_are_previous_group_creation_steps_complete( $step_slug ) ) {
			return false;
		}
	}

	// Check the current step against the step parameter.
	if ( sz_is_action_variable( $step_slug ) ) {
		return true;
	}

	return false;
}

/**
 * @since 1.1.0
 *
 * @param array $step_slugs
 *
 * @return bool
 */
function sz_is_group_creation_step_complete( $step_slugs ) {
	$sz = sportszone();

	if ( !isset( $sz->groups->completed_create_steps ) ) {
		return false;
	}

	if ( is_array( $step_slugs ) ) {
		$found = true;

		foreach ( (array) $step_slugs as $step_slug ) {
			if ( !in_array( $step_slug, $sz->groups->completed_create_steps ) ) {
				$found = false;
			}
		}

		return $found;
	} else {
		return in_array( $step_slugs, $sz->groups->completed_create_steps );
	}

	return true;
}

/**
 * @since 1.1.0
 *
 * @param string $step_slug
 *
 * @return bool
 */
function sz_are_previous_group_creation_steps_complete( $step_slug ) {
	$sz = sportszone();

	// If this is the first group creation step, return true.
	$keys = array_keys( $sz->groups->group_creation_steps );
	if ( array_shift( $keys ) == $step_slug ) {
		return true;
	}

	reset( $sz->groups->group_creation_steps );

	$previous_steps = array();

	// Get previous steps.
	foreach ( (array) $sz->groups->group_creation_steps as $slug => $name ) {
		if ( $slug === $step_slug ) {
			break;
		}

		$previous_steps[] = $slug;
	}

	return sz_is_group_creation_step_complete( $previous_steps );
}

/**
 * @since 1.1.0
 */
function sz_new_group_id() {
	echo sz_get_new_group_id();
}

	/**
	 * @since 1.1.0
	 *
	 * @return int
	 */
	function sz_get_new_group_id() {
		$sz           = sportszone();
		$new_group_id = isset( $sz->groups->new_group_id )
			? $sz->groups->new_group_id
			: 0;

		/**
		 * Filters the new group ID.
		 *
		 * @since 1.1.0
		 *
		 * @param int $new_group_id ID of the new group.
		 */
		return (int) apply_filters( 'sz_get_new_group_id', $new_group_id );
	}

/**
 * @since 1.1.0
 */
function sz_new_group_name() {
	echo sz_get_new_group_name();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_new_group_name() {
		$sz   = sportszone();
		$name = isset( $sz->groups->current_group->name )
			? $sz->groups->current_group->name
			: '';

		/**
		 * Filters the new group name.
		 *
		 * @since 1.1.0
		 *
		 * @param string $name Name of the new group.
		 */
		return apply_filters( 'sz_get_new_group_name', $name );
	}

/**
 * @since 1.1.0
 */
function sz_new_group_description() {
	echo sz_get_new_group_description();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_new_group_description() {
		$sz          = sportszone();
		$description = isset( $sz->groups->current_group->description )
			? $sz->groups->current_group->description
			: '';

		/**
		 * Filters the new group description.
		 *
		 * @since 1.1.0
		 *
		 * @param string $name Description of the new group.
		 */
		return apply_filters( 'sz_get_new_group_description', $description );
	}

/**
 * @since 1.1.0
 */
function sz_new_group_enable_forum() {
	echo sz_get_new_group_enable_forum();
}

	/**
	 * @since 1.1.0
	 *
	 * @return int
	 */
	function sz_get_new_group_enable_forum() {
		$sz    = sportszone();
		$forum = isset( $sz->groups->current_group->enable_forum )
			? $sz->groups->current_group->enable_forum
			: false;

		/**
		 * Filters whether or not to enable forums for the new group.
		 *
		 * @since 1.1.0
		 *
		 * @param int $forum Whether or not to enable forums.
		 */
		return (int) apply_filters( 'sz_get_new_group_enable_forum', $forum );
	}

/**
 * @since 1.1.0
 */
function sz_new_group_status() {
	echo sz_get_new_group_status();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_new_group_status() {
		$sz     = sportszone();
		$status = isset( $sz->groups->current_group->status )
			? $sz->groups->current_group->status
			: 'public';

		/**
		 * Filters the new group status.
		 *
		 * @since 1.1.0
		 *
		 * @param string $status Status for the new group.
		 */
		return apply_filters( 'sz_get_new_group_status', $status );
	}

/**
 * Output the avatar for the group currently being created
 *
 * @since 1.1.0
 *
 * @see sz_core_fetch_avatar() For more information on accepted arguments
 *
 * @param array|string $args See sz_core_fetch_avatar().
 */
function sz_new_group_avatar( $args = '' ) {
	echo sz_get_new_group_avatar( $args );
}
	/**
	 * Return the avatar for the group currently being created
	 *
	 * @since 1.1.0
	 *
	 * @see sz_core_fetch_avatar() For a description of arguments and return values.
	 *
	 * @param array|string $args {
	 *     Arguments are listed here with an explanation of their defaults.
	 *     For more information about the arguments, see {@link sz_core_fetch_avatar()}.
	 *
	 *     @type string   $alt     Default: 'Group photo'.
	 *     @type string   $class   Default: 'avatar'.
	 *     @type string   $type    Default: 'full'.
	 *     @type int|bool $width   Default: false.
	 *     @type int|bool $height  Default: false.
	 *     @type string   $id      Passed to $css_id parameter. Default: 'avatar-crop-preview'.
	 * }
	 * @return string       The avatar for the group being created
	 */
	function sz_get_new_group_avatar( $args = '' ) {

		// Parse arguments.
		$r = sz_parse_args( $args, array(
			'type'    => 'full',
			'width'   => false,
			'height'  => false,
			'class'   => 'avatar',
			'id'      => 'avatar-crop-preview',
			'alt'     => __( 'Group photo', 'sportszone' ),
		), 'get_new_group_avatar' );

		// Merge parsed arguments with object specific data.
		$r = array_merge( $r, array(
			'item_id'    => sz_get_current_group_id(),
			'object'     => 'group',
			'avatar_dir' => 'group-avatars',
		) );

		// Get the avatar.
		$avatar = sz_core_fetch_avatar( $r );

		/**
		 * Filters the new group avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $avatar HTML markup for the new group avatar.
		 * @param array  $r      Array of parsed arguments for the group avatar.
		 * @param array  $args   Array of original arguments passed to the function.
		 */
		return apply_filters( 'sz_get_new_group_avatar', $avatar, $r, $args );
	}

/**
 * Escape & output the URL to the previous group creation step
 *
 * @since 1.1.0
 */
function sz_group_creation_previous_link() {
	echo esc_url( sz_get_group_creation_previous_link() );
}
	/**
	 * Return the URL to the previous group creation step
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	function sz_get_group_creation_previous_link() {
		$sz    = sportszone();
		$steps = array_keys( $sz->groups->group_creation_steps );

		// Loop through steps.
		foreach ( $steps as $slug ) {

			// Break when the current step is found.
			if ( sz_is_action_variable( $slug ) ) {
				break;
			}

			// Add slug to previous steps.
			$previous_steps[] = $slug;
		}

		// Generate the URL for the previous step.
		$group_directory = sz_get_groups_directory_permalink();
		$create_step     = 'create/step/';
		$previous_step   = array_pop( $previous_steps );
		$url             = trailingslashit( $group_directory . $create_step . $previous_step );

		/**
		 * Filters the permalink for the previous step with the group creation process.
		 *
		 * @since 1.1.0
		 *
		 * @param string $url Permalink for the previous step.
		 */
		return apply_filters( 'sz_get_group_creation_previous_link', $url );
	}

/**
 * Echoes the current group creation step.
 *
 * @since 1.6.0
 */
function sz_groups_current_create_step() {
	echo sz_get_groups_current_create_step();
}
	/**
	 * Returns the current group creation step. If none is found, returns an empty string.
	 *
	 * @since 1.6.0
	 *
	 *
	 * @return string $current_create_step
	 */
	function sz_get_groups_current_create_step() {
		$sz = sportszone();

		if ( !empty( $sz->groups->current_create_step ) ) {
			$current_create_step = $sz->groups->current_create_step;
		} else {
			$current_create_step = '';
		}

		/**
		 * Filters the current group creation step.
		 *
		 * If none is found, returns an empty string.
		 *
		 * @since 1.6.0
		 *
		 * @param string $current_create_step Current step in the group creation process.
		 */
		return apply_filters( 'sz_get_groups_current_create_step', $current_create_step );
	}

/**
 * Is the user looking at the last step in the group creation process.
 *
 * @since 1.1.0
 *
 * @param string $step Step to compare.
 * @return bool True if yes, False if no
 */
function sz_is_last_group_creation_step( $step = '' ) {

	// Use current step, if no step passed.
	if ( empty( $step ) ) {
		$step = sz_get_groups_current_create_step();
	}

	// Get the last step.
	$sz     = sportszone();
	$steps  = array_keys( $sz->groups->group_creation_steps );
	$l_step = array_pop( $steps );

	// Compare last step to step.
	$retval = ( $l_step === $step );

	/**
	 * Filters whether or not user is looking at last step in group creation process.
	 *
	 * @since 2.4.0
	 *
	 * @param bool   $retval Whether or not we are looking at last step.
	 * @param array  $steps  Array of steps from the group creation process.
	 * @param string $step   Step to compare.
	 */
	return (bool) apply_filters( 'sz_is_last_group_creation_step', $retval, $steps, $step );
}

/**
 * Is the user looking at the first step in the group creation process
 *
 * @since 1.1.0
 *
 * @param string $step Step to compare.
 * @return bool True if yes, False if no
 */
function sz_is_first_group_creation_step( $step = '' ) {

	// Use current step, if no step passed.
	if ( empty( $step ) ) {
		$step = sz_get_groups_current_create_step();
	}

	// Get the first step.
	$sz     = sportszone();
	$steps  = array_keys( $sz->groups->group_creation_steps );
	$f_step = array_shift( $steps );

	// Compare first step to step.
	$retval = ( $f_step === $step );

	/**
	 * Filters whether or not user is looking at first step in group creation process.
	 *
	 * @since 2.4.0
	 *
	 * @param bool   $retval Whether or not we are looking at first step.
	 * @param array  $steps  Array of steps from the group creation process.
	 * @param string $step   Step to compare.
	 */
	return (bool) apply_filters( 'sz_is_first_group_creation_step', $retval, $steps, $step );
}

/**
 * Output a list of friends who can be invited to a group
 *
 * @since 1.0.0
 *
 * @param array $args Array of arguments for friends list output.
 */
function sz_new_group_invite_friend_list( $args = array() ) {
	echo sz_get_new_group_invite_friend_list( $args );
}
	/**
	 * Return a list of friends who can be invited to a group
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of arguments for friends list output.
	 * @return false|string HTML list of checkboxes, or false
	 */
	function sz_get_new_group_invite_friend_list( $args = array() ) {

		// Bail if no friends component.
		if ( ! sz_is_active( 'friends' ) ) {
			return false;
		}

		// Parse arguments.
		$r = sz_parse_args( $args, array(
			'user_id'   => sz_loggedin_user_id(),
			'group_id'  => false,
			'before'    => '',
			'separator' => 'li',
			'after'     => '',
		), 'group_invite_friend_list' );

		// No group passed, so look for new or current group ID's.
		if ( empty( $r['group_id'] ) ) {
			$sz            = sportszone();
			$r['group_id'] = ! empty( $sz->groups->new_group_id )
				? $sz->groups->new_group_id
				: $sz->groups->current_group->id;
		}

		// Setup empty items array.
		$items = array();

		// Build list markup parent elements.
		$before = '';
		if ( ! empty( $r['before'] ) ) {
			$before = $r['before'];
		}

		$after = '';
		if ( ! empty( $r['after'] ) ) {
			$after = $r['after'];
		}

		// Get user's friends who are not in this group already.
		$friends = friends_get_friends_invite_list( $r['user_id'], $r['group_id'] );

		if ( ! empty( $friends ) ) {

			// Get already invited users.
			$invites = groups_get_invites_for_group( $r['user_id'], $r['group_id'] );

			for ( $i = 0, $count = count( $friends ); $i < $count; ++$i ) {
				$checked = in_array( (int) $friends[ $i ]['id'], (array) $invites );
				$items[] = '<' . $r['separator'] . '><label for="f-' . esc_attr( $friends[ $i ]['id'] ) . '"><input' . checked( $checked, true, false ) . ' type="checkbox" name="friends[]" id="f-' . esc_attr( $friends[ $i ]['id'] ) . '" value="' . esc_attr( $friends[ $i ]['id'] ) . '" /> ' . esc_html( $friends[ $i ]['full_name'] ) . '</label></' . $r['separator'] . '>';
			}
		}

		/**
		 * Filters the array of friends who can be invited to a group.
		 *
		 * @since 2.4.0
		 *
		 * @param array $items Array of friends.
		 * @param array $r     Parsed arguments from sz_get_new_group_invite_friend_list()
		 * @param array $args  Unparsed arguments from sz_get_new_group_invite_friend_list()
		 */
		$invitable_friends = apply_filters( 'sz_get_new_group_invite_friend_list', $items, $r, $args );

		if ( ! empty( $invitable_friends ) && is_array( $invitable_friends ) ) {
			$retval = $before . implode( "\n", $invitable_friends ) . $after;
		} else {
			$retval = false;
		}

		return $retval;
	}

/**
 * @since 1.0.0
 */
function sz_directory_groups_search_form() {

	$query_arg = sz_core_get_component_search_query_arg( 'groups' );

	if ( ! empty( $_REQUEST[ $query_arg ] ) ) {
		$search_value = stripslashes( $_REQUEST[ $query_arg ] );
	} else {
		$search_value = sz_get_search_default_text( 'groups' );
	}

	$search_form_html = '<form action="" method="get" id="search-groups-form">
		<label for="groups_search"><input type="text" name="' . esc_attr( $query_arg ) . '" id="groups_search" placeholder="'. esc_attr( $search_value ) .'" /></label>
		<input type="submit" id="groups_search_submit" name="groups_search_submit" value="'. __( 'Search', 'sportszone' ) .'" />
	</form>';

	/**
	 * Filters the HTML markup for the groups search form.
	 *
	 * @since 1.9.0
	 *
	 * @param string $search_form_html HTML markup for the search form.
	 */
	echo apply_filters( 'sz_directory_groups_search_form', $search_form_html );

}

/**
 * Displays group header tabs.
 *
 * @since 1.0.0
 *
 * @todo Deprecate?
 */
function sz_groups_header_tabs() {
	$user_groups = sz_displayed_user_domain() . sz_get_groups_slug(); ?>

	<li<?php if ( !sz_action_variable( 0 ) || sz_is_action_variable( 'recently-active', 0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_groups . '/my-groups/recently-active' ); ?>"><?php _e( 'Recently Active', 'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'recently-joined', 0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_groups . '/my-groups/recently-joined' ); ?>"><?php _e( 'Recently Joined',  'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'most-popular',    0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_groups . '/my-groups/most-popular'    ); ?>"><?php _e( 'Most Popular',     'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'admin-of',        0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_groups . '/my-groups/admin-of'        ); ?>"><?php _e( 'Administrator Of', 'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'mod-of',          0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_groups . '/my-groups/mod-of'          ); ?>"><?php _e( 'Moderator Of',     'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'alphabetically'     ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_groups . '/my-groups/alphabetically'  ); ?>"><?php _e( 'Alphabetically',   'sportszone' ); ?></a></li>

<?php
	do_action( 'groups_header_tabs' );
}

/**
 * Displays group filter titles.
 *
 * @since 1.0.0
 *
 * @todo Deprecate?
 */
function sz_groups_filter_title() {
	$current_filter = sz_action_variable( 0 );

	switch ( $current_filter ) {
		case 'recently-active': default:
			_e( 'Recently Active', 'sportszone' );
			break;
		case 'recently-joined':
			_e( 'Recently Joined', 'sportszone' );
			break;
		case 'most-popular':
			_e( 'Most Popular', 'sportszone' );
			break;
		case 'admin-of':
			_e( 'Administrator Of', 'sportszone' );
			break;
		case 'mod-of':
			_e( 'Moderator Of', 'sportszone' );
			break;
		case 'alphabetically':
			_e( 'Alphabetically', 'sportszone' );
		break;
	}
	do_action( 'sz_groups_filter_title' );
}

/**
 * Echo the current group type message.
 *
 * @since 2.7.0
 */
function sz_current_group_directory_type_message() {
	echo sz_get_current_group_directory_type_message();
}
	/**
	 * Generate the current group type message.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	function sz_get_current_group_directory_type_message() {
		$type_object = sz_groups_get_group_type_object( sz_get_current_group_directory_type() );

		$message = sprintf( __( 'Viewing groups of the type: %s', 'sportszone' ), '<strong>' . $type_object->labels['singular_name'] . '</strong>' );

		/**
		 * Filters the current group type message.
		 *
		 * @since 2.7.0
		 *
		 * @param string $message Message to filter.
		 */
		return apply_filters( 'sz_get_current_group_type_message', $message );
	}

/**
 * Is the current page a specific group admin screen?
 *
 * @since 1.1.0
 *
 * @param string $slug Admin screen slug.
 * @return bool
 */
function sz_is_group_admin_screen( $slug = '' ) {
	return (bool) ( sz_is_group_admin_page() && sz_is_action_variable( $slug ) );
}

/**
 * Echoes the current group admin tab slug.
 *
 * @since 1.6.0
 */
function sz_group_current_admin_tab() {
	echo sz_get_group_current_admin_tab();
}
	/**
	 * Returns the current group admin tab slug.
	 *
	 * @since 1.6.0
	 *
	 *
	 * @return string $tab The current tab's slug.
	 */
	function sz_get_group_current_admin_tab() {
		if ( sz_is_groups_component() && sz_is_current_action( 'admin' ) ) {
			$tab = sz_action_variable( 0 );
		} else {
			$tab = '';
		}

		/**
		 * Filters the current group admin tab slug.
		 *
		 * @since 1.6.0
		 *
		 * @param string $tab Current group admin tab slug.
		 */
		return apply_filters( 'sz_get_current_group_admin_tab', $tab );
	}

/** Group Avatar Template Tags ************************************************/

/**
 * Outputs the current group avatar.
 *
 * @since 1.0.0
 *
 * @param string $type Thumb or full.
 */
function sz_group_current_avatar( $type = 'thumb' ) {
	echo sz_get_group_current_avatar( $type );
}
	/**
	 * Returns the current group avatar.
	 *
	 * @since 2.0.0
	 *
	 * @param string $type Thumb or full.
	 * @return string $tab The current tab's slug.
	 */
	function sz_get_group_current_avatar( $type = 'thumb' ) {

		$group_avatar = sz_core_fetch_avatar( array(
			'item_id'    => sz_get_current_group_id(),
			'object'     => 'group',
			'type'       => $type,
			'avatar_dir' => 'group-avatars',
			'alt'        => __( 'Group avatar', 'sportszone' ),
			'class'      => 'avatar'
		) );

		/**
		 * Filters the current group avatar.
		 *
		 * @since 2.0.0
		 *
		 * @param string $group_avatar HTML markup for current group avatar.
		 */
		return apply_filters( 'sz_get_group_current_avatar', $group_avatar );
	}

/**
 * Return whether a group has an avatar.
 *
 * @since 1.1.0
 *
 * @param int|bool $group_id Group ID to check.
 * @return boolean
 */
function sz_get_group_has_avatar( $group_id = false ) {

	if ( false === $group_id ) {
		$group_id = sz_get_current_group_id();
	}

	$avatar_args = array(
		'item_id' => $group_id,
		'object'  => 'group',
		'no_grav' => true,
		'html'    => false,
		'type'    => 'thumb',
	);

	$group_avatar = sz_core_fetch_avatar( $avatar_args );

	if ( sz_core_avatar_default( 'local', $avatar_args ) === $group_avatar ) {
		return false;
	}

	return true;
}

/**
 * @since 1.1.0
 */
function sz_group_avatar_delete_link() {
	echo sz_get_group_avatar_delete_link();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_avatar_delete_link() {
		$sz = sportszone();

		/**
		 * Filters the URL to delete the group avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value URL to delete the group avatar.
		 */
		return apply_filters( 'sz_get_group_avatar_delete_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( $sz->groups->current_group ) . 'admin/group-avatar/delete' ), 'sz_group_avatar_delete' ) );
	}

/**
 * @since 1.0.0
 */
function sz_custom_group_boxes() {
	do_action( 'groups_custom_group_boxes' );
}

/**
 * @since 1.0.0
 */
function sz_custom_group_admin_tabs() {
	do_action( 'groups_custom_group_admin_tabs' );
}

/**
 * @since 1.0.0
 */
function sz_custom_group_fields_editable() {
	do_action( 'groups_custom_group_fields_editable' );
}

/**
 * @since 1.0.0
 */
function sz_custom_group_fields() {
	do_action( 'groups_custom_group_fields' );
}

/* Group Membership Requests *************************************************/

/**
 * Initialize a group membership request template loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *     @type int $group_id ID of the group. Defaults to current group.
 *     @type int $per_page Number of records to return per page. Default: 10.
 *     @type int $page     Page of results to return. Default: 1.
 *     @type int $max      Max number of items to return. Default: false.
 * }
 * @return bool True if there are requests, otherwise false.
 */
function sz_group_has_membership_requests( $args = '' ) {
	global $requests_template;

	$r = sz_parse_args( $args, array(
		'group_id' => sz_get_current_group_id(),
		'per_page' => 10,
		'page'     => 1,
		'max'      => false
	), 'group_has_membership_requests' );

	$requests_template = new SZ_Groups_Membership_Requests_Template( $r );

	/**
	 * Filters whether or not a group membership query has requests to display.
	 *
	 * @since 1.1.0
	 *
	 * @param bool                                   $value             Whether there are requests to display.
	 * @param SZ_Groups_Membership_Requests_Template $requests_template Object holding the requests query results.
	 */
	return apply_filters( 'sz_group_has_membership_requests', $requests_template->has_requests(), $requests_template );
}

/**
 * @since 1.0.0
 *
 * @return mixed
 */
function sz_group_membership_requests() {
	global $requests_template;

	return $requests_template->requests();
}

/**
 * @since 1.0.0
 *
 * @return mixed
 */
function sz_group_the_membership_request() {
	global $requests_template;

	return $requests_template->the_request();
}

/**
 * @since 1.0.0
 */
function sz_group_request_user_avatar_thumb() {
	global $requests_template;

	/**
	 * Filters the requesting user's avatar thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value HTML markup for the user's avatar thumbnail.
	 */
	echo apply_filters( 'sz_group_request_user_avatar_thumb', sz_core_fetch_avatar( array( 'item_id' => $requests_template->request->user_id, 'type' => 'thumb', 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $requests_template->request->user_id ) ) ) ) );
}

/**
 * @since 1.0.0
 */
function sz_group_request_reject_link() {
	echo sz_get_group_request_reject_link();
}

	/**
	 * @since 1.2.6
	 *
	 * @return mixed|void
	 */
	function sz_get_group_request_reject_link() {
		global $requests_template;

		/**
		 * Filters the URL to use to reject a membership request.
		 *
		 * @since 1.2.6
		 *
		 * @param string $value URL to use to reject a membership request.
		 */
		return apply_filters( 'sz_get_group_request_reject_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( groups_get_current_group() ) . 'admin/membership-requests/reject/' . $requests_template->request->membership_id ), 'groups_reject_membership_request' ) );
	}

/**
 * @since 1.0.0
 */
function sz_group_request_accept_link() {
	echo sz_get_group_request_accept_link();
}

	/**
	 * @since 1.2.6
	 * @return mixed|void
	 */
	function sz_get_group_request_accept_link() {
		global $requests_template;

		/**
		 * Filters the URL to use to accept a membership request.
		 *
		 * @since 1.2.6
		 *
		 * @param string $value URL to use to accept a membership request.
		 */
		return apply_filters( 'sz_get_group_request_accept_link', wp_nonce_url( trailingslashit( sz_get_group_permalink( groups_get_current_group() ) . 'admin/membership-requests/accept/' . $requests_template->request->membership_id ), 'groups_accept_membership_request' ) );
	}

/**
 * @since 1.0.0
 */
function sz_group_request_user_link() {
	echo sz_get_group_request_user_link();
}

	/**
	 * @since 1.2.6
	 *
	 * @return mixed|void
	 */
	function sz_get_group_request_user_link() {
		global $requests_template;

		/**
		 * Filters the URL for the user requesting membership.
		 *
		 * @since 1.2.6
		 *
		 * @param string $value URL for the user requestion membership.
		 */
		return apply_filters( 'sz_get_group_request_user_link', sz_core_get_userlink( $requests_template->request->user_id ) );
	}

/**
 * @since 1.0.0
 */
function sz_group_request_time_since_requested() {
	global $requests_template;

	/**
	 * Filters the formatted time since membership was requested.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Formatted time since membership was requested.
	 */
	echo apply_filters( 'sz_group_request_time_since_requested', sprintf( __( 'requested %s', 'sportszone' ), sz_core_time_since( $requests_template->request->date_modified ) ) );
}

/**
 * @since 1.0.0
 */
function sz_group_request_comment() {
	global $requests_template;

	/**
	 * Filters the membership request comment left by user.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Membership request comment left by user.
	 */
	echo apply_filters( 'sz_group_request_comment', strip_tags( stripslashes( $requests_template->request->comments ) ) );
}

/**
 * Output pagination links for group membership requests.
 *
 * @since 2.0.0
 */
function sz_group_requests_pagination_links() {
	echo sz_get_group_requests_pagination_links();
}
	/**
	 * Get pagination links for group membership requests.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function sz_get_group_requests_pagination_links() {
		global $requests_template;

		/**
		 * Filters pagination links for group membership requests.
		 *
		 * @since 2.0.0
		 *
		 * @param string $value Pagination links for group membership requests.
		 */
		return apply_filters( 'sz_get_group_requests_pagination_links', $requests_template->pag_links );
	}

/**
 * Output pagination count text for group membership requests.
 *
 * @since 2.0.0
 */
function sz_group_requests_pagination_count() {
	echo sz_get_group_requests_pagination_count();
}
	/**
	 * Get pagination count text for group membership requests.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function sz_get_group_requests_pagination_count() {
		global $requests_template;

		$start_num = intval( ( $requests_template->pag_page - 1 ) * $requests_template->pag_num ) + 1;
		$from_num  = sz_core_number_format( $start_num );
		$to_num    = sz_core_number_format( ( $start_num + ( $requests_template->pag_num - 1 ) > $requests_template->total_request_count ) ? $requests_template->total_request_count : $start_num + ( $requests_template->pag_num - 1 ) );
		$total     = sz_core_number_format( $requests_template->total_request_count );

		if ( 1 == $requests_template->total_request_count ) {
			$message = __( 'Viewing 1 request', 'sportszone' );
		} else {
			$message = sprintf( _n( 'Viewing %1$s - %2$s of %3$s request', 'Viewing %1$s - %2$s of %3$s requests', $requests_template->total_request_count, 'sportszone' ), $from_num, $to_num, $total );
		}

		/**
		 * Filters pagination count text for group membership requests.
		 *
		 * @since 2.0.0
		 *
		 * @param string $message  Pagination count text for group membership requests.
		 * @param string $from_num Total amount for the low value in the range.
		 * @param string $to_num   Total amount for the high value in the range.
		 * @param string $total    Total amount of members found.
		 */
		return apply_filters( 'sz_get_group_requests_pagination_count', $message, $from_num, $to_num, $total );
	}

/** Group Invitations *********************************************************/

/**
 * Whether or not there are invites.
 *
 * @since 1.1.0
 *
 * @param string $args
 * @return bool|mixed|void
 */
function sz_group_has_invites( $args = '' ) {
	global $invites_template, $group_id;

	$r = sz_parse_args( $args, array(
		'group_id' => false,
		'user_id'  => sz_loggedin_user_id(),
		'per_page' => false,
		'page'     => 1,
	), 'group_has_invites' );

	if ( empty( $r['group_id'] ) ) {
		if ( groups_get_current_group() ) {
			$r['group_id'] = sz_get_current_group_id();
		} elseif ( ! empty( sportszone()->groups->new_group_id ) ) {
			$r['group_id'] = sportszone()->groups->new_group_id;
		}
	}

	// Set the global (for use in SZ_Groups_Invite_Template::the_invite()).
	if ( empty( $group_id ) ) {
		$group_id = $r['group_id'];
	}

	if ( ! $group_id ) {
		return false;
	}

	$invites_template = new SZ_Groups_Invite_Template( $r );

	/**
	 * Filters whether or not a group invites query has invites to display.
	 *
	 * @since 1.1.0
	 *
	 * @param bool                      $value            Whether there are requests to display.
	 * @param SZ_Groups_Invite_Template $invites_template Object holding the invites query results.
	 */
	return apply_filters( 'sz_group_has_invites', $invites_template->has_invites(), $invites_template );
}

/**
 * @since 1.1.0
 *
 * @return mixed
 */
function sz_group_invites() {
	global $invites_template;

	return $invites_template->invites();
}

/**
 * @since 1.1.0
 *
 * @return mixed
 */
function sz_group_the_invite() {
	global $invites_template;

	return $invites_template->the_invite();
}

/**
 * @since 1.1.0
 */
function sz_group_invite_item_id() {
	echo sz_get_group_invite_item_id();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_invite_item_id() {
		global $invites_template;

		/**
		 * Filters the group invite item ID.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Group invite item ID.
		 */
		return apply_filters( 'sz_get_group_invite_item_id', 'uid-' . $invites_template->invite->user->id );
	}

/**
 * @since 1.1.0
 */
function sz_group_invite_user_avatar() {
	echo sz_get_group_invite_user_avatar();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_invite_user_avatar() {
		global $invites_template;

		/**
		 * Filters the group invite user avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Group invite user avatar.
		 */
		return apply_filters( 'sz_get_group_invite_user_avatar', $invites_template->invite->user->avatar_thumb );
	}

/**
 * @since 1.1.0
 */
function sz_group_invite_user_link() {
	echo sz_get_group_invite_user_link();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_invite_user_link() {
		global $invites_template;

		/**
		 * Filters the group invite user link.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Group invite user link.
		 */
		return apply_filters( 'sz_get_group_invite_user_link', sz_core_get_userlink( $invites_template->invite->user->id ) );
	}

/**
 * @since 1.1.0
 */
function sz_group_invite_user_last_active() {
	echo sz_get_group_invite_user_last_active();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_group_invite_user_last_active() {
		global $invites_template;

		/**
		 * Filters the group invite user's last active time.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Group invite user's last active time.
		 */
		return apply_filters( 'sz_get_group_invite_user_last_active', $invites_template->invite->user->last_active );
	}

/**
 * @since 1.1.0
 */
function sz_group_invite_user_remove_invite_url() {
	echo sz_get_group_invite_user_remove_invite_url();
}

	/**
	 * @since 1.1.0
	 *
	 * @return string
	 */
	function sz_get_group_invite_user_remove_invite_url() {
		global $invites_template;

		$user_id = intval( $invites_template->invite->user->id );

		if ( sz_is_current_action( 'create' ) ) {
			$uninvite_url = sz_get_groups_directory_permalink() . 'create/step/group-invites/?user_id=' . $user_id;
		} else {
			$uninvite_url = trailingslashit( sz_get_group_permalink( groups_get_current_group() ) . 'send-invites/remove/' . $user_id );
		}

		return wp_nonce_url( $uninvite_url, 'groups_invite_uninvite_user' );
	}

/**
 * Output pagination links for group invitations.
 *
 * @since 2.0.0
 */
function sz_group_invite_pagination_links() {
	echo sz_get_group_invite_pagination_links();
}

	/**
	 * Get pagination links for group invitations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function sz_get_group_invite_pagination_links() {
		global $invites_template;

		/**
		 * Filters the pagination links for group invitations.
		 *
		 * @since 2.0.0
		 *
		 * @param string $value Pagination links for group invitations.
		 */
		return apply_filters( 'sz_get_group_invite_pagination_links', $invites_template->pag_links );
	}

/**
 * Output pagination count text for group invitations.
 *
 * @since 2.0.0
 */
function sz_group_invite_pagination_count() {
	echo sz_get_group_invite_pagination_count();
}
	/**
	 * Get pagination count text for group invitations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function sz_get_group_invite_pagination_count() {
		global $invites_template;

		$start_num = intval( ( $invites_template->pag_page - 1 ) * $invites_template->pag_num ) + 1;
		$from_num  = sz_core_number_format( $start_num );
		$to_num    = sz_core_number_format( ( $start_num + ( $invites_template->pag_num - 1 ) > $invites_template->total_invite_count ) ? $invites_template->total_invite_count : $start_num + ( $invites_template->pag_num - 1 ) );
		$total     = sz_core_number_format( $invites_template->total_invite_count );

		if ( 1 == $invites_template->total_invite_count ) {
			$message = __( 'Viewing 1 invitation', 'sportszone' );
		} else {
			$message = sprintf( _n( 'Viewing %1$s - %2$s of %3$s invitation', 'Viewing %1$s - %2$s of %3$s invitations', $invites_template->total_invite_count, 'sportszone' ), $from_num, $to_num, $total );
		}

		/** This filter is documented in sz-groups/sz-groups-template.php */
		return apply_filters( 'sz_get_groups_pagination_count', $message, $from_num, $to_num, $total );
	}

/** Group RSS *****************************************************************/

/**
 * Hook group activity feed to <head>.
 *
 * @since 1.5.0
 */
function sz_groups_activity_feed() {

	// Bail if not viewing a single group or activity is not active.
	if ( ! sz_is_active( 'groups' ) || ! sz_is_active( 'activity' ) || ! sz_is_group() ) {
		return;
	} ?>

	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ) ?> | <?php sz_current_group_name() ?> | <?php _e( 'Group Activity RSS Feed', 'sportszone' ) ?>" href="<?php sz_group_activity_feed_link() ?>" />

<?php
}
add_action( 'sz_head', 'sz_groups_activity_feed' );

/**
 * Output the current group activity-stream RSS URL.
 *
 * @since 1.5.0
 */
function sz_group_activity_feed_link() {
	echo sz_get_group_activity_feed_link();
}
	/**
	 * Return the current group activity-stream RSS URL.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_group_activity_feed_link() {
		$current_group = groups_get_current_group();
		$group_link    = sz_get_group_permalink( $current_group ) . 'feed';
		$feed_link     = trailingslashit( $group_link );

		/**
		 * Filters the current group activity-stream RSS URL.
		 *
		 * @since 1.2.0
		 *
		 * @param string $feed_link Current group activity-stream RSS URL.
		 */
		return apply_filters( 'sz_get_group_activity_feed_link', $feed_link );
	}

/** Current Group *************************************************************/

/**
 * Echoes the output of sz_get_current_group_id().
 *
 * @since 1.5.0
 */
function sz_current_group_id() {
	echo sz_get_current_group_id();
}
	/**
	 * Returns the ID of the current group.
	 *
	 * @since 1.5.0
	 *
	 * @return int $current_group_id The id of the current group, if there is one.
	 */
	function sz_get_current_group_id() {
		$current_group    = groups_get_current_group();
		$current_group_id = isset( $current_group->id ) ? (int) $current_group->id : 0;

		/**
		 * Filters the ID of the current group.
		 *
		 * @since 1.5.0
		 *
		 * @param int    $current_group_id ID of the current group.
		 * @param object $current_group    Instance holding the current group.
		 */
		return apply_filters( 'sz_get_current_group_id', $current_group_id, $current_group );
	}

/**
 * Echoes the output of sz_get_current_group_slug().
 *
 * @since 1.5.0
 */
function sz_current_group_slug() {
	echo sz_get_current_group_slug();
}
	/**
	 * Returns the slug of the current group.
	 *
	 * @since 1.5.0
	 *
	 * @return string $current_group_slug The slug of the current group, if there is one.
	 */
	function sz_get_current_group_slug() {
		$current_group      = groups_get_current_group();
		$current_group_slug = isset( $current_group->slug ) ? $current_group->slug : '';

		/**
		 * Filters the slug of the current group.
		 *
		 * @since 1.5.0
		 *
		 * @param string $current_group_slug Slug of the current group.
		 * @param object $current_group      Instance holding the current group.
		 */
		return apply_filters( 'sz_get_current_group_slug', $current_group_slug, $current_group );
	}

/**
 * Echoes the output of sz_get_current_group_name().
 *
 * @since 1.5.0
 */
function sz_current_group_name() {
	echo sz_get_current_group_name();
}
	/**
	 * Returns the name of the current group.
	 *
	 * @since 1.5.0
	 *
	 * @return string The name of the current group, if there is one.
	 */
	function sz_get_current_group_name() {
		$current_group = groups_get_current_group();
		$current_name  = sz_get_group_name( $current_group );

		/**
		 * Filters the name of the current group.
		 *
		 * @since 1.2.0
		 *
		 * @param string $current_name  Name of the current group.
		 * @param object $current_group Instance holding the current group.
		 */
		return apply_filters( 'sz_get_current_group_name', $current_name, $current_group );
	}

/**
 * Echoes the output of sz_get_current_group_description().
 *
 * @since 2.1.0
 */
function sz_current_group_description() {
	echo sz_get_current_group_description();
}
	/**
	 * Returns the description of the current group.
	 *
	 * @since 2.1.0
	 *                       this output.
	 *
	 * @return string The description of the current group, if there is one.
	 */
	function sz_get_current_group_description() {
		$current_group      = groups_get_current_group();
		$current_group_desc = isset( $current_group->description ) ? $current_group->description : '';

		/**
		 * Filters the description of the current group.
		 *
		 * This filter is used to apply extra filters related to formatting.
		 *
		 * @since 1.0.0
		 *
		 * @param string $current_group_desc Description of the current group.
		 */
		$desc               = apply_filters( 'sz_get_group_description', $current_group_desc );

		/**
		 * Filters the description of the current group.
		 *
		 * @since 2.1.0
		 *
		 * @param string $desc Description of the current group.
		 */
		return apply_filters( 'sz_get_current_group_description', $desc );
	}

/**
 * Output a URL for a group component action.
 *
 * @since 1.2.0
 *
 * @param string $action
 * @param string $query_args
 * @param bool $nonce
 * @return string|null
 */
function sz_groups_action_link( $action = '', $query_args = '', $nonce = false ) {
	echo sz_get_groups_action_link( $action, $query_args, $nonce );
}
	/**
	 * Get a URL for a group component action.
	 *
	 * @since 1.2.0
	 *
	 * @param string $action
	 * @param string $query_args
	 * @param bool $nonce
	 * @return string
	 */
	function sz_get_groups_action_link( $action = '', $query_args = '', $nonce = false ) {

		$current_group = groups_get_current_group();
		$url           = '';

		// Must be a group.
		if ( ! empty( $current_group->id ) ) {

			// Append $action to $url if provided
			if ( !empty( $action ) ) {
				$url = sz_get_group_permalink( $current_group ) . $action;
			} else {
				$url = sz_get_group_permalink( $current_group );
			}

			// Add a slash at the end of our user url.
			$url = trailingslashit( $url );

			// Add possible query args.
			if ( !empty( $query_args ) && is_array( $query_args ) ) {
				$url = add_query_arg( $query_args, $url );
			}

			// To nonce, or not to nonce...
			if ( true === $nonce ) {
				$url = wp_nonce_url( $url );
			} elseif ( is_string( $nonce ) ) {
				$url = wp_nonce_url( $url, $nonce );
			}
		}

		/**
		 * Filters a URL for a group component action.
		 *
		 * @since 2.1.0
		 *
		 * @param string $url        URL for a group component action.
		 * @param string $action     Action being taken for the group.
		 * @param string $query_args Query arguments being passed.
		 * @param bool   $nonce      Whether or not to add a nonce.
		 */
		return apply_filters( 'sz_get_groups_action_link', $url, $action, $query_args, $nonce );
	}

/** Stats **********************************************************************/

/**
 * Display the number of groups in user's profile.
 *
 * @since 2.0.0
 *
 * @param array|string $args before|after|user_id
 *
 */
function sz_groups_profile_stats( $args = '' ) {
	echo sz_groups_get_profile_stats( $args );
}
add_action( 'sz_members_admin_user_stats', 'sz_groups_profile_stats', 8, 1 );

/**
 * Return the number of groups in user's profile.
 *
 * @since 2.0.0
 *
 * @param array|string $args before|after|user_id
 * @return string HTML for stats output.
 */
function sz_groups_get_profile_stats( $args = '' ) {

	// Parse the args
	$r = sz_parse_args( $args, array(
		'before'  => '<li class="sz-groups-profile-stats">',
		'after'   => '</li>',
		'user_id' => sz_displayed_user_id(),
		'groups'  => 0,
		'output'  => ''
	), 'groups_get_profile_stats' );

	// Allow completely overloaded output
	if ( empty( $r['output'] ) ) {

		// Only proceed if a user ID was passed
		if ( ! empty( $r['user_id'] ) ) {

			// Get the user groups
			if ( empty( $r['groups'] ) ) {
				$r['groups'] = absint( sz_get_total_group_count_for_user( $r['user_id'] ) );
			}

			// If groups exist, show some formatted output
			$r['output'] = $r['before'] . sprintf( _n( '%s group', '%s groups', $r['groups'], 'sportszone' ), '<strong>' . $r['groups'] . '</strong>' ) . $r['after'];
		}
	}

	/**
	 * Filters the number of groups in user's profile.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value HTML for stats output.
	 * @param array  $r     Array of parsed arguments for query.
	 */
	return apply_filters( 'sz_groups_get_profile_stats', $r['output'], $r );
}