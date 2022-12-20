<?php
/**
 * Generates dropdown using CPT posts.
 *
 * @package useful-snippets
 */

/**
 * Generate CPT post dropdown for Gravity Forms.
 *
 * @param array $form Form fields.
 *
 * @return array
 */
function gf_populate_books( array $form ): array {

	foreach ( $form['fields'] as $field ) {
		// phpcs:ignore
		if ( 'select' !== $field->type || str_contains( $field->cssClass, 'populate-books' ) === false ) {
			continue;
		}

		// you can add additional parameters here to alter the posts that are retrieved
		// more info: http://codex.wordpress.org/Template_Tags/get_posts.
		$the_query = new WP_Query(
			[
				'post_type'   => 'book',
				'numberposts' => - 1,
				'post_status' => 'publish',
			]
		);

		if ( $the_query->have_posts() ) {
			$choices = array();

			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$choices[] = array(
					'text'  => get_the_title(),
					'value' => get_post_field( 'post_name' ),
				);
			}

			$field->placeholder = 'Select a Book';
			$field->choices     = $choices;
		}
	}

	return $form;
}
add_filter( 'gform_pre_render_1', 'gf_populate_books' );
add_filter( 'gform_pre_validation_1', 'gf_populate_books' );
add_filter( 'gform_pre_submission_filter_1', 'gf_populate_books' );
add_filter( 'gform_admin_pre_render_1', 'gf_populate_books' );
