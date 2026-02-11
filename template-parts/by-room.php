<?php
/**
 * By Room Section Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

$rooms = array();
for ( $i = 1; $i <= 8; $i++ ) {
	$image = get_theme_mod( "lesnamax_room_{$i}_image" );
	if ( $image ) {
		$rooms[] = array(
			'image' => $image,
			'name'  => get_theme_mod( "lesnamax_room_{$i}_name", '' ),
			'link'  => get_theme_mod( "lesnamax_room_{$i}_link", '#' ),
			'large' => ( $i === 1 ), // First room is large
		);
	}
}

if ( empty( $rooms ) ) {
	return;
}
?>
<section class="section by-room">
	<h2 class="section-title"><?php esc_html_e( 'By room', 'lesnamax' ); ?></h2>

	<div class="by-room-grid">
		<?php foreach ( $rooms as $room ) : ?>
			<a href="<?php echo esc_url( $room['link'] ); ?>" class="room-card <?php echo $room['large'] ? 'room-card--large' : ''; ?>">
				<img
					class="room-card__image"
					src="<?php echo esc_url( $room['image'] ); ?>"
					alt="<?php echo esc_attr( $room['name'] ); ?>"
					loading="lazy"
				>
				<?php if ( ! empty( $room['name'] ) ) : ?>
					<div class="room-card__overlay">
						<span class="room-card__name"><?php echo esc_html( $room['name'] ); ?></span>
					</div>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
</section>
