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

	// ---- BY ROOM SECTION ----
	$wp_customize->add_section( 'lesnamax_rooms', array(
		'title'    => __( 'By Room Section', 'lesnamax' ),
		'priority' => 38,
	) );

	for ( $i = 1; $i <= 8; $i++ ) {
		$wp_customize->add_setting( "lesnamax_room_{$i}_image", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "lesnamax_room_{$i}_image", array(
			'label'   => sprintf( __( 'Room %d Image', 'lesnamax' ), $i ),
			'section' => 'lesnamax_rooms',
		) ) );

		$wp_customize->add_setting( "lesnamax_room_{$i}_name", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( "lesnamax_room_{$i}_name", array(
			'label'   => sprintf( __( 'Room %d Name', 'lesnamax' ), $i ),
			'section' => 'lesnamax_rooms',
			'type'    => 'text',
		) );

		$wp_customize->add_setting( "lesnamax_room_{$i}_link", array(
			'default'           => '#',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( "lesnamax_room_{$i}_link", array(
			'label'   => sprintf( __( 'Room %d Link', 'lesnamax' ), $i ),
			'section' => 'lesnamax_rooms',
			'type'    => 'url',
		) );
	}

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
}
add_action( 'customize_register', 'lesnamax_customize_register' );
