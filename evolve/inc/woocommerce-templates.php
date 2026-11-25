<?php
/**
 * evolve markup for small WooCommerce template parts, without copying WooCommerce's template files.
 *
 * These used to be full copies of WooCommerce templates in /woocommerce/ that only changed a heading, a class
 * or an icon, and fell out of date with every WooCommerce release. The same markup is now printed here.
 *
 * Compatibility rules (customer sites must keep working):
 * - A child theme or plugin that provides its own copy of the template still wins (evolve_wc_template_is_overridden()).
 * - Code that removes WooCommerce's callback (e.g. remove_action( 'woocommerce_single_product_summary',
 *   'woocommerce_template_single_meta', 40 )) still works: the swap happens only at render time, and only if
 *   WooCommerce's callback is still hooked at that moment (evolve_wc_swap_callback()).
 *
 * @package evolve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether something other than WooCommerce itself provides this template (child theme, plugin filter).
 *
 * @param string $template Template name relative to the WooCommerce templates folder.
 * @return bool
 */
function evolve_wc_template_is_overridden( $template ) {
	$located = wc_locate_template( $template );
	return wp_normalize_path( $located ) !== wp_normalize_path( WC()->plugin_path() . '/templates/' . $template );
}

/**
 * Replace WooCommerce's callback on a hook with evolve's, at render time.
 *
 * @param string   $hook     Action name.
 * @param callable $wc_cb    WooCommerce's callback.
 * @param int      $priority Priority WooCommerce's callback is hooked at.
 * @param string   $template Template WooCommerce's callback would load.
 * @param callable $evolve_cb evolve's callback.
 */
function evolve_wc_swap_callback( $hook, $wc_cb, $priority, $template, $evolve_cb ) {
	add_action(
		$hook,
		function () use ( $hook, $wc_cb, $priority, $template, $evolve_cb ) {
			if ( has_action( $hook, $wc_cb ) !== $priority || evolve_wc_template_is_overridden( $template ) ) {
				return;
			}
			remove_action( $hook, $wc_cb, $priority );
			add_action( $hook, $evolve_cb, $priority );
		},
		$priority - 1
	);
}

/*
 * Breadcrumb: add the Shop page after Home on product, category and tag pages.
 */
add_filter( 'woocommerce_get_breadcrumb', 'evolve_wc_breadcrumb_shop_link' );

function evolve_wc_breadcrumb_shop_link( $breadcrumb ) {
	if ( empty( $breadcrumb ) || ! ( is_product_category() || is_product_tag() || is_product() ) || evolve_wc_template_is_overridden( 'global/breadcrumb.php' ) ) {
		return $breadcrumb;
	}
	$shop_page_id = wc_get_page_id( 'shop' );
	array_splice( $breadcrumb, 1, 0, array( array( get_the_title( $shop_page_id ), get_permalink( $shop_page_id ) ) ) );
	return $breadcrumb;
}

/*
 * Single product title (evolve shows it above the product, see inc/woocommerce-support.php),
 * hidden when the product's "Page title" option is off.
 */
evolve_wc_swap_callback( 'woocommerce_before_single_product', 'woocommerce_template_single_title', 20, 'single-product/title.php', 'evolve_wc_single_title' );

function evolve_wc_single_title() {
	$show = get_post_meta( get_the_ID(), 'evolve_page_title', true );
	if ( 'yes' === $show || '' === $show ) {
		the_title( '<h1 class="post-title" itemprop="name">', '</h1>' );
	}
}

/*
 * Short description.
 */
evolve_wc_swap_callback( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20, 'single-product/short-description.php', 'evolve_wc_single_excerpt' );

function evolve_wc_single_excerpt() {
	global $post;
	$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
	if ( ! $short_description ) {
		return;
	}
	echo '<div class="post-content" itemprop="description">' . $short_description . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post HTML, as in WooCommerce.
}

/*
 * Product meta (SKU, categories, tags) with icons instead of "Category:" / "Tags:" labels.
 */
evolve_wc_swap_callback( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40, 'single-product/meta.php', 'evolve_wc_single_meta' );

function evolve_wc_single_meta() {
	global $product;
	echo '<div class="product_meta post-meta post-meta-footer">';
	do_action( 'woocommerce_product_meta_start' );
	if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) {
		$sku = $product->get_sku();
		echo '<span class="sku_wrapper">' . esc_html__( 'SKU:', 'evolve' ) . ' <span class="sku">' . ( $sku ? esc_html( $sku ) : esc_html__( 'N/A', 'evolve' ) ) . '</span></span>';
	}
	echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . evolve_get_svg( 'category' ) . ' ', '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . evolve_get_svg( 'tag' ) . ' ', '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	do_action( 'woocommerce_product_meta_end' );
	echo '</div>';
}

/*
 * Description and Additional information tabs: h3 headings in a .post-content box.
 * Only when the tab still uses WooCommerce's own callback (plugins may replace it).
 */
add_filter( 'woocommerce_product_tabs', 'evolve_wc_product_tabs', 98 );

function evolve_wc_product_tabs( $tabs ) {
	$swap = array(
		'description'            => array( 'woocommerce_product_description_tab', 'single-product/tabs/description.php', 'evolve_wc_description_tab' ),
		'additional_information' => array( 'woocommerce_product_additional_information_tab', 'single-product/tabs/additional-information.php', 'evolve_wc_additional_information_tab' ),
	);
	foreach ( $swap as $key => list( $wc_cb, $template, $evolve_cb ) ) {
		if ( isset( $tabs[ $key ]['callback'] ) && $wc_cb === $tabs[ $key ]['callback'] && ! evolve_wc_template_is_overridden( $template ) ) {
			$tabs[ $key ]['callback'] = $evolve_cb;
		}
	}
	return $tabs;
}

function evolve_wc_description_tab() {
	$heading = apply_filters( 'woocommerce_product_description_heading', __( 'Product description', 'evolve' ) );
	echo '<div class="post-content" itemprop="mainContentOfPage"><h3>' . esc_html( $heading ) . '</h3>';
	the_content();
	echo '</div>';
}

function evolve_wc_additional_information_tab() {
	global $product;
	$heading = apply_filters( 'woocommerce_product_additional_information_heading', __( 'Additional information', 'evolve' ) );
	echo '<div class="post-content" itemprop="mainContentOfPage">';
	if ( $heading ) {
		echo '<h3>' . esc_html( $heading ) . '</h3>';
	}
	do_action( 'woocommerce_product_additional_information', $product );
	echo '</div>';
}

/*
 * No products found: message plus search suggestions and a product search form.
 */
evolve_wc_swap_callback( 'woocommerce_no_products_found', 'wc_no_products_found', 10, 'loop/no-products-found.php', 'evolve_wc_no_products_found' );

function evolve_wc_no_products_found() {
	echo '<div class="alert alert-warning mb-5" role="alert"><h1 class="page-title">' . esc_html__( 'No products were found matching your selection', 'evolve' ) . '</h1></div>';
	echo '<h2 class="display-4">' . esc_html__( 'Suggestions', 'evolve' ) . '</h2><ul class="lead">';
	echo '<li>' . esc_html__( 'Make sure all words are spelled correctly.', 'evolve' ) . '</li>';
	echo '<li>' . esc_html__( 'Try different keywords.', 'evolve' ) . '</li>';
	echo '<li>' . esc_html__( 'Try more general keywords.', 'evolve' ) . '</li>';
	echo '</ul><div class="search-full-width">';
	get_product_search_form();
	echo '</div>';
}

/**
 * Extra class for WooCommerce buttons in evolve's template copies: WooCommerce's
 * wc_wp_theme_get_element_class_name( 'button' ) where it exists (WooCommerce 7.0.1+), nothing on older stores.
 * It only returns a class for block themes, so for evolve the result is always empty.
 *
 * @return string Class name with a leading space, or an empty string.
 */
function evolve_wc_button_class() {
	$class = function_exists( 'wc_wp_theme_get_element_class_name' ) ? wc_wp_theme_get_element_class_name( 'button' ) : '';

	return $class ? ' ' . $class : '';
}

/**
 * Product name for a cart row in evolve's cart and checkout templates. WooCommerce 11.2+ appends the chosen
 * "Any" variation attributes (WC_Cart::get_item_product_name); older stores get the product name, as their own
 * templates did. Keeps the cart and checkout working on WooCommerce versions before 11.2.
 *
 * @param array      $cart_item Cart item.
 * @param WC_Product $product   Product from the woocommerce_cart_item_product filter.
 * @return string
 */
function evolve_wc_cart_item_name( $cart_item, $product ) {
	if ( is_callable( array( WC()->cart, 'get_item_product_name' ) ) ) {
		return WC()->cart->get_item_product_name( $cart_item, $product );
	}

	return $product->get_name();
}
