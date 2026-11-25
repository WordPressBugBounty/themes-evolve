<?php
/**
 * Loop Add to Cart
 *
 * evolve: WooCommerce 9.2.0's template (classes, attributes and the link filter come from WooCommerce's $args),
 * with evolve's icon inside the button and evolve's labels and links per product type:
 * variable "Select options", grouped "View options", external and out of stock "Read more" (product page).
 * The labels and links still pass through WooCommerce's filters, so snippets that change them keep working.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

$evolve_permalink = get_permalink( $product->get_id() );
$evolve_icon      = evolve_get_svg( 'more' );

if ( ! $product->is_in_stock() ) {
	$evolve_url   = apply_filters( 'out_of_stock_add_to_cart_url', $evolve_permalink );
	$evolve_label = apply_filters( 'out_of_stock_add_to_cart_text', __( 'Read more', 'evolve' ) );
} else {
	switch ( $product->get_type() ) {
		case 'variable':
			$evolve_url   = apply_filters( 'variable_add_to_cart_url', $evolve_permalink );
			$evolve_label = apply_filters( 'variable_add_to_cart_text', __( 'Select options', 'evolve' ) );
			break;
		case 'grouped':
			$evolve_url   = apply_filters( 'grouped_add_to_cart_url', $evolve_permalink );
			$evolve_label = apply_filters( 'grouped_add_to_cart_text', __( 'View options', 'evolve' ) );
			break;
		case 'external':
			$evolve_url   = apply_filters( 'external_add_to_cart_url', $evolve_permalink );
			$evolve_label = apply_filters( 'external_add_to_cart_text', __( 'Read more', 'evolve' ) );
			break;
		default:
			if ( $product->is_purchasable() ) {
				$evolve_url   = apply_filters( 'add_to_cart_url', $product->add_to_cart_url() );
				$evolve_label = apply_filters( 'add_to_cart_text', __( 'Add to cart', 'evolve' ) );
				$evolve_icon  = evolve_get_svg( 'shop' );
			} else {
				$evolve_url   = apply_filters( 'not_purchasable_url', $evolve_permalink );
				$evolve_label = apply_filters( 'not_purchasable_text', __( 'Read more', 'evolve' ) );
			}
			break;
	}
}

/** This filter is documented in woocommerce/includes/class-wc-product-simple.php */
$evolve_label = apply_filters( 'woocommerce_product_add_to_cart_text', $evolve_label, $product );
if ( $evolve_url === $evolve_permalink ) {
	/** This filter is documented in woocommerce/includes/class-wc-product-simple.php (add_to_cart_url() already applies it to the other links) */
	$evolve_url = apply_filters( 'woocommerce_product_add_to_cart_url', $evolve_url, $product );
}

$args = isset( $args ) && is_array( $args ) ? $args : array();

// External products link to the product page here, so describe the link as such (WooCommerce describes the partner link).
if ( 'external' === $product->get_type() && $product->is_in_stock() && isset( $args['attributes']['aria-label'] ) ) {
	/* translators: %s: Product title */
	$args['attributes']['aria-label'] = sprintf( __( 'Read more about &ldquo;%s&rdquo;', 'evolve' ), wp_strip_all_tags( $product->get_name() ) );
}
// A link to the product page itself needs no nofollow.
if ( $evolve_url === $evolve_permalink && isset( $args['attributes']['rel'] ) && 'nofollow' === $args['attributes']['rel'] ) {
	$args['attributes']['rel'] = '';
}

$aria_describedby = isset( $args['aria-describedby_text'] ) ? sprintf( 'aria-describedby="woocommerce_loop_add_to_cart_link_describedby_%s"', esc_attr( $product->get_id() ) ) : '';

/**
 * Filters the loop add-to-cart link HTML.
 *
 * @param string     $link    The add-to-cart link HTML.
 * @param WC_Product $product The product object.
 * @param array      $args    Arguments used to build the link.
 *
 * @since 2.0.0
 */
echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The public filter intentionally allows extensions to return link markup.
	'woocommerce_loop_add_to_cart_link',
	sprintf(
		'<a href="%s" %s data-quantity="%s" class="%s" %s>%s%s</a>',
		esc_url( $evolve_url ),
		$aria_describedby,
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
		esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		$evolve_icon, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme SVG markup.
		esc_html( $evolve_label )
	),
	$product,
	$args
);
?>
<?php if ( isset( $args['aria-describedby_text'] ) ) : ?>
	<span id="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr( $product->get_id() ); ?>" class="screen-reader-text sr-only">
		<?php echo esc_html( $args['aria-describedby_text'] ); ?>
	</span>
<?php endif; ?>
