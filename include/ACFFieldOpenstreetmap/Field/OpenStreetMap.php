<?php

namespace ACFFieldOpenstreetmap\Field;

use ACFFieldOpenstreetmap\Core;
use ACFFieldOpenstreetmap\Helper;

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


class OpenStreetMap extends \acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct() {

		$core = Core\Core::instance();
		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		$this->name = 'open_street_map';
		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		$this->label = __("OpenStreetMap",'acf-open-street-map');

		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		$this->category = 'jquery';

		$this->default_values = array(
			'center_lat'		=> '-37.81411',
			'center_lng'		=> '144.96328',
			'marker_lat'	=> '',
			'marker_lng'	=> '',
			'zoom'				=> '14',
			'address'			=> '',
			'osm_layer'			=> 'mapnik',
			'leaflet_layers'	=> array( 'OpenStreetMap' ),
		);
		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		$this->defaults = array(
			'leaflet_map'		=> $this->default_values,
			'height'			=> 400,
			'return_format'		=> 'leaflet',
			'allow_map_layers'	=> 1,
			'leaflet_layers'	=> $this->default_values['leaflet_layers'],
		);

		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('FIELD_NAME', 'error');
		*/

		$this->l10n = array(
			'foo'	=> 'Bar!',
		);


		$this->l10n = array(
			'locating'			=> __("Locating",'acf'),
			'browser_support'	=> __("Sorry, this browser does not support geolocation",'acf'),
		);


		// do not delete!
    	parent::__construct();

	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {
		$core = Core\Core::instance();
		/*
		*  acf_render_field_setting
		*
		*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
		*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
		*
		*  More than one setting can be added by copy/paste the above code.
		*  Please note that you must also have a matching $defaults value for the field name (font_size)
		*/

		// return_format
		acf_render_field_setting( $field, array(
			'label'			=> __('Return Format','acf'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'choices'		=> array(
				'raw'			=> __("Raw Data",'acf-openstreetmap-field'),
				'leaflet'		=> __("Leaflet JS",'acf-openstreetmap-field'),
				'osm'			=> __("iFrame (OpenStreetMap.org)",'acf-openstreetmap-field'),
			),
			'layout'	=>	'horizontal',
		));


		// center_lat
		acf_render_field_setting( $field, array(
			'label'			=> __('Map Position','acf-openstreetmap-field'),
			'instructions'	=> __('Center the initial map','acf-openstreetmap-field'),
			'type'			=> 'number',
			'name'			=> 'center_lat',
			'prepend'		=> __('lat','acf-openstreetmap-field'),
			'placeholder'	=> $this->default_values['center_lat']
		));


		// center_lng
		acf_render_field_setting( $field, array(
			'label'			=> __('Center','acf-openstreetmap-field'),
			'instructions'	=> __('Center the initial map','acf-openstreetmap-field'),
			'type'			=> 'number',
			'name'			=> 'center_lng',
			'prepend'		=> __('lng','acf-openstreetmap-field'),
			'placeholder'	=> $this->default_values['center_lng'],
			'_append' 		=> 'center_lat'
		));


		// zoom
		acf_render_field_setting( $field, array(
			'label'			=> __('Zoom','acf-openstreetmap-field'),
			'instructions'	=> __('Set the initial zoom level','acf-openstreetmap-field'),
			'type'			=> 'number',
			'name'			=> 'zoom',
			'min'			=> 1,
			'max'			=> 20,
			'prepend'		=> __('zoom','acf-openstreetmap-field'),
			'placeholder'	=> $this->default_values['zoom'],
			'_append' 		=> 'center_lat',
			'attributes'		=> array(
				'data-prop'		=> 'zoom',
			),
			'data-prop'		=> 'zoom',
		));



		acf_render_field_setting( $field, array(
			'label'			=> __('layers','acf-openstreetmap-field'),
			'instructions'	=> __('Choose Layers from the map','acf-openstreetmap-field'),
			'type'			=> 'text',
			'name'			=> 'leaflet_layers',
//			'prepend'		=> __('zoom','acf-openstreetmap-field'),
			'placeholder'	=> $this->default_values['leaflet_layers'],
		));


		acf_render_field_setting( $field, array(
			'label'			=> __('Map Appearance','acf-openstreetmap-field'),
			'instructions'	=> __('Set zoom, center and select layers to be displayed.','acf-openstreetmap-field'),
			'type'			=> 'leaflet_map',
			'name'			=> 'leaflet_map',
			'placeholder'	=> $this->default_values,
		) );


		// map height
		acf_render_field_setting( $field, array(
			'label'			=> __('Height','acf'),
			'instructions'	=> __('Customise the map height','acf-openstreetmap-field'),
			'type'			=> 'text',
			'name'			=> 'height',
			'append'		=> 'px',
		));

		// allow_layer selection
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow layer selection','acf-openstreetmap-field'),
			'instructions'	=> '',
			'name'			=> 'allow_map_layers',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));

		// layers


		acf_render_field_setting( $field, array(
			'label'			=> __('Default OSM Map Layer','acf-openstreetmap-field'),
			'instructions'	=> '',
			'type'			=> 'select',
			'name'			=> 'default_osm_layer',
			'choices'		=> $core->get_osm_layers( ),
			'multiple'		=> 0,
			'ui'			=> 0,
			'allow_null'	=> 0,
			'placeholder'	=> __("Map Layers",'acf-openstreetmap-field'),
		));


	}


	/**
	 *	@param string $key
	 *	@param array $array
	 *	@return scalar
	 */
	// private function array_search_recursive_key( $key, $array ) {
	//
	// 	if ( isset( $array[ $key ] ) && ! is_array( $array[ $key ] ) ) {
	// 		return $array[ $key ];
	// 	}
	//
	// 	foreach ( $array as $k => $v ) {
	// 		if ( ! is_array($v) ) {
	// 			continue;
	// 		}
	// 		$result = $this->array_search_recursive_key( $key, $v );
	// 		if ( $result !== false ) {
	// 			return $result;
	// 		}
	// 	}
	// 	return false;
	// }

	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {

		$core = Core\Core::instance();

		// validate value
		if( empty($field['value']) ) {
			$field['value'] = array();
		}


		// value
		$field['value'] = wp_parse_args( $field['value'], $this->default_values );

		// center
		acf_hidden_input(array(
			'id'		=> $field['id'] . '-center_lat',
			'name'		=> $field['name'] . '[center_lat]',
			'value'		=> $field['value']['center_lat'],
		));

		acf_hidden_input(array(
			'id'		=> $field['id'] . '-center_lng',
			'name'		=> $field['name'] . '[center_lng]',
			'value'		=> $field['value']['center_lng'],
		));

		acf_hidden_input(array(
			'id'		=> $field['id'] . '-zoom',
			'name'		=> $field['name'] . '[zoom]',
			'value'		=> $field['value']['zoom'],
		));


		// marker
		acf_hidden_input(array(
			'id'		=> $field['id'] . '-marker_lat',
			'name'		=> $field['name'] . '[marker_lat]',
			'value'		=> $field['value']['marker_lat'],
		));

		acf_hidden_input(array(
			'id'		=> $field['id'] . '-marker_lng',
			'name'		=> $field['name'] . '[marker_lng]',
			'value'		=> $field['value']['marker_lng'],
		));

		// layers
		if ( $field['allow_map_layers'] ) {
			acf_hidden_input(array(
				'id'		=> $field['id'] . '-leaflet_layers',
				'name'		=> $field['name'] . '[leaflet_layers]',
				'value'		=> $field['value']['leaflet_layers'],
			));
		}


		?>
			<div class="acf-osm-geocode">
			<?php

			acf_text_input( array(
				'id'		=> $field['id'] . '-address',
				'name'		=> $field['name'] . '[address]',
				'type'		=> 'text',
				'value'		=> $field['value']['address'],
				'data-prop'	=> 'address',
			));
			?>
			<div class="osm-results"></div>
		</div>
		<?php

		acf_render_field( array(
			'type'				=> 'leaflet_map',
			'name'				=> $field['name'],
			'value'				=> $field['value'],
			'allow_map_layers'	=> $field['allow_map_layers'],
		) );


	}

	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*
	*/

	function input_admin_enqueue_scripts() {

		wp_enqueue_script('acf-input-osm');

		wp_enqueue_style('acf-input-osm');

	}

	function field_group_admin_enqueue_scripts() {

		wp_enqueue_script('acf-input-osm');

		wp_enqueue_style('acf-input-osm');

	}


	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function input_admin_head() {



	}

	*/


	/*
   	*  input_form_data()
   	*
   	*  This function is called once on the 'input' page between the head and footer
   	*  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
   	*  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
   	*  seen on comments / user edit forms on the front end. This function will always be called, and includes
   	*  $args that related to the current screen such as $args['post_id']
   	*
   	*  @type	function
   	*  @date	6/03/2014
   	*  @since	5.0.0
   	*
   	*  @param	$args (array)
   	*  @return	n/a
   	*/

   	/*

   	function input_form_data( $args ) {



   	}

   	*/


	/*
	*  input_admin_footer()
	*
	*  This action is called in the admin_footer action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_footer)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function input_admin_footer() {



	}

	*/

	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function field_group_admin_enqueue_scripts() {

	}

	*/


	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	*
	*  @type	action (admin_head)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	/*

	function field_group_admin_head() {

	}

	*/


	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/

	/*

	function load_value( $value, $post_id, $field ) {

		return $value;

	}

	*/


	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/

	/*

	function update_value( $value, $post_id, $field ) {

		return $value;

	}

	*/


	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/

	//*

	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) {

			return $value;

		}


		// apply setting
		if( $field['return_format'] === 'osm' ) {

			$bbox = Helper\Maphelper::getBbox( $value['center_lat'], $value['center_lng'], $value['zoom'] );

			$iframe_src_args = array(
				'bbox'	=> implode( ',', $bbox ),
				'layer'	=> $value['osm_layer'],
			);
			$map_link_args = array();

			if ( ! empty( $value['address'] ) ) {
				$iframe_src_args['marker'] = implode(',', array( $value['center_lat'], $value['center_lng'] ) );
				$map_link_args['mlat'] = $value['center_lat'];
				$map_link_args['mlon'] = $value['center_lng'];
			}

			$iframe_src = add_query_arg( $iframe_src_args, 'https://www.openstreetmap.org/export/embed.html' );

			$iframe_atts = array(
				'height'		=> $field['height'],
				'width'			=> '425',
				'frmaeborder'	=> 0,
				'scrolling'		=> 'no',
				'marginheight'	=> 0,
				'marginwidth'	=> 0,
				'src'			=> $iframe_src,
			);

			$map_link = add_query_arg( $map_link_args, 'https://www.openstreetmap.org/' );
			$map_link .= '#map=' . implode( '/', array( $value['zoom'], $value['center_lat'], $value['center_lng'] ) );
			if ( $value['osm_layer'] !== 'mapnik' ) {
				$map_link .= '&layers=' . strtoupper($value['osm_layer'][0]);
			}
			$html = '<iframe %1$s></iframe><br/><small><a href="%2$s">%3$s</a></small>';

			/**
			 *	Filter iframe HTML.
			 *
			 *	@param string $html Template String. Placeholders: $1$s: iFrame Source, $2%s: URL of bigger map, %3$s: Link-Text.
			 */
			$html = apply_filters( 'osm_map_iframe_template', $html );

			$value = sprintf( $html, acf_esc_attr( $iframe_atts ), $map_link, __( 'View Larger Map','acf-field-openstreetmap' ) );

		} else if ( $field['return_format'] === 'leaflet' ) {
			$map_attr = array(
				'class'				=> 'leaflet-map',
				'data-height'		=> $field['height'],
				'data-map'			=> 'leaflet',
				'data-map-lng'		=> $value['center_lng'],
				'data-map-lat'		=> $value['center_lat'],
				'data-map-zoom'		=> $value['zoom'],
				'data-map-layers'	=> $value['leaflet_layers'],
			);

			if ( ! empty( $value['address'] ) ) {

				$map_attr['data-marker-lng']	= $value['marker_lng'];
				$map_attr['data-marker-lat']	= $value['marker_lat'];
				$map_attr['data-marker-label']	= $value['address'];

			}

			$html = sprintf('<div %s></div>', acf_esc_attr( $map_attr ) );
			$value = $html;
			wp_enqueue_script( 'acf-osm-frontend' );
			wp_enqueue_style('leaflet');
		}


		// return
		return $value;
	}


	//*/


	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	*/

	function validate_value( $valid, $value, $field, $input ){

		// bail early if not required
		if( ! $field['required'] ) {

			return $valid;

		}


		if( empty($value) || empty($value['lat']) || empty($value['lng']) ) {

			return false;

		}


		// return
		return $valid;

	}


	/*
	*  delete_value()
	*
	*  This action is fired after a value has been deleted from the db.
	*  Please note that saving a blank value is treated as an update, not a delete
	*
	*  @type	action
	*  @date	6/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (mixed) the $post_id from which the value was deleted
	*  @param	$key (string) the $meta_key which the value was deleted
	*  @return	n/a
	*/

	/*

	function delete_value( $post_id, $key ) {



	}

	*/


	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/


	function load_field( $field ) {
		return $field;

		$defaults = wp_parse_args( array(
			'leaflet_layers'	=> $field['default_leaflet_layers'],
			'osm_layer'			=> $field['default_osm_layer'],
		), $this->default_values );

		$field['value'] = wp_parse_args( $field['value'], $defaults );

		return $field;

	}



	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @date	23/01/2013
	*  @since	3.6.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	$field
	*/

	/*

	function update_field( $field ) {

		$field['value'] = wp_parse_args( $field['value'], $this->default_values );

		return $field;

	}
	*/



	/*
	*  delete_field()
	*
	*  This action is fired after a field is deleted from the database
	*
	*  @type	action
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$field (array) the field array holding all the field options
	*  @return	n/a
	*/

	/*

	function delete_field( $field ) {



	}

	*/


}