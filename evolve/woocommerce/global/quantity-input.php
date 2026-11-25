<?php
/**
 * Product quantity inputs
 *
 * evolve: WooCommerce 10.1.0's template inside a Bootstrap input group with a visible "Quantity" label.
 * When the quantity can't be changed (type "hidden") the field stays invisible, as before.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 *
 * @var bool   $readonly If the input should be set to readonly mode.
 * @var string $type     The input type attribute.
 */

defined( 'ABSPATH' ) || exit;

if ( 'hidden' === $type ) {
	?>
    <div class="quantity hidden">
        <input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty"
               name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>"/>
    </div>
	<?php
	return;
}

/* translators: %s: Quantity. */
$label = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'evolve' ), wp_strip_all_tags( $args['product_name'] ) ) : esc_html__( 'Quantity', 'evolve' );

$classes   = (array) $classes;
$classes[] = 'form-control';

?>
<div class="quantity mb-4">
	<?php
	/**
	 * Hook to output something before the quantity input field.
	 *
	 * @since 7.2.0
	 */
	do_action( 'woocommerce_before_quantity_input_field' );
	?>
    <label class="screen-reader-text sr-only" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $label ); ?></label>
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text" aria-hidden="true"><?php esc_html_e( 'Quantity', 'evolve' ); ?></div>
        </div>
        <input
            type="<?php echo esc_attr( $type ); ?>"
            <?php echo $readonly ? 'readonly="readonly"' : ''; ?>
            id="<?php echo esc_attr( $input_id ); ?>"
            class="<?php echo esc_attr( join( ' ', array_unique( $classes ) ) ); ?>"
            name="<?php echo esc_attr( $input_name ); ?>"
            value="<?php echo esc_attr( $input_value ); ?>"
            aria-label="<?php esc_attr_e( 'Product quantity', 'evolve' ); ?>"
            size="4"
            min="<?php echo esc_attr( $min_value ); ?>"
            <?php if ( 0 < $max_value ) : ?>
                max="<?php echo esc_attr( $max_value ); ?>"
            <?php endif; ?>
            <?php if ( ! $readonly ) : ?>
                step="<?php echo esc_attr( $step ); ?>"
                placeholder="<?php echo esc_attr( $placeholder ); ?>"
                inputmode="<?php echo esc_attr( $inputmode ); ?>"
                autocomplete="<?php echo esc_attr( isset( $autocomplete ) ? $autocomplete : 'on' ); ?>"
            <?php endif; ?>
        />
    </div>
	<?php
	/**
	 * Hook to output something after quantity input field
	 *
	 * @since 3.6.0
	 */
	do_action( 'woocommerce_after_quantity_input_field' );
	?>
</div>
