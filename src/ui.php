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
	 * CF_Discount_UI constructor.
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
			return cf_discount_number_field( $args, $id, $classes, $required );
		}

		if( 'date' == $type ){
			return cf_discount_date_field( $args, $id, $classes, $required );
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
	 * Create UI group template
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function group_template(){

		printf( '<script type="text/html" id="%s">', esc_attr( $this->slug . '-group-tmpl' ) );
		echo '{{#each group}}';
		$group = new group_field( $this->fields, 'cf-discount', $this->translation_strings );
		echo '<div class="cf-discount-group">';
		echo $group->get_html();
		echo '<hr class="clear"></div>';
		echo '{{/each}}';
		echo '</script>';

	}

}
