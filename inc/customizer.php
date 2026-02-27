<?php
/**
 * Theme Customizer Settings
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Customizer settings.
 */
function lesnamax_customize_register( $wp_customize ) {

	// ---- LOGO SIZE (per breakpoint) ----
	$logo_sizes = array(
		'lesnamax_logo_height_desktop' => array(
			'label'   => __( 'Logo Height — Desktop (px)', 'lesnamax' ),
			'default' => 50,
		),
		'lesnamax_logo_height_tablet' => array(
			'label'   => __( 'Logo Height — Tablet (px)', 'lesnamax' ),
			'default' => 40,
		),
		'lesnamax_logo_height_mobile' => array(
			'label'   => __( 'Logo Height — Mobile (px)', 'lesnamax' ),
			'default' => 35,
		),
	);

	foreach ( $logo_sizes as $setting_id => $args ) {
		$wp_customize->add_setting( $setting_id, array(
			'default'           => $args['default'],
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		) );

		$wp_customize->add_control( $setting_id, array(
			'label'       => $args['label'],
			'section'     => 'title_tagline',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 20,
				'max'  => 200,
				'step' => 1,
			),
		) );
	}

	// ---- BRAND COLORS ----
	$wp_customize->add_section( 'lesnamax_brand_colors', array(
		'title'    => __( 'Brand Colors', 'lesnamax' ),
		'priority' => 25,
	) );

	$wp_customize->add_setting( 'lesnamax_color_primary', array(
		'default'           => '#00BCD4',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'lesnamax_color_primary', array(
		'label'       => __( 'Primary Brand Color', 'lesnamax' ),
		'description' => __( 'Main color for buttons, badges, links, and accents.', 'lesnamax' ),
		'section'     => 'lesnamax_brand_colors',
	) ) );

	$wp_customize->add_setting( 'lesnamax_color_primary_dark', array(
		'default'           => '#00ACC1',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'lesnamax_color_primary_dark', array(
		'label'       => __( 'Primary Dark (Hover)', 'lesnamax' ),
		'description' => __( 'Darker shade used for hover states.', 'lesnamax' ),
		'section'     => 'lesnamax_brand_colors',
	) ) );

	$wp_customize->add_setting( 'lesnamax_color_primary_light', array(
		'default'           => '#B2EBF2',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'lesnamax_color_primary_light', array(
		'label'       => __( 'Primary Light (Focus)', 'lesnamax' ),
		'description' => __( 'Lighter shade used for focus rings and highlights.', 'lesnamax' ),
		'section'     => 'lesnamax_brand_colors',
	) ) );

	// ---- ANNOUNCEMENT BAR ----
	$wp_customize->add_section( 'lesnamax_announcement', array(
		'title'    => __( 'Announcement Bar', 'lesnamax' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'lesnamax_announcement_show', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'lesnamax_announcement_show', array(
		'label'   => __( 'Show Announcement Bar', 'lesnamax' ),
		'section' => 'lesnamax_announcement',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'lesnamax_announcement_text', array(
		'default'           => 'Zbritje ne produkte online.',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'lesnamax_announcement_text', array(
		'label'   => __( 'Announcement Text', 'lesnamax' ),
		'section' => 'lesnamax_announcement',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'lesnamax_announcement_bg_color', array(
		'default'           => '#00BCD4',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'lesnamax_announcement_bg_color', array(
		'label'   => __( 'Announcement Background Color', 'lesnamax' ),
		'section' => 'lesnamax_announcement',
	) ) );

	$wp_customize->add_setting( 'lesnamax_announcement_text_color', array(
		'default'           => '#FFFFFF',
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'lesnamax_announcement_text_color', array(
		'label'   => __( 'Announcement Text Color', 'lesnamax' ),
		'section' => 'lesnamax_announcement',
	) ) );

	// ---- HERO SLIDER ----
	$wp_customize->add_section( 'lesnamax_hero', array(
		'title'    => __( 'Hero Slider', 'lesnamax' ),
		'priority' => 35,
	) );

	for ( $i = 1; $i <= 5; $i++ ) {
		$wp_customize->add_setting( "lesnamax_slide_{$i}_image", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "lesnamax_slide_{$i}_image", array(
			'label'   => sprintf( __( 'Slide %d Image', 'lesnamax' ), $i ),
			'section' => 'lesnamax_hero',
		) ) );

		$wp_customize->add_setting( "lesnamax_slide_{$i}_title", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( "lesnamax_slide_{$i}_title", array(
			'label'   => sprintf( __( 'Slide %d Title', 'lesnamax' ), $i ),
			'section' => 'lesnamax_hero',
			'type'    => 'text',
		) );

		$wp_customize->add_setting( "lesnamax_slide_{$i}_subtitle", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( "lesnamax_slide_{$i}_subtitle", array(
			'label'   => sprintf( __( 'Slide %d Subtitle', 'lesnamax' ), $i ),
			'section' => 'lesnamax_hero',
			'type'    => 'text',
		) );

		$wp_customize->add_setting( "lesnamax_slide_{$i}_link", array(
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( "lesnamax_slide_{$i}_link", array(
			'label'   => sprintf( __( 'Slide %d Link', 'lesnamax' ), $i ),
			'section' => 'lesnamax_hero',
			'type'    => 'url',
		) );

		$wp_customize->add_setting( "lesnamax_slide_{$i}_button_text", array(
			'default'           => 'Shiko me shume',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( "lesnamax_slide_{$i}_button_text", array(
			'label'   => sprintf( __( 'Slide %d Button Text', 'lesnamax' ), $i ),
			'section' => 'lesnamax_hero',
			'type'    => 'text',
		) );
	}

	// ---- PROMO BANNERS ----
	$wp_customize->add_section( 'lesnamax_promos', array(
		'title'    => __( 'Promo Banners', 'lesnamax' ),
		'priority' => 36,
	) );

	for ( $i = 1; $i <= 2; $i++ ) {
		$wp_customize->add_setting( "lesnamax_promo_{$i}_image", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "lesnamax_promo_{$i}_image", array(
			'label'   => sprintf( __( 'Banner %d Image', 'lesnamax' ), $i ),
			'section' => 'lesnamax_promos',
		) ) );

		$wp_customize->add_setting( "lesnamax_promo_{$i}_title", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( "lesnamax_promo_{$i}_title", array(
			'label'   => sprintf( __( 'Banner %d Title', 'lesnamax' ), $i ),
			'section' => 'lesnamax_promos',
			'type'    => 'text',
		) );

		$wp_customize->add_setting( "lesnamax_promo_{$i}_link", array(
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( "lesnamax_promo_{$i}_link", array(
			'label'   => sprintf( __( 'Banner %d Link', 'lesnamax' ), $i ),
			'section' => 'lesnamax_promos',
			'type'    => 'url',
		) );
	}

	// ---- CATEGORY QUICK LINKS ----
	$wp_customize->add_section( 'lesnamax_catlinks', array(
		'title'    => __( 'Category Quick Links', 'lesnamax' ),
		'priority' => 37,
	) );

	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( "lesnamax_catlink_{$i}_label", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( "lesnamax_catlink_{$i}_label", array(
			'label'   => sprintf( __( 'Link %d Label', 'lesnamax' ), $i ),
			'section' => 'lesnamax_catlinks',
			'type'    => 'text',
		) );

		$wp_customize->add_setting( "lesnamax_catlink_{$i}_url", array(
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( "lesnamax_catlink_{$i}_url", array(
			'label'   => sprintf( __( 'Link %d URL', 'lesnamax' ), $i ),
			'section' => 'lesnamax_catlinks',
			'type'    => 'url',
		) );
	}

	// ---- PRODUCT SLIDER ----
	$wp_customize->add_section( 'lesnamax_product_sliders', array(
		'title'    => __( 'Product Slider', 'lesnamax' ),
		'priority' => 39,
	) );

	// Get product categories for dropdown
	$slider_cat_choices = array( '' => __( '— Select Category —', 'lesnamax' ) );
	if ( class_exists( 'WooCommerce' ) ) {
		$slider_cats = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'parent'     => 0,
			'exclude'    => array( get_option( 'default_product_cat' ) ),
		) );
		if ( ! is_wp_error( $slider_cats ) ) {
			foreach ( $slider_cats as $cat ) {
				$slider_cat_choices[ $cat->slug ] = $cat->name;
			}
		}
	}

	$wp_customize->add_setting( 'lesnamax_slider_1_title', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'lesnamax_slider_1_title', array(
		'label'   => __( 'Slider Title', 'lesnamax' ),
		'section' => 'lesnamax_product_sliders',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'lesnamax_slider_1_category', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'lesnamax_slider_1_category', array(
		'label'   => __( 'Slider Category', 'lesnamax' ),
		'section' => 'lesnamax_product_sliders',
		'type'    => 'select',
		'choices' => $slider_cat_choices,
	) );

	// ---- NEWSLETTER ----
	$wp_customize->add_section( 'lesnamax_newsletter', array(
		'title'    => __( 'Newsletter Section', 'lesnamax' ),
		'priority' => 40,
	) );

	$wp_customize->add_setting( 'lesnamax_newsletter_bg_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'lesnamax_newsletter_bg_image', array(
		'label'   => __( 'Background Image', 'lesnamax' ),
		'section' => 'lesnamax_newsletter',
	) ) );

	$wp_customize->add_setting( 'lesnamax_newsletter_title', array(
		'default'           => 'Newsletter',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'lesnamax_newsletter_title', array(
		'label'   => __( 'Newsletter Title', 'lesnamax' ),
		'section' => 'lesnamax_newsletter',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'lesnamax_newsletter_subtitle', array(
		'default'           => 'Merr ofertat më të mira direkt në emailin tënd',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'lesnamax_newsletter_subtitle', array(
		'label'   => __( 'Newsletter Subtitle', 'lesnamax' ),
		'section' => 'lesnamax_newsletter',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'lesnamax_social_facebook', array(
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( 'lesnamax_social_facebook', array(
		'label'   => __( 'Facebook URL', 'lesnamax' ),
		'section' => 'lesnamax_newsletter',
		'type'    => 'url',
	) );

	$wp_customize->add_setting( 'lesnamax_social_instagram', array(
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( 'lesnamax_social_instagram', array(
		'label'   => __( 'Instagram URL', 'lesnamax' ),
		'section' => 'lesnamax_newsletter',
		'type'    => 'url',
	) );

	// ---- FOOTER ----
	$wp_customize->add_section( 'lesnamax_footer', array(
		'title'    => __( 'Footer Settings', 'lesnamax' ),
		'priority' => 90,
	) );

	$wp_customize->add_setting( 'lesnamax_footer_phone', array(
		'default'           => '+38344 / 77-99-77',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'lesnamax_footer_phone', array(
		'label'   => __( 'Phone Number', 'lesnamax' ),
		'section' => 'lesnamax_footer',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'lesnamax_footer_email', array(
		'default'           => 'online.lesnamax@gmail.com',
		'sanitize_callback' => 'sanitize_email',
	) );

	$wp_customize->add_control( 'lesnamax_footer_email', array(
		'label'   => __( 'Email Address', 'lesnamax' ),
		'section' => 'lesnamax_footer',
		'type'    => 'email',
	) );

	$wp_customize->add_setting( 'lesnamax_footer_address', array(
		'default'           => 'Magjistralja Prishtine Fushe Kosove',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'lesnamax_footer_address', array(
		'label'   => __( 'Address', 'lesnamax' ),
		'section' => 'lesnamax_footer',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'lesnamax_footer_hours', array(
		'default'           => 'E HENE - E PREMTE 08:00 - 20:00 / E SHTUNE - E DIEL 09:00 - 18:00',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'lesnamax_footer_hours', array(
		'label'   => __( 'Business Hours', 'lesnamax' ),
		'section' => 'lesnamax_footer',
		'type'    => 'textarea',
	) );

	$footer_columns = array(
		1 => 'RRETH NESH',
		2 => 'TEPIHE',
		3 => 'KUZHINA',
	);

	foreach ( $footer_columns as $column_number => $default_title ) {
		$wp_customize->add_setting( "lesnamax_footer_col{$column_number}_enabled", array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		) );

		$wp_customize->add_control( "lesnamax_footer_col{$column_number}_enabled", array(
			/* translators: %d: footer column number. */
			'label'   => sprintf( __( 'Enable Footer Menu Column %d', 'lesnamax' ), $column_number ),
			'section' => 'lesnamax_footer',
			'type'    => 'checkbox',
		) );

		$wp_customize->add_setting( "lesnamax_footer_col{$column_number}_title", array(
			'default'           => $default_title,
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( "lesnamax_footer_col{$column_number}_title", array(
			/* translators: %d: footer column number. */
			'label'   => sprintf( __( 'Footer Menu Column %d Title', 'lesnamax' ), $column_number ),
			'section' => 'lesnamax_footer',
			'type'    => 'text',
		) );
	}

	// ---- FLOATING CHAT BUTTON ----
	$wp_customize->add_section( 'lesnamax_floating_chat', array(
		'title'    => __( 'Floating Chat Button', 'lesnamax' ),
		'priority' => 92,
	) );

	$wp_customize->add_setting( 'lesnamax_messenger_link', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );

	$wp_customize->add_control( 'lesnamax_messenger_link', array(
		'label'       => __( 'Messenger Link', 'lesnamax' ),
		'description' => __( 'Paste your Facebook Messenger link (e.g. https://m.me/yourpage).', 'lesnamax' ),
		'section'     => 'lesnamax_floating_chat',
		'type'        => 'url',
	) );
}
add_action( 'customize_register', 'lesnamax_customize_register' );

/**
 * Output dynamic CSS for brand colors.
 *
 * Overrides the :root CSS variables when custom colors are set.
 */
function lesnamax_brand_colors_css() {
	$primary       = get_theme_mod( 'lesnamax_color_primary', '#00BCD4' );
	$primary_dark  = get_theme_mod( 'lesnamax_color_primary_dark', '#00ACC1' );
	$primary_light = get_theme_mod( 'lesnamax_color_primary_light', '#B2EBF2' );

	if ( '#00BCD4' !== $primary || '#00ACC1' !== $primary_dark || '#B2EBF2' !== $primary_light ) {
		$css = ':root {';
		$css .= '--color-primary: ' . esc_attr( $primary ) . ';';
		$css .= '--color-primary-dark: ' . esc_attr( $primary_dark ) . ';';
		$css .= '--color-primary-light: ' . esc_attr( $primary_light ) . ';';
		$css .= '--color-badge-new: ' . esc_attr( $primary ) . ';';
		$css .= '--color-badge-sale: ' . esc_attr( $primary ) . ';';
		$css .= '}';
		printf( '<style id="lesnamax-brand-colors">%s</style>', $css );
	}

	$logo_desktop = absint( get_theme_mod( 'lesnamax_logo_height_desktop', 50 ) );
	$logo_tablet  = absint( get_theme_mod( 'lesnamax_logo_height_tablet', 40 ) );
	$logo_mobile  = absint( get_theme_mod( 'lesnamax_logo_height_mobile', 35 ) );

	if ( 50 !== $logo_desktop || 40 !== $logo_tablet || 35 !== $logo_mobile ) {
		$logo_css  = '.site-logo img,.custom-logo{max-height:' . $logo_desktop . 'px;width:auto;}';
		$logo_css .= '@media(max-width:992px){.site-logo img,.custom-logo{max-height:' . $logo_tablet . 'px;}}';
		$logo_css .= '@media(max-width:576px){.site-logo img,.custom-logo{max-height:' . $logo_mobile . 'px;}}';
		printf( '<style id="lesnamax-logo-size">%s</style>', $logo_css );
	}

	$announcement_bg_color   = sanitize_hex_color( get_theme_mod( 'lesnamax_announcement_bg_color', '#00BCD4' ) );
	$announcement_text_color = sanitize_hex_color( get_theme_mod( 'lesnamax_announcement_text_color', '#FFFFFF' ) );

	if ( ! $announcement_bg_color ) {
		$announcement_bg_color = '#00BCD4';
	}

	if ( ! $announcement_text_color ) {
		$announcement_text_color = '#FFFFFF';
	}

	if ( '#00BCD4' !== strtoupper( $announcement_bg_color ) || '#FFFFFF' !== strtoupper( $announcement_text_color ) ) {
		$announcement_css  = '.announcement-bar{background-color:' . esc_attr( $announcement_bg_color ) . ';color:' . esc_attr( $announcement_text_color ) . ';}';
		$announcement_css .= '.announcement-bar .announcement-bar__close,.announcement-bar a,.announcement-bar .icon{color:' . esc_attr( $announcement_text_color ) . ';}';
		printf( '<style id="lesnamax-announcement-colors">%s</style>', $announcement_css );
	}
}
add_action( 'wp_head', 'lesnamax_brand_colors_css', 100 );

/**
 * Live preview JS for Customizer brand colors.
 */
function lesnamax_customize_preview_js() {
	$script = "
	( function( $ ) {
		function updateColor( setting, props ) {
			wp.customize( setting, function( value ) {
				value.bind( function( newVal ) {
					var style = document.getElementById( 'lesnamax-brand-colors' );
					if ( ! style ) {
						style = document.createElement( 'style' );
						style.id = 'lesnamax-brand-colors';
						document.head.appendChild( style );
					}
					var primary      = wp.customize( 'lesnamax_color_primary' ).get();
					var primaryDark  = wp.customize( 'lesnamax_color_primary_dark' ).get();
					var primaryLight = wp.customize( 'lesnamax_color_primary_light' ).get();
					style.textContent = ':root {' +
						'--color-primary: ' + primary + ';' +
						'--color-primary-dark: ' + primaryDark + ';' +
						'--color-primary-light: ' + primaryLight + ';' +
						'--color-badge-new: ' + primary + ';' +
						'--color-badge-sale: ' + primary + ';' +
					'}';
				} );
			} );
		}
		updateColor( 'lesnamax_color_primary' );
		updateColor( 'lesnamax_color_primary_dark' );
		updateColor( 'lesnamax_color_primary_light' );

		function updateLogoSize() {
			var desktop = wp.customize( 'lesnamax_logo_height_desktop' ).get();
			var tablet  = wp.customize( 'lesnamax_logo_height_tablet' ).get();
			var mobile  = wp.customize( 'lesnamax_logo_height_mobile' ).get();
			var style   = document.getElementById( 'lesnamax-logo-size' );
			if ( ! style ) {
				style = document.createElement( 'style' );
				style.id = 'lesnamax-logo-size';
				document.head.appendChild( style );
			}
			style.textContent =
				'.site-logo img,.custom-logo{max-height:' + desktop + 'px;width:auto;}' +
				'@media(max-width:992px){.site-logo img,.custom-logo{max-height:' + tablet + 'px;}}' +
				'@media(max-width:576px){.site-logo img,.custom-logo{max-height:' + mobile + 'px;}}';
		}
		wp.customize( 'lesnamax_logo_height_desktop', function( v ) { v.bind( updateLogoSize ); } );
		wp.customize( 'lesnamax_logo_height_tablet', function( v ) { v.bind( updateLogoSize ); } );
		wp.customize( 'lesnamax_logo_height_mobile', function( v ) { v.bind( updateLogoSize ); } );

		function updateAnnouncementColors() {
			var bgColor = wp.customize( 'lesnamax_announcement_bg_color' ).get();
			var textColor = wp.customize( 'lesnamax_announcement_text_color' ).get();
			var style = document.getElementById( 'lesnamax-announcement-colors' );

			if ( ! style ) {
				style = document.createElement( 'style' );
				style.id = 'lesnamax-announcement-colors';
				document.head.appendChild( style );
			}

			style.textContent =
				'.announcement-bar{background-color:' + bgColor + ';color:' + textColor + ';}' +
				'.announcement-bar .announcement-bar__close,.announcement-bar a,.announcement-bar .icon{color:' + textColor + ';}';
		}

		wp.customize( 'lesnamax_announcement_bg_color', function( v ) { v.bind( updateAnnouncementColors ); } );
		wp.customize( 'lesnamax_announcement_text_color', function( v ) { v.bind( updateAnnouncementColors ); } );
	} )( jQuery );
	";

	wp_add_inline_script( 'customize-preview', $script );
}
add_action( 'customize_preview_init', 'lesnamax_customize_preview_js' );
