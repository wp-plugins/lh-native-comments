<?php
/*
 Plugin Name: LH Native Comments
 Plugin URI: http://lhero.org/plugins/lh-comments/
 Description: Do comments the LH way
 Author: Peter Shaw
 Author URI: http://shawfactor.com
 Version: 1.0.0-dev
 License: GPL v3 (http://www.gnu.org/licenses/gpl.html)
*/



class LH_native_comments_plugin {


/**
 * Take the textarea code out of the default fields and print it on top.
 *
 * @wp-hook comment_form_defaults 100
 * @wp-hook comment_form_top
 * @param   array $input Default fields if called as filter
 * @return  string|void
 */
public function move_textarea( $input = array () ){
    static $textarea = '';

wp_enqueue_script('lh_comment_script', plugins_url( '/scripts/scripts.js' , __FILE__ ), array(), '1.11', true  );

    if ( 'comment_form_defaults' === current_filter() ) {
        // Copy the field to our internal variable …
        $textarea   = "<textarea id=\"comment\" name=\"comment\" aria-required=\"true\" class=\"required\" placeholder=\"Leave a Reply\"  data-lh_comments-css=\"".plugins_url( '/styles/style.css' , __FILE__ )."\" required=\"required\" auto-resize></textarea>\n\n";
        // … and remove it from the defaults array.
        $input['comment_field'] = '';
        return $input;
    }

    // Now called on comment_form_top, $textarea is filled.
    print apply_filters( 'comment_form_field_comment', $textarea );
}

public function comment_form_fields($fields){

$commenter = wp_get_current_commenter();

$fields['author'] = "\n<fieldset id=\"lh_comments-fieldset\" class=\"comment-form-author lh-comment-navigation-input\"><legend>Fill in your details below or <a href=\"".wp_login_url()."\" title=\"login to comment\">login</a></legend><!--[if lt IE 10]><br/><label for=\"author\">".__('Your Name' , 'theme_text_domain') . ($req ? '<span class="required">*</span>' : '') . '</label><![endif]--> ' .'<input id="author" name="author" placeholder="Your Name (required)" type="text" value="'.esc_attr($commenter['comment_author']) . '" class="required" required="required" />' ."</p>\n\n";

$fields['email']  = "\n<p class=\"comment-form-email lh-comment-navigation-input\"><!--[if lt IE 10]><label for=\"email\">".__('Your Email' , 'theme_text_domain') .($req ? '<span class="required">*</span>' : '').'</label><![endif]--> ' .'<input id="email" name="email" placeholder="Your Email (required - never published)" type="email" value="'.esc_attr(  $commenter['comment_author_email']).'" size="40"'.' class="required email" required="required" />' ."</fieldset>\n\n";

$fields['url']  = '';

//$fields['lh_comments_check']  = '<input type="hidden" name="lh_comments_check" value="" id="lh_comments_check" />';



return $fields;
}

public function comment_form_defaults( $defaults ) {

	//$defaults['id_form'] = "lh-comment-form";
	$defaults['logged_in_as'] = "\n<p class=\"logged-in-as\">".sprintf( __('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s">Log out?</a>'), admin_url('profile.php'), $user_identity, wp_logout_url( apply_filters('the_permalink', get_permalink($post->id)))) ."</p>\n\n";
	$defaults['title_reply'] = '<!--[if lt IE 10]>Leave a Reply<![endif]-->';
	$defaults['comment_notes_before'] = '';
	$defaults['comment_notes_after'] = '';
	$defaults['cancel_reply_link'] = '';
	$defaults['label_submit'] = __('Post Comment' , 'theme_text_domain');
	return $defaults;
}



function __construct() {

// We use just one function for both jobs.
add_filter( 'comment_form_defaults', array($this,"move_textarea"),100);
add_action( 'comment_form_top',      array($this,"move_textarea"));
add_filter('comment_form_default_fields',array($this,"comment_form_fields"));
add_filter( 'comment_form_defaults', array($this,"comment_form_defaults"));

}


}

$lh_native_comments = new LH_native_comments_plugin();


?>