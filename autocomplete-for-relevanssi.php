<?php 
/*
Plugin Name: Autocomplete For Relevanssi
Plugin URI: http://www.relevanssi.com/
Description: Autocompletion functionality for WordPress search input when Relevanssi plugin is installed.
Version: 0.1
Author: Jojaba
Author URI: http://jojaba.fr/
Text-domain: autocomplete-for-relevanssi
Domain Path: /languages
*/

/* No direct access */
defined( 'ABSPATH' ) or die( 'Sorry, no direct access to this file ;)' );

/* Language load */
function afr_load_textdomain() {
  load_plugin_textdomain( 'autocomplete-for-relevanssi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'afr_load_textdomain' );

/* **************** */
/* The options page */
/* **************** */
require_once('afr-options.php');

/* ************************** */
/* The main part for frontend */
/* ************************** */

/* Script enqueuing */
if (!function_exists('afr_scripts')) {
    function afr_scripts() {
        $options = get_option( 'afr_settings_options' );
    	$afr_style = (isset($options['afr_style']) && $options['afr_style'] != '') ? $options['afr_style'] :    'default' ;
        if($afr_style == 'default')
            wp_enqueue_style( 'afr-css', plugins_url( 'afr.min.css', __FILE__ ) );
         elseif($afr_style == 'awesomplete')
            wp_enqueue_style( 'afr-css', plugins_url( 'awesomplete-gh-pages/awesomplete.css', __FILE__ ) );
        wp_enqueue_script( 'afr-js', plugins_url( 'awesomplete-gh-pages/awesomplete.js', __FILE__ ), array(),   '0.1', false );
    }
}


/* Adding js functionnalities in footer */
if (!function_exists('afr_footer_add')) {
    function afr_footer_add() {
    // Retrieving settings values
    $options = get_option( 'afr_settings_options' );
    $afr_min_chars = (isset($options['afr_min_chars']) && $options['afr_min_chars'] != '') ? $options['afr_min_chars'] : 2 ;
    $afr_max_suggestions = (isset($options['afr_max_suggestions']) && $options['afr_max_suggestions'] != '') ? $options['afr_max_suggestions'] : 10 ;
    // Get all the relevanssi words
    global $wpdb;
    $iwords = $wpdb->get_col( 'SELECT DISTINCT term FROM '.$wpdb->prefix.'relevanssi' );
    $iwords_list = implode('","', $iwords);
    ?>
    <script>
    var searchInputs = document.getElementsByName("s");
    for(var i = 0; i < searchInputs.length; i++) {
        var awesomplete = new Awesomplete(searchInputs[i]);
        awesomplete.list = ["<?php echo $iwords_list; ?>"];
        awesomplete.minChars = <?php echo $afr_min_chars; ?>;
        awesomplete.maxItems = <?php echo $afr_max_suggestions; ?>;
    }
    </script>
<?php   }
}

// Looking if the relevanssi table exists then enable the plugin actions
global $wpdb;
if($wpdb->get_var('SHOW TABLES LIKE "'.$wpdb->prefix.'relevanssi"') == $wpdb->prefix.'relevanssi') {
    $indexed_words = $wpdb->get_var('SELECT COUNT(*) FROM '.$wpdb->prefix.'relevanssi');
    if($indexed_words > 0) {
        add_action( 'wp_enqueue_scripts', 'afr_scripts' );
        add_action( 'wp_footer', 'afr_footer_add', 50);
    }
}
?>