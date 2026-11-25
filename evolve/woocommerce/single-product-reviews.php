<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 11.2.0
 *
 * evolve: reviews listed with the theme comment layout (evolve_comments_callback), Bootstrap form fields.
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
    return;
}

?>
    <div id="comments" class="woocommerce-reviews">

		<?php if ( have_comments() ) : ?>

            <h3 class="woocommerce-reviews-title mb-0"><?php
				$count = $product->get_review_count();
				if ( $count && wc_review_ratings_enabled() ) {
					/* translators: 1: reviews count 2: product name */
					printf( esc_html( _n( '%1$s Review for %2$s', '%1$s Reviews for %2$s', $count, 'evolve' ) ), esc_html( $count ), '<strong>' . wp_kses_post( get_the_title() ) . '</strong>' );
				} else {
					_e( 'Reviews', 'evolve' );
				}
				?></h3>

            <ol class="comment-list">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array(
					'callback'    => 'evolve_comments_callback',
					'avatar_size' => 45,
					'style'       => 'ol',
					'short_ping'  => true,
				) ) ); ?>
            </ol>

			<?php evolve_comment_navigation( '#reviews' ); ?>

		<?php else : ?>

            <h3 class="no-comment">

				<?php _e( 'There are no reviews yet', 'evolve' ); ?>

            </h3>

		<?php endif; ?>
    </div>

<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>

	<?php
	$commenter           = wp_get_current_commenter();
	$name_email_required = (bool) get_option( 'require_name_email', 1 );
	$required_mark       = $name_email_required ? '&nbsp;<span class="required">*</span>' : '';
	$required_attr       = $name_email_required ? ' required' : '';

	$comment_form = array(
		'title_reply'   => have_comments() ? '<h3 id="reply-title" class="add-review-title">' . __( 'Add a review', 'evolve' ) . '</h3>' : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'evolve' ), wp_kses_post( get_the_title() ) ),
		'fields'        => array(
			'author' => '<p class="comment-form-author">' . '<label for="author">' . esc_html__( 'Name', 'evolve' ) . $required_mark . '</label> ' .
			            '<input id="author" name="author" class="form-control" type="text" autocomplete="name" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $required_attr . ' /></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'evolve' ) . $required_mark . '</label> ' .
			            '<input id="email" name="email" class="form-control" type="email" autocomplete="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $required_attr . ' /></p>',
		),
		'label_submit'  => __( 'Submit', 'evolve' ),
		'comment_field' => '<p class="comment-form-comment"><textarea id="comment" name="comment" class="form-control" cols="45" rows="8" aria-required="true"></textarea></p>',

	);

	if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
		$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a review', 'evolve' ), esc_url( $account_page_url ) ) . '</p>';
	}

	if ( wc_review_ratings_enabled() ) {
		$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'evolve' ) . '</label><div class="container"><select name="rating" id="rating"' . ( wc_review_ratings_required() ? ' required' : '' ) . '>
							<option value="">' . esc_html__( 'Rate&hellip;', 'evolve' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'evolve' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'evolve' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'evolve' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'evolve' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'evolve' ) . '</option>
						</select></div></div>';
		$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'evolve' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" class="form-control" cols="45" rows="8" required></textarea></p>';
	}

	comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) ); ?>

<?php else : ?>

    <p class="woocommerce-verification-required alert alert-warning comments-closed"
       role="alert"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'evolve' ); ?></p>

<?php endif;