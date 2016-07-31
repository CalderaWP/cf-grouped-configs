<?php

namespace calderawp\cf\groupconfig;


class ui {

	/**
	 * Form config
	 *
	 * @var array
	 */
	protected  $form;

	/**
	 * Group slug
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Translation strings
	 *
	 * @var array
	 */
	protected $translation_strings;

	/**
	 * The group fields
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Constructor.
	 *
	 * @param array $form Form congig
	 * @param string $slug Group type slug
	 * @param array $translation_strings Translation strings
	 * @param  array $fields The group fields
	 */
	public function __construct( array $form, $slug, array $translation_strings, array $fields ) {
		$this->form = $form;
		$this->slug = $slug;
		$this->fields = $fields;
		$this->translation_strings = $translation_strings;
		add_filter( 'caldera_forms_processor_ui_input_pre_html', array( $this, 'filter_fields' ), 10, 6 );

		// add template
		add_action( 'caldera_forms_edit_end', array( $this, 'group_template' ) );
		
		//load the script
		wp_enqueue_script( 'cf-group-config', plugins_url( __FILE__ ) . 'cf-config-group.js', array( 'jquery' ) );

	}

	/**
	 * Callback for filter to add field markup for custom processor field types
	 *
	 * @uses "caldera_forms_processor_ui_input_pre_html"
	 *
	 * @param $field_html
	 * @param $type
	 * @param $args
	 * @param $id
	 * @param $classes
	 * @param $required
	 *
	 * @return null|string
	 */
	public function filter_fields( $field_html, $type, $args, $id, $classes, $required ){
		if( 'group' == $type ){
			return $this->group_field();
		}

		if( 'number' == $type ){
			return $this->number_field( $args, $id, $classes, $required );
		}

		if( 'date' == $type ){
			return $this->date_field( $args, $id, $classes, $required );
		}

		return null;
	}

	/**
	 * Print template for group
	 *
	 * @uses "caldera_forms_edit_end" action
	 *
	 * @return string
	 */
	protected function group_field(){

		$out = sprintf( '<div id="{{_id}}_groups"><input class="ajax-trigger" name="{{_name}}[group]" data-name="{{_name}}" autocomplete="off" data-request="%s" data-template="#%s" data-target="#{{_id}}_groups" data-event="build_groups" type="hidden" id="{{_id}}_config_groups" value="{{#if group}}{{json group}}{{/if}}" data-callback="%s" data-processor-id="{{_id}}"></div>',
			esc_attr( str_replace( '-', '_', $this->slug ) . '_group' ),
			esc_attr( $this->slug . '-group-tmpl' ),
			esc_attr( str_replace( '-', '_', $this->slug ) . '_cleanup' )
		);


		// Add new group button trigger
		$out .= sprintf( '<button class="button ajax-trigger %s" title="%" data-name="{{_name}}" data-request="%s" data-template="#%s" data-target="#{{_id}}_groups" data-target-insert="append" type="button" data-callback="%s"><span class="dashicons dashicons-plus" style="margin: 0px 0px 0px -6px; padding: 5px 0px;"></span> %s</button>',
			esc_attr( $this->slug . '-group-add' ),
			esc_attr( $this->translation_strings[ 'add_title' ] ),
			esc_attr( str_replace( '-', '_', $this->slug ) . '_group' ),
			esc_attr( $this->slug . '-group-tmpl' ),
			esc_attr( str_replace( '-', '_', $this->slug ) . '_cleanup' ),
			esc_html__( $this->translation_strings[ 'add_text'] )
		);

		return $out;
	}

	/**
 * Create markup for processor UI date field
 *
 * @param $args
 * @param $id
 * @param $classes
 * @param $required
 *
 * @return string
 */
protected function date_field( $args, $id, $classes, $required ){
	$field = sprintf( '<input type="date" class="%2s" id="{{_id}}_%3s" name="{{_name}}[%4s]" value="%5s" %6s>',
		$classes,
		esc_attr( $id ),
		esc_attr( $id ),
		'{{' . esc_attr( $id ) . '}}',
		$required
	);

	return $field;
}


	/**
 * Create markup for a processor UI number field
 *
 * @param $args
 * @param $id
 * @param $classes
 * @param $required
 *
 * @return string
 */
protected function number_field( $args, $id, $classes, $required ){
	$min = 0;
	$max = false;
	if( isset( $args[ 'min' ] ) ){
		$min = intval( $args['min'] );
	}

	if( isset( $args[ 'max' ] ) && $min < intval( $args[ 'max' ] ) ){
		$max = intval( $args[ 'max' ] );
	}

	$min_max = sprintf( 'min="%d"', $min );
	if( false !== $max ) {
		$min_max = $min_max . sprintf( ' max="%d"', $max );
	}

	$field = sprintf( '<input type="number" class="%s" id="{{_id}}_%s" name="{{_name}}[%s]" value="%s" %s %s >',
		$classes,
		esc_attr( $id ),
		esc_attr( $id ),
		'{{' . esc_attr( $id ) . '}}',
		$required,
		$min_max
	);

	return $field;
}




	/**
	 * Create UI group template
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function group_template(){

		printf( '<script type="text/html" id="%s">', esc_attr( $this->slug . '-group-tmpl' ) );
		echo '{{#each group}}';
		$group = new group_field( $this->fields, $this->slug, $this->translation_strings );
		printf( '<div class="%s">', esc_attr( $this->slug . '-group' ) );
		echo $group->get_html();
		echo '<hr class="clear"></div>';
		echo '{{/each}}';
		echo '</script>';

	}

}
