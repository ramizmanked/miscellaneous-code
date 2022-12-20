<?php

function my_get_gallery_image_with_title_html( $attachment_id, $main_image = false ) {
	global $product;

	$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	// $alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
	$image       = wp_get_attachment_image(
		$attachment_id,
		$image_size,
		false,
		apply_filters(
			'woocommerce_gallery_image_html_attachment_image_params',
			array(
				'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-src'                => esc_url( $full_src[0] ),
				'data-large_image'        => esc_url( $full_src[0] ),
				'data-large_image_width'  => esc_attr( $full_src[1] ),
				'data-large_image_height' => esc_attr( $full_src[2] ),
				'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
			),
			$attachment_id,
			$image_size,
			$main_image
		)
	);
	$image_title = esc_html( get_the_title( $attachment_id ) );

	$parent_id  = $product->get_id();
	$variant_id = false;
	if ( ! empty( $attachment_id ) ) {
		$the_query = new WP_Query(
			array(
				'meta_query'  => array(
					array(
						'key'   => '_thumbnail_id',
						'value' => $attachment_id,
					),
				),
				'post_parent' => $parent_id,
				'post_type'   => array( 'product_variation' ),
				// 'posts_per_page'    => '1'
			)
		);
		if ( $the_query->have_posts() ) {
			$html = '';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$variant_id       = get_the_ID();
				$variant_in_stock = '0' != get_post_meta( $variant_id, '_stock', true ) ? 'true' : 'false';
				$html            .= '<div data-is-variant-in-stock="' . $variant_in_stock . '" data-variant-id="' . $variant_id . '" data-title="' . $image_title . '" data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_src[0] ) . '">' . $image . '</a></div>';
			}
		}

		return $html;
	}
}

if ( ! function_exists( 'wc_dropdown_variation_attribute_options' ) ) {

	/**
	 * Output a list of variation attributes for use in the cart forms.
	 *
	 * @param array $args Arguments.
	 * @since 2.4.0
	 */
	function wc_dropdown_variation_attribute_options( $args = array() ) {
		$args = wp_parse_args(
			apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
			array(
				'options'          => false,
				'attribute'        => false,
				'product'          => false,
				'selected'         => false,
				'name'             => '',
				'id'               => '',
				'class'            => '',
				'show_option_none' => __( 'Choose an option', 'woocommerce' ),
			)
		);

		// Get selected value.
		if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
			$selected_key = 'attribute_' . sanitize_title( $args['attribute'] );
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] );
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = $args['class'];
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		$html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
		$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms(
					$product->get_id(),
					$attribute,
					array(
						'fields' => 'all',
					)
				);

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, (array) $options, true ) ) {
						$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
					}
				}
			} else {
				$count = 0;
				foreach ( $options as $option ) {
					$variation_id = false;
					if ( ! empty( $option ) ) {
						$prod_attributes = $product->get_attributes();
						$meta_query      = array( 'relation' => 'OR' );

						if ( is_array( $prod_attributes ) && count( $prod_attributes ) > 0 ) {
							foreach ( $prod_attributes as $key => $value ) {
								$meta_query[] = array(
									'key'   => 'attribute_' . $key,
									'value' => $option,
								);
							}
						}

						$the_query = new WP_Query(
							array(
								'post_parent' => $product->get_id(),
								'post_type'   => 'product_variation',
								'fields'      => 'ids',
								'meta_query'  => $meta_query,
							)
						);

						if ( $the_query->posts && is_array( $the_query->posts ) ) {
							$variation_id = implode( ',', $the_query->posts );
						}
					}
					// if ( '0' != get_post_meta( $variations[ $count ], '_stock', true ) ) {
					// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
					$html    .= '<option data-variant-id="' . $variation_id . '" value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
					// }
					$count++;
				}
			}
		}

		$html .= '</select>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args );
	}
}

add_filter( 'woocommerce_ajax_variation_threshold', 'my_ajax_variation_threshold', 10, 2 );
function my_ajax_variation_threshold( $default, $product ) {
	return 50; // increase this number if needed.
}
