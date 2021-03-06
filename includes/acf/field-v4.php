<?php
/**
 * Simple Calendar ACF v4.x field support
 *
 * @package    SimpleCalendar/Extensions
 * @subpackage ACF/v4
 */
namespace SimpleCalendar\Acf;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Advanced Custom Field v5 field.
 */
class Field_V4 extends \acf_field {

	/**
	 * Field settings
	 *
	 * @access public
	 * @var array
	 */
	public $settings;

	/**
	 * Field options.
	 *
	 * @access public
	 * @var array
	 */
	public $defaults;

	/**
	 * Setup field data.
	 */
	public function __construct() {

		$this->name     = 'simple_calendar';
		$this->label    = 'Simple Calendar';
		$this->category = __( "Content", 'acf' );
		$this->defaults = array(
			'allow_null' => 1,
		);

		parent::__construct();

		$this->settings = array(
			'path'    => apply_filters( 'acf/helpers/get_path', __FILE__ ),
			'dir'     => apply_filters( 'acf/helpers/get_dir', __FILE__ ),
			'version' => '1.0.0'
		);

	}


	/**
	 * Create options.
	 *
	 * @param $field
	 */
	public function create_options( $field ) {

		$field = array_merge( $this->defaults, $field );
		$key = $field['name'];

		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e( "Allow Null?", 'acf' ); ?></label>
			</td>
			<td>
				<?php

				do_action( 'acf/create_field', array(
					'type'	    => 'radio',
					'name'	    => 'fields['.$key.'][allow_null]',
					'value'     => $field['allow_null'],
					'choices'	=> array(
						1 => __( "Yes", 'acf' ),
						0 => __( "No", 'acf' ),
					),
					'layout'    => 'horizontal',
				) );

				?>
			</td>
		</tr>
		<?php

	}

	/**
	 * Create field.
	 *
	 * @param $field
	 */
	public function create_field( $field ) {

		echo '<select id="' . $field['id'] . '" class="' . $field['class'] . ' fa-select2-field" name="' . $field['name'] . '" >';

			$calendars = simcal_get_calendars();

			if ( $field['allow_null'] || empty( $calendars ) ) {
				echo '<option value="null"></option>';
			}

			if ( ! empty( $calendars ) ) {
				foreach ( $calendars as $id => $name ) {
					$selected = selected( $id, $field['value'], false );
					echo '<option value="' . strval( $id ) . '" ' . $selected . '>' . $name . '</option>' . "\n";
				}
			}

		echo '</select>';

	}

	/**
	 * Enqueue field scripts.
	 */
	public function input_admin_enqueue_scripts() {
		wp_enqueue_script( 'simcal-admin-add-calendar' );
	}

	/**
	 * Load value.
	 *
	 * @param $value
	 * @param $post_id
	 * @param $field
	 *
	 * @return string
	 */
	public function load_value( $value, $post_id, $field ) {
		return is_numeric( $value ) ? intval( $value ) : '';
	}

	/**
	 * Format value for API.
	 *
	 * @param $value
	 * @param $post_id
	 * @param $field
	 *
	 * @return string
	 */
	public function format_value_for_api( $value, $post_id, $field ) {

		$html = '';

		if ( is_numeric( $value ) && $value > 0 ) {

			$calendar = simcal_get_calendar( $value );

			if ( $calendar instanceof \SimpleCalendar\Abstracts\Calendar ) {
				ob_start();
				do_shortcode( '[calendar id="' . $value . '"]' );
				$html = ob_get_clean();
			}
		}

		return $html;
	}

}

new Field_V4();
