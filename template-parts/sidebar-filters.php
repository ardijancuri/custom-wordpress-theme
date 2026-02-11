<?php
/**
 * Sidebar Filters Template Part
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}
?>

<!-- Flags Filter -->
<div class="filter-group">
	<h3 class="filter-group__title">
		<?php esc_html_e( 'Flags', 'lesnamax' ); ?>
		<?php lesnamax_icon( 'chevron-down' ); ?>
	</h3>
	<div class="filter-group__list">
		<?php
		$flags = array(
			'trending'    => __( 'Trendit', 'lesnamax' ),
			'sale'        => __( 'Zbritje', 'lesnamax' ),
			'online-only' => __( 'Vetem online', 'lesnamax' ),
			'top-seller'  => __( 'Top seller', 'lesnamax' ),
		);
		foreach ( $flags as $slug => $label ) :
		?>
			<label class="filter-item">
				<input
					type="checkbox"
					class="filter-item__checkbox"
					data-filter-group="flags"
					value="<?php echo esc_attr( $slug ); ?>"
				>
				<span class="filter-item__label"><?php echo esc_html( $label ); ?></span>
			</label>
		<?php endforeach; ?>
	</div>
</div>

<!-- Category Filter -->
<div class="filter-group">
	<h3 class="filter-group__title">
		<?php esc_html_e( 'Kategoria', 'lesnamax' ); ?>
		<?php lesnamax_icon( 'chevron-down' ); ?>
	</h3>
	<div class="filter-group__list">
		<?php
		$categories = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
		) );

		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
			foreach ( $categories as $cat ) :
		?>
			<label class="filter-item">
				<input
					type="checkbox"
					class="filter-item__checkbox"
					data-filter-group="category"
					value="<?php echo esc_attr( $cat->slug ); ?>"
					<?php checked( is_product_category( $cat->slug ) ); ?>
				>
				<span class="filter-item__label"><?php echo esc_html( $cat->name ); ?></span>
				<span class="filter-item__count"><?php echo esc_html( $cat->count ); ?></span>
			</label>
		<?php
			endforeach;
		endif;
		?>
	</div>
</div>

<!-- Attributes Filter (Color/Model) -->
<?php
$filter_attributes = array( 'pa_model', 'pa_ngjyra' );

foreach ( $filter_attributes as $taxonomy ) :
	$terms = get_terms( array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => true,
	) );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		continue;
	}

	$tax_obj = get_taxonomy( $taxonomy );
	$title   = $tax_obj ? $tax_obj->labels->singular_name : $taxonomy;
?>
	<div class="filter-group">
		<h3 class="filter-group__title">
			<?php echo esc_html( $title ); ?>
			<?php lesnamax_icon( 'chevron-down' ); ?>
		</h3>
		<div class="filter-group__list">
			<?php foreach ( $terms as $term ) : ?>
				<label class="filter-item">
					<input
						type="checkbox"
						class="filter-item__checkbox"
						data-filter-group="<?php echo esc_attr( $taxonomy ); ?>"
						value="<?php echo esc_attr( $term->slug ); ?>"
					>
					<span class="filter-item__label"><?php echo esc_html( $term->name ); ?></span>
					<span class="filter-item__count"><?php echo esc_html( $term->count ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</div>
<?php endforeach; ?>
