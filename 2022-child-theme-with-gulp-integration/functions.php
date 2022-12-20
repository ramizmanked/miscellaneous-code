<?php

add_action( 'wp_enqueue_scripts', 'brc_theme_enqueue_styles' );
function brc_theme_enqueue_styles() {
	wp_enqueue_style(
		'twentytwentytwo-child-style',
		get_stylesheet_uri(),
		array( 'twentytwentytwo-style' ),
		wp_get_theme()->get( 'Version' ) // this only works if you have Version in the style header.
	);
}

function twentytwentytwo_get_font_face_styles() {
	return false;
	/*
	return "
		@font-face{
			font-family: 'Source Serif Pro';
			font-weight: 200 900;
			font-style: normal;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Roman.ttf.woff2' ) . "') format('woff2');
		}

		@font-face{
			font-family: 'Source Serif Pro';
			font-weight: 200 900;
			font-style: italic;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Italic.ttf.woff2' ) . "') format('woff2');
		}

		@font-face{
			font-family: 'Poppins', sans-serif;
			font-weight: 400;
			font-style: normal;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/poppins/Poppins-Regular.woff2' ) . "') format('woff2');
		}
		@font-face{
			font-family: 'Poppins', sans-serif;
			font-weight: 600;
			font-style: normal;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/poppins/Poppins-SemiBold.woff2' ) . "') format('woff2');
		}
		@font-face{
			font-family: 'Poppins', sans-serif;
			font-weight: 700;
			font-style: normal;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/poppins/Poppins-Bold.woff2' ) . "') format('woff2');
		}
		";*/

}
