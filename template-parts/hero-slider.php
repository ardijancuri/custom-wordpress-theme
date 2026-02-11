<?php
/**
 * Hero Slider Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

$slides = array();
for ( $i = 1; $i <= 5; $i++ ) {
	$image = get_theme_mod( "lesnamax_slide_{$i}_image" );
	if ( $image ) {
		$slides[] = array(
			'image'    => $image,
			'title'    => get_theme_mod( "lesnamax_slide_{$i}_title", '' ),
			'subtitle' => get_theme_mod( "lesnamax_slide_{$i}_subtitle", '' ),
			'link'     => get_theme_mod( "lesnamax_slide_{$i}_link", '#' ),
		);
	}
}

// Fallback: if no slides configured, show a placeholder
if ( empty( $slides ) ) {
	$slides[] = array(
		'image'    => LESNAMAX_URI . '/assets/images/placeholder-hero.jpg',
		'title'    => 'Karrige Michella',
		'subtitle' => '',
		'link'     => '#',
	);
}

if ( empty( $slides ) ) {
	return;
}
?>
<section class="hero-slider" aria-label="<?php esc_attr_e( 'Slider kryesor', 'lesnamax' ); ?>">
	<div class="hero-slider__track">
		<?php foreach ( $slides as $index => $slide ) : ?>
			<div class="hero-slide">
				<a href="<?php echo esc_url( $slide['link'] ); ?>">
					<img
						class="hero-slide__image"
						src="<?php echo esc_url( $slide['image'] ); ?>"
						alt="<?php echo esc_attr( $slide['title'] ); ?>"
						<?php echo $index > 0 ? 'loading="lazy"' : ''; ?>
					>
					<?php if ( ! empty( $slide['title'] ) ) : ?>
						<div class="hero-slide__content">
							<h2 class="hero-slide__title"><?php echo esc_html( $slide['title'] ); ?></h2>
							<?php if ( ! empty( $slide['subtitle'] ) ) : ?>
								<p class="hero-slide__subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</a>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( count( $slides ) > 1 ) : ?>
		<div class="hero-slider__dots">
			<?php for ( $i = 0; $i < count( $slides ); $i++ ) : ?>
				<button
					class="hero-slider__dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'lesnamax' ), $i + 1 ) ); ?>"
				></button>
			<?php endfor; ?>
		</div>
	<?php endif; ?>
</section>
