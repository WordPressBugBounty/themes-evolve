<?php
/**
 * Single Product Thumbnails
 *
 * evolve: thumbnail strip for the product image carousel (product-image.php).
 * Reviewed against WooCommerce 11.1.0's template; product videos (WooCommerce 11) are not shown here.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     11.1.0
 */

defined( 'ABSPATH' ) || exit;

global $post, $product;

if ( ! $product || ! $product instanceof WC_Product ) {
	return;
}

$attachment_ids = $product->get_gallery_image_ids();

if ( $attachment_ids ) { ?>

    <div id="carousel-slider-thumbnails" class="product-carousel carousel-multiple-items multiple-items carousel slide mb-4 mb-md-0"
         data-ride="carousel" data-wrap="false">
        <div class="carousel-inner row w-100 mx-auto">

			<?php
			if ( $product->get_image_id() ) {

				$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'woocommerce_single' ), array(
					'alt'      => $image_title,
					'class'    => 'd-block w-100',
					'itemprop' => 'image',
				) );

				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="carousel-item col-6 col-md-4 p-0 px-sm-3 active" data-target="#carousel-slider-product" data-slide-to="0">%s</div>', $image ), $post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			$loop = 0;

			foreach ( $attachment_ids as $attachment_id ) {

				if ( ! wp_get_attachment_url( $attachment_id ) ) {
					continue;
				}

				$image_title = esc_attr( get_the_title( $attachment_id ) );
				$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'woocommerce_single' ), false, array(
					'alt'   => $image_title,
					'class' => 'd-block w-100',
				) );

				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="carousel-item col-6 col-md-4 p-0 px-sm-3" data-target="#carousel-slider-product" data-slide-to="%s">%s</div>', $loop + 1, $image ), $attachment_id, $post->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$loop ++;
			} ?>

        </div>

		<?php if ( $loop > 2 ) {
			echo "<a class='left carousel-control-prev carousel-control' href='#carousel-slider-thumbnails' role='button'
           data-slide='prev'>
            <span class='carousel-control-button carousel-control-prev-icon' aria-hidden='true'></span>
            <span class='screen-reader-text sr-only'>" . esc_html__( 'Previous', 'evolve' ) . "</span>
        </a>
        <a class='right carousel-control-next carousel-control' href='#carousel-slider-thumbnails' role='button'
           data-slide='next'>
            <span class='carousel-control-button carousel-control-next-icon' aria-hidden='true'></span>
            <span class='screen-reader-text sr-only'>" . esc_html__( 'Next', 'evolve' ) . "</span>
        </a>";
		} ?>

    </div>

<?php }
