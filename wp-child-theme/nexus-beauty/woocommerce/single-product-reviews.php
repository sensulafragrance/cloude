<?php
/**
 * Review form only: the list of reviews is drawn by nexus_reviews_section() in the design's style.
 *
 * @package NexusBeauty
 * @version 4.3.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}
?>
<div id="review_form_wrapper" hidden>
	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
		<div id="review_form">
			<?php
			$commenter    = wp_get_current_commenter();
			$comment_form = array(
				/* translators: %s is product title */
				'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'nexus-beauty' ) : sprintf( esc_html__( 'Be the first to review “%s”', 'nexus-beauty' ), get_the_title() ),
				'title_reply_to'      => esc_html__( 'Leave a reply to %s', 'nexus-beauty' ),
				'title_reply_before'  => '<span id="reply-title" class="comment-reply-title" role="heading" aria-level="3">',
				'title_reply_after'   => '</span>',
				'comment_notes_after' => '',
				'label_submit'        => esc_html__( 'Post my review', 'nexus-beauty' ),
				'class_submit'        => 'btn btn--primary',
				'logged_in_as'        => '',
				'comment_field'       => '',
			);

			$name_email_required = (bool) get_option( 'require_name_email', 1 );
			$fields              = array(
				'author' => array( 'label' => __( 'Name', 'nexus-beauty' ), 'type' => 'text', 'value' => $commenter['comment_author'], 'required' => $name_email_required, 'autocomplete' => 'name' ),
				'email'  => array( 'label' => __( 'Email', 'nexus-beauty' ), 'type' => 'email', 'value' => $commenter['comment_author_email'], 'required' => $name_email_required, 'autocomplete' => 'email' ),
			);
			$comment_form['fields'] = array();
			foreach ( $fields as $key => $field ) {
				$field_html  = '<p class="comment-form-' . esc_attr( $key ) . '"><label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . ( $field['required'] ? '&nbsp;<span class="required">*</span>' : '' ) . '</label>';
				$field_html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" autocomplete="' . esc_attr( $field['autocomplete'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';
				$comment_form['fields'][ $key ] = $field_html;
			}

			$account_page_url = wc_get_page_permalink( 'myaccount' );
			if ( $account_page_url ) {
				/* translators: %s opening and closing link tags respectively */
				$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'nexus-beauty' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
			}

			if ( wc_review_ratings_enabled() ) {
				$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating" id="comment-form-rating-label">' . esc_html__( 'Your rating', 'nexus-beauty' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
					<option value="">' . esc_html__( 'Rate&hellip;', 'nexus-beauty' ) . '</option>
					<option value="5">' . esc_html__( 'Perfect', 'nexus-beauty' ) . '</option>
					<option value="4">' . esc_html__( 'Good', 'nexus-beauty' ) . '</option>
					<option value="3">' . esc_html__( 'Average', 'nexus-beauty' ) . '</option>
					<option value="2">' . esc_html__( 'Not that bad', 'nexus-beauty' ) . '</option>
					<option value="1">' . esc_html__( 'Very poor', 'nexus-beauty' ) . '</option>
				</select></div>';
			}

			$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'nexus-beauty' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" required></textarea></p>';

			comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
			?>
		</div>
	<?php else : ?>
		<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'nexus-beauty' ); ?></p>
	<?php endif; ?>
</div>
