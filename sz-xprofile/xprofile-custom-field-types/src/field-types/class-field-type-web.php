<?php
/**
 * Post Type Field.
 *
 * @package    SportsZone Xprofile Custom Field Types
 * @subpackage Field_Types
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace SZXProfileCFTR\Field_Types;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Web Type
 */
class Field_Type_Web extends \SZ_XProfile_Field_Type_URL {

	public function __construct() {
		parent::__construct();
		$this->name     = _x( 'Website (HTML5 field)', 'xprofile field type', 'sz-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'sz-xprofile-custom-field-types' );
	}
}
