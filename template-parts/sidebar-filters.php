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

// Get global min/max prices for the price filter (published products only)
global $wpdb;
$min_price = floor( (float) $wpdb->get_var(
	"SELECT MIN( pm.meta_value + 0 )
	 FROM {$wpdb->postmeta} pm
	 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	 WHERE pm.meta_key = '_price'
	   AND pm.meta_value != ''
	   AND p.post_type = 'product'
	   AND p.post_status = 'publish'"
) );
$max_price = ceil( (float) $wpdb->get_var(
	"SELECT MAX( pm.meta_value + 0 )
	 FROM {$wpdb->postmeta} pm
	 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	 WHERE pm.meta_key = '_price'
	   AND pm.meta_value != ''
	   AND p.post_type = 'product'
	   AND p.post_status = 'publish'"
) );

if ( $min_price === $max_price ) {
	$max_price = $min_price + 100;
}
?>

<!-- Price Filter -->
<div class="filter-group filter-group--price">
	<h3 class="filter-group__title">
		<?php esc_html_e( 'Cmimi', 'lesnamax' ); ?>
		<?php lesnamax_icon( 'chevron-down' ); ?>
	</h3>
	<div class="filter-group__list">
		<div class="price-filter">
			<div class="price-filter__slider">
				<div class="price-filter__track"></div>
				<div class="price-filter__range" id="price-range-fill"></div>
				<input
					type="range"
					class="price-filter__input price-filter__input--min"
					id="price-min"
					min="<?php echo esc_attr( $min_price ); ?>"
					max="<?php echo esc_attr( $max_price ); ?>"
					value="<?php echo esc_attr( $min_price ); ?>"
					step="1"
				>
				<input
					type="range"
					class="price-filter__input price-filter__input--max"
					id="price-max"
					min="<?php echo esc_attr( $min_price ); ?>"
					max="<?php echo esc_attr( $max_price ); ?>"
					value="<?php echo esc_attr( $max_price ); ?>"
					step="1"
				>
			</div>
			<div class="price-filter__values">
				<span class="price-filter__value" id="price-min-display"><?php echo esc_html( $min_price ); ?>&euro;</span>
				<span class="price-filter__separator">&mdash;</span>
				<span class="price-filter__value" id="price-max-display"><?php echo esc_html( $max_price ); ?>&euro;</span>
			</div>
		</div>
	</div>
</div>

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

<!-- Recently Visited Products -->
<div class="filter-group filter-group--recently-visited" id="recently-visited-section" style="display:none;">
	<h3 class="filter-group__title">
		<?php esc_html_e( 'Shikuar se fundmi', 'lesnamax' ); ?>
		<?php lesnamax_icon( 'chevron-down' ); ?>
	</h3>
	<div class="filter-group__list">
		<div class="recently-visited" id="recently-visited-products"></div>
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
