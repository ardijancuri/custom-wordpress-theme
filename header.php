<?php
/**
 * The header template.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site-wrapper">
	<?php get_template_part( 'template-parts/header/announcement-bar' ); ?>

	<header id="site-header" class="site-header" role="banner">
		<?php get_template_part( 'template-parts/header/site-header' ); ?>
		<?php get_template_part( 'template-parts/header/navigation' ); ?>
	</header>
