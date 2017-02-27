<?php
/**
 * Plugin Name: Custom Plugin for applying tweaks
 * Plugin URI: #
 * Description: This plugin adds custom functionality to your wordpress site
 * Version: 1.0.0
 * Author: Abhisek Padhi
 * Author URI: #
 * License: GPL2
 */

/*
*Disable Auto Plugin & Theme Updates & JSON REST API
*
*/
add_filter('json_enabled', '__return_false');
add_filter('json_jsonp_enabled', '__return_false');
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_update_theme', '__return_false' );

//To Disable all the Nags & Notifications
function remove_core_updates(){
global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_core','remove_core_updates');
add_filter('pre_site_transient_update_plugins','remove_core_updates');
add_filter('pre_site_transient_update_themes','remove_core_updates');

//Shorcode for Woo Predictive Search by a3rev
function custom_mini_search() {
	echo '<a class="shiftnav-searchbar-toggle  shiftnav-toggle-main-block shiftnav-toggle-main-ontop"><i class="fa fa-search"></i></a>';
	echo '<div class="shiftnav-searchbar-drop">';
	$ps_echo = true ;
	if ( function_exists( 'woo_predictive_search_widget' ) ) woo_predictive_search_widget( $ps_echo );
	echo '</div>';
}
add_shortcode( 'custom-mini-search', 'custom_mini_search' );

// Woocommerce Mini cart icon and dropdown shortcode
function custom_mini_cart() {

	echo '<a href="#" class="dropdown-back" data-toggle="dropdown"> ';
	    echo '<i class="fa fa-shopping-cart" aria-hidden="true"></i>';
	    echo '<div class="basket-item-count" style="display: inline;">';
	        echo '<span class="cart-items-count count">';
	            echo WC()->cart->get_cart_contents_count();
	        echo '</span>';
	    echo '</div>';
	echo '</a>';
	echo '<ul class="dropdown-menu dropdown-menu-mini-cart">';
	        echo '<li> <div class="widget_shopping_cart_content">';
	                  woocommerce_mini_cart();
	            echo '</div></li></ul>';

}
add_shortcode( 'custom-mini-cart', 'custom_mini_cart' );

// Add billing first name to email subject, eg: Abhisek, Thank you for your order

add_filter('woocommerce_email_subject_customer_processing_order', 'abhisek_change_processing_email_subject', 10, 2);

function abhisek_change_processing_email_subject( $subject, $order ) {
global $woocommerce;
$subject = $order->billing_first_name . ', Thank you for your ' . get_bloginfo( 'name', 'display' ) . ' Order!';
return $subject;
}

//Add Next and Prev button below single product page

//add_action( 'woocommerce_before_single_product', 'abhisek_prev_next_product' );
// and if you also want them at the bottom...
add_action( 'woocommerce_after_single_product', 'abhisek_prev_next_product' );

function abhisek_prev_next_product(){

echo '<div class="prev_next_buttons">';

    // 'product_cat' will make sure to return next/prev from current category
        $previous = next_post_link('%link', '&larr; PREVIOUS', TRUE, ' ', 'product_cat');
    $next = previous_post_link('%link', 'NEXT &rarr;', TRUE, ' ', 'product_cat');

    echo $previous;
    echo $next;

echo '</div>';

}

//Change Total label to Total Payable
add_filter( 'gettext', 'upload_wp_text_convert', 20, 3 );
function upload_wp_text_convert( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Total' :
            $translated_text = __( 'Total Payable', 'woocommerce' );
            break;
    }
    return $translated_text;
}

//Social Sharing at end of post
function scrollstory_social_sharing_buttons($content) {
	global $post;
	if(is_single()){

		// Get current page URL
		$scrollstoryURL = urlencode(get_permalink());

		// Get current page title
		$scrollstoryTitle = str_replace( ' ', '%20', get_the_title());

		// Get Post Thumbnail for pinterest
		$scrollstoryThumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

		// Construct sharing URL without using any script
		$twitterURL = 'https://twitter.com/intent/tweet?text='.$scrollstoryTitle.'&amp;url='.$scrollstoryURL.'&amp;via=hiabhisek';
		$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$scrollstoryURL;
		$googleURL = 'https://plus.google.com/share?url='.$scrollstoryURL;
		$bufferURL = 'https://bufferapp.com/add?url='.$scrollstoryURL.'&amp;text='.$scrollstoryTitle;
		$whatsappURL = 'whatsapp://send?text='.$scrollstoryTitle . ' ' . $scrollstoryURL;
		$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url='.$scrollstoryURL.'&amp;title='.$scrollstoryTitle;

		// Based on popular demand added Pinterest too
		$pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$scrollstoryURL.'&amp;media='.$scrollstoryThumbnail[0].'&amp;description='.$scrollstoryTitle;

		// Add sharing button at the end of page/page content
		$content .= '<div class="scrollstory-social">';
		$content .= '<h5>SHARE ON</h5> <a class="scrollstory-link scrollstory-twitter" href="'. $twitterURL .'" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter</a>';
		$content .= '<a class="scrollstory-link scrollstory-facebook" href="'.$facebookURL.'" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i> Facebook</a>';
		$content .= '<a class="scrollstory-link scrollstory-whatsapp" href="'.$whatsappURL.'" target="_blank"><i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp</a>';
		//$content .= '<a class="scrollstory-link scrollstory-googleplus" href="'.$googleURL.'" target="_blank">Google+</a>';
		//$content .= '<a class="scrollstory-link scrollstory-buffer" href="'.$bufferURL.'" target="_blank">Buffer</a>';
		$content .= '<a class="scrollstory-link scrollstory-linkedin" href="'.$linkedInURL.'" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i> LinkedIn</a>';
		//$content .= '<a class="scrollstory-link scrollstory-pinterest" href="'.$pinterestURL.'" target="_blank">Pin It</a>';
		$content .= '</div>';

		return $content;
	}else{
		// if not a post then don't include sharing button
		return $content;
	}
};
add_filter( 'the_content', 'scrollstory_social_sharing_buttons');

function scrollstory_social_sharing_single_product() {
	global $post;
	if(is_product()) {

		// Get current page URL
		$scrollstoryURL = urlencode(get_permalink());

		// Get current page title
		$scrollstoryTitle = str_replace( ' ', '%20', get_the_title());

		// Get Post Thumbnail for pinterest
		$scrollstoryThumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

		// Construct sharing URL without using any script
		$twitterURL = 'https://twitter.com/intent/tweet?text='.$scrollstoryTitle.'&amp;url='.$scrollstoryURL.'&amp;via=hiabhisek';
		$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$scrollstoryURL;
		$googleURL = 'https://plus.google.com/share?url='.$scrollstoryURL;
		$bufferURL = 'https://bufferapp.com/add?url='.$scrollstoryURL.'&amp;text='.$scrollstoryTitle;
		$whatsappURL = 'whatsapp://send?text='.$scrollstoryTitle . ' ' . $scrollstoryURL;
		$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url='.$scrollstoryURL.'&amp;title='.$scrollstoryTitle;

		// Based on popular demand added Pinterest too
		$pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$scrollstoryURL.'&amp;media='.$scrollstoryThumbnail[0].'&amp;description='.$scrollstoryTitle;

		// Add sharing button at the end of page/page content
		echo '<div class="scrollstory-social">';
		echo '<h5>SHARE ON</h5> <a class="scrollstory-link scrollstory-twitter" href="'. $twitterURL .'" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter</a>';
		echo '<a class="scrollstory-link scrollstory-facebook" href="'.$facebookURL.'" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i> Facebook</a>';
		echo '<a class="scrollstory-link scrollstory-whatsapp" href="'.$whatsappURL.'" target="_blank"><i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp</a>';
		//echo '<a class="scrollstory-link scrollstory-googleplus" href="'.$googleURL.'" target="_blank">Google+</a>';
		//echo '<a class="scrollstory-link scrollstory-buffer" href="'.$bufferURL.'" target="_blank">Buffer</a>';
		//echo '<a class="scrollstory-link scrollstory-linkedin" href="'.$linkedInURL.'" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i> LinkedIn</a>';
		//echo '<a class="scrollstory-link scrollstory-pinterest" href="'.$pinterestURL.'" target="_blank">Pin It</a>';
		echo '</div>';

	}
}
add_action( 'woocommerce_product_meta_end', 'scrollstory_social_sharing_single_product' );


// Add Shortcode for predictive search bar
function abhisek_ps_search() {

	$ps_echo = true ;
	if ( function_exists( 'woo_predictive_search_widget' ) ) woo_predictive_search_widget( $ps_echo );

}
add_shortcode( 'abhisek_search', 'abhisek_ps_search' );

// Print something befor my account
function before_my_account () {
  echo '<a href="#"> Click here </a> to know how this Work';
}
add_action ('woocommerce_before_my_account', 'before_my_account');

//Removes Visual composer meta tag from source code
add_action('init', 'myoverride', 100);
function myoverride() {
    remove_action('wp_head', array(visual_composer(), 'addMetaData'));
}

//Custom validations for checkout fields, as per indian norms
add_filter( 'woocommerce_checkout_fields' , 'abhisek_validation_checkout_fields' );
function abhisek_validation_checkout_fields( $fields )
{
     $fields['billing']['billing_phone']['maxlength'] = 10;
     $fields['billing']['billing_postcode']['maxlength'] = 6;
     $fields['billing']['billing_postcode']['custom_attributes'] = array( "minlength" => "6" );
     $fields['billing']['billing_phone']['custom_attributes'] = array( "minlength" => "10" );
     $fields['billing']['billing_phone']['placeholder'] = '10 digits only';
     $fields['shipping']['shipping_phone']['placeholder'] = '10 digits only';
     unset($fields['billing']['billing_company']);
     unset($fields['shipping']['shipping_company']);
     return $fields;
}

//Change field labels of checkout forms
add_filter( 'woocommerce_default_address_fields' , 'abhisek_custom_label' );
function abhisek_custom_label( $address_fields ) {
     $address_fields['state']['label'] = 'State';
     $address_fields['city']['label'] = 'City';
     $address_fields['postcode']['label'] = 'Pincode';
     $address_fields['address_1']['placeholder'] = 'Plot No., Apartment, Room No.';
     $address_fields['address_2']['placeholder'] = 'Street, Area, City';
     return $address_fields;
}

//This basically blanks out the function in pluggable.php responsible for admin notification for lost password changed
if ( !function_exists( 'wp_password_change_notification' ) ) {
 function wp_password_change_notification() {}
}
//OR
//remove_action( 'after_password_reset', 'wp_password_change_notification' );

//Remove woocommerce password meter during registration
function abhisek_remove_password_strength() {
	if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
		wp_dequeue_script( 'wc-password-strength-meter' );
	}
}
add_action( 'wp_print_scripts', 'abhisek_remove_password_strength', 100 );

/**
 * Manipulate default state and countries
 *
 * As always, code goes in your theme functions.php file
 */

function change_default_checkout_country() {
  return 'IN'; // country code
}

function change_default_checkout_state() {
  return 'OD'; // state code
}
add_filter( 'default_checkout_country', 'change_default_checkout_country' );
add_filter( 'default_checkout_state', 'change_default_checkout_state' );

/**
 * Sell only in Orissa
 */
function wc_sell_only_states( $states ) {
	$states['IN'] = array(
		'OD' => __( 'Odisha', 'woocommerce' ),
	);
	return $states;
}
add_filter( 'woocommerce_states', 'wc_sell_only_states' );

// Disable W3TC footer comment for all users
add_filter( 'w3tc_can_print_comment', function( $w3tc_setting ) { return false; }, 10, 1 );

// check for empty-cart get param to clear the cart
add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
  global $woocommerce;

	if ( isset( $_GET['empty-cart'] ) ) {
		$woocommerce->cart->empty_cart();
	}
}

/**
 * remove cod gateway if cart total > 10000
 * @param $gateways
 * @return mixed
 */
add_filter( 'woocommerce_available_payment_gateways' , 'change_payment_gateway', 20, 1);
function change_payment_gateway( $gateways ){
    // Compare cart subtotal (without shipment fees)
    if( WC()->cart->subtotal > 10000 ){
         // then unset the 'cod' key (cod is the unique id of COD Gateway)
         unset( $gateways['cod'] );
    }
    return $gateways;
}

//Redirect Attachment pages to parent post page or to Home URL

function abhisek_attachment_redirect() {
		global $post;
		if ( is_attachment() && isset($post->post_parent) && is_numeric($post->post_parent) && ($post->post_parent != 0) ) {
			wp_redirect(get_permalink($post->post_parent), 301); // permanent redirect to post/page where image or document was uploaded
			exit;
		} elseif ( is_attachment() && isset($post->post_parent) && is_numeric($post->post_parent) && ($post->post_parent < 1) ) {   // for some reason it doesnt works checking for 0, so checking lower than 1 instead...
			wp_redirect(get_bloginfo('wpurl'), 302); // temp redirect to home for image or document not associated to any post/page
			exit;
    }
	}

add_action('template_redirect', 'abhisek_attachment_redirect',1);

//Remove Additional information tab
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
function woo_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] );  	// Remove the additional information tab
    return $tabs;
}

// Add tracking codes in footer
add_action('wp_footer', 'add_googleanalytics');
function add_googleanalytics() { ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-XXXXXXXX-X', 'auto');
  ga('send', 'pageview');

</script>

<!--Responsive youtube embeds, usage <div class="youtube-container"><div class="youtube-player" data-id="l5zw-zQlJBo"></div></div> -->
<script>
(function() {
    var v = document.getElementsByClassName("youtube-player");
    for (var n = 0; n < v.length; n++) {
        var p = document.createElement("div");
        p.innerHTML = abhisekThumb(v[n].dataset.id);
        p.onclick = abhisekIframe;
        v[n].appendChild(p);
    }
})();

function abhisekThumb(id) {
    return '<img class="youtube-thumb" src="//i.ytimg.com/vi/' + id + '/hqdefault.jpg"><div class="play-button"></div>';
}

function abhisekIframe() {
    var iframe = document.createElement("iframe");
    iframe.setAttribute("src", "//www.youtube.com/embed/" + this.parentNode.dataset.id + "?rel=0&autoplay=1&autohide=2&border=0&wmode=opaque&enablejsapi=1&controls=0&showinfo=0");
    iframe.setAttribute("frameborder", "0");
    iframe.setAttribute("id", "youtube-iframe");
    this.parentNode.replaceChild(iframe, this);
}
</script>
<?php }


// Add the code below to your theme's functions.php file to add a confirm password field on the register form under My Accounts.
add_filter('woocommerce_registration_errors', 'registration_errors_validation', 10,3);
function registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
	global $woocommerce;
	extract( $_POST );
	if ( strcmp( $password, $password2 ) !== 0 ) {
		return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'woocommerce' ) );
	}
	return $reg_errors;
}
add_action( 'woocommerce_register_form', 'wc_register_form_password_repeat' );
function wc_register_form_password_repeat() {
	?>
	<p class="form-row form-row-wide">
		<label for="reg_password2"><?php _e( 'Password Repeat', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
	</p>
	<?php
}

//Pre-check the terms and conditions in checkout
function abhisek_wc_terms( $terms_is_checked ) {
	return true;
}
add_filter( 'woocommerce_terms_is_checked', 'abhisek_wc_terms', 10 );
add_filter( 'woocommerce_terms_is_checked_default', 'abhisek_wc_terms', 10 );

//Removes URL field from wordpress comments
function remove_comment_fields($fields) {
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields', 'remove_comment_fields');

//Disabling HTML in WordPress comments
// This will occur when the comment is posted
    function plc_comment_post( $incoming_comment ) {

    // convert everything in a comment to display literally
    $incoming_comment['comment_content'] = htmlspecialchars($incoming_comment['comment_content']);

    // the one exception is single quotes, which cannot be #039; because WordPress marks it as spam
    $incoming_comment['comment_content'] = str_replace( "'", '&apos;', $incoming_comment['comment_content'] );

    return( $incoming_comment );
    }

    // This will occur before a comment is displayed
    function plc_comment_display( $comment_to_display ) {

    // Put the single quotes back in
    $comment_to_display = str_replace( '&apos;', "'", $comment_to_display );

    return $comment_to_display;
}

//Remove Wordpress footer in wp-admin
function remove_footer_admin () {
echo 'Built by <a href="//abhisek.github.io/" target="_blank">abhisek</a>';
}
add_filter('admin_footer_text', 'remove_footer_admin');

//Use Font Awesome Icons in Wordpress Social Login
function wsl_use_fontawesome_icons( $provider_id, $provider_name, $authenticate_url )
{
    ?>
        <a
           rel           = "nofollow"
           href          = "<?php echo $authenticate_url; ?>"
           data-provider = "<?php echo $provider_id ?>"
           class         = "wp-social-login-provider wp-social-login-provider-<?php echo strtolower( $provider_id ); ?>"
         >
            <span>
                <i class="fa fa-<?php echo strtolower( $provider_id ); ?>"></i> <?php echo $provider_name; ?>
            </span>
        </a>
    <?php
}

add_filter( 'wsl_render_auth_widget_alter_provider_icon_markup', 'wsl_use_fontawesome_icons', 10, 3 );

//Add billing_postcode field to woocommerce registration form, add extra fields in similar fashion
//Adding New Fields
function wooc_extra_register_fields() {
	?>

	<div class="field-row form-row form-row-wide">
	<label for="reg_billing_postcode"><?php _e( 'Pincode', 'media_center' ); ?> <span class="required">*</span></label>
	<input type="text" class="le-input input-text" name="billing_postcode" id="reg_billing_postcode" value="<?php if ( ! empty( $_POST['billing_postcode'] ) ) esc_attr_e( $_POST['billing_postcode'] ); ?>" />
	</div>

	<?php
}
add_action( 'woocommerce_register_form_start', 'wooc_extra_register_fields' );

//Validating New Fields
function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {
	if ( isset( $_POST['billing_postcode'] ) && empty( $_POST['billing_postcode'] ) ) {
		$validation_errors->add( 'billing_postcode_error', __( '<strong>Error</strong>: Pincode is required!', 'woocommerce' ) );
	}

}
add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );

//Save New Fields
function wooc_save_extra_register_fields( $customer_id ) {

	if ( isset( $_POST['billing_postcode'] ) ) {
		// WooCommerce postcode
		update_user_meta( $customer_id, 'billing_postcode', sanitize_text_field( $_POST['billing_postcode'] ) );
	}
}
add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );

//Remove Reviews TAB Completely
add_filter( 'woocommerce_product_tabs', 'wcs_woo_remove_reviews_tab', 98 );
function wcs_woo_remove_reviews_tab($tabs) {
 unset($tabs['reviews']);
 return $tabs;
}
?>
