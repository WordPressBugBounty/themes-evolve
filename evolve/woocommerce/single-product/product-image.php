<?php
/**
 * Single Product Image
 *
 * evolve: product images as a Bootstrap carousel (thumbnails: product-thumbnails.php) instead of WooCommerce's
 * gallery. Reviewed against WooCommerce 11.1.0's template; product videos (WooCommerce 11) are not shown here.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 11.1.0
 */

defined( 'ABSPATH' ) || exit;

global $post, $product;

$loop = 0;

?>

<div class="<?php evolve_single_product_class(); ?>">
    <div id="carousel-slider-product" class="product-carousel carousel slide" data-ride="carousel">
        <div class="carousel-inner carousel-resize">

			<?php if ( $product->get_image_id() ) {

				$attachment_ids = $product->get_gallery_image_ids();
				$gallery        = $attachment_ids ? '[product-gallery]' : '';
				$image_title    = esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_link     = wp_get_attachment_url( get_post_thumbnail_id() );
				$image          = get_the_post_thumbnail( $post->ID, 'woocommerce_single', array(
					'alt'      => $image_title,
					'class'    => 'd-block w-100',
					'itemprop' => 'image',
				) );

				echo sprintf( '<div class="carousel-item active"><a href="%s" itemprop="image" class="woocommerce-product-gallery__image woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '" rel="prettyPhoto">%s</a></div>', esc_url( $image_link ), $image_title, $image ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				foreach ( $attachment_ids as $attachment_id ) {

					$image_link = wp_get_attachment_url( $attachment_id );

					if ( ! $image_link ) {
						continue;
					}

					$image_title = esc_attr( get_the_title( $attachment_id ) );
					$image       = wp_get_attachment_image( $attachment_id, 'woocommerce_single', false, array(
						'alt'   => $image_title,
						'class' => 'd-block w-100',
					) );

					echo sprintf( '<div class="carousel-item"><a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '" rel="prettyPhoto">%s</a></div>', esc_url( $image_link ), $image_title, $image ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$loop ++;
				}
			} else {
				$html = sprintf( '<div class="carousel-item active"><img src="%s" alt="%s" class="wp-post-image d-block w-100" /></div>', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'evolve' ) );
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, 0 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( $loop > 0 ) {
				echo "<a class='carousel-control-prev carousel-control' href='#carousel-slider-product' role='button' data-slide='prev'>
                    <span class='carousel-control-button carousel-control-prev-icon' aria-hidden='true'></span>
                    <span class='screen-reader-text sr-only'>" . esc_html__( 'Previous', 'evolve' ) . "</span>
                </a>
                <a class='carousel-control-next carousel-control' href='#carousel-slider-product' role='button' data-slide='next'>
                <span class='carousel-control-button carousel-control-next-icon' aria-hidden='true'></span>
                <span class='screen-reader-text sr-only'>" . esc_html__( 'Next', 'evolve' ) . "</span>
                </a>";
			} ?>


        </div><!-- .carousel-inner -->
    </div><!-- #product-slider -->

	<?php do_action( 'woocommerce_product_thumbnails' ); ?>

</div>
