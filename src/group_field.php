<?php

namespace calderawp\cf\groupconfig;


class group_field {

	/**
	 * Group slug
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Rendered HTML for the field
	 *
	 * @var  string
	 */
	protected $html;

	/**
	 * Translation strings
	 *
	 * @var array
	 */
	protected $translation_strings;

	/**
	 * group_field constructor.
	 *
	 * @param array $field Field config
	 * @param string $slug Group type slug
	 * @param array $translation_strings Translation strings
	 */
	public function __construct( array  $field, $slug, array $translation_strings ) {
		$this->slug = $slug;
		$this->translation_strings = $translation_strings;
		$this->sub_fields = $field[ 'fields' ];
		$this->set_html();
	}

	/**
	 * Get HTML for field group
	 *
	 * @return string
	 */
	public function get_html(){
		return $this->html;
	}

	/**
	 * Set HTML for the field group
	 */
	protected function set_html(){
		$this->html = $this->create_fields();
	}

	/**
	 * Create fields in group
	 *
	 * @return string
	 */
	protected function create_fields(){
		$out = array();
		foreach( $this->sub_fields as $field ){
			$out[] = \Caldera_Forms_Processor_UI::config_field( $field );
		}

		return sprintf( '%s<button type="button" class="button %s pull-right" title="%s" data-confirm="%s" style="margin-bottom:6px;"><span class="dashicons dashicons-plus" style="margin: 0px 0px 0px -6px; padding: 5px 0px;"></span> %s</button>',
			implode( "\n\n", $out ),
			esc_attr( $this->slug . '-group-remove' ),
			esc_attr( $this->translation_strings[ 'remove_title' ] ),
			esc_attr( $this->translation_strings[ 'remove_confirm' ] ),
			esc_html( $this->translation_strings[ 'remove_text' ] )
		);

	}

}