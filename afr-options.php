<?php

/* ************************************************************** */
/* The options page menu item                                     */
/* ************************************************************** */

/**
 * Load up the options page
 */
if( !function_exists('afr_options_add_page'))  {
	function afr_options_add_page() {
		add_options_page(
			__( 'Autocomplete for Relevanssi options', 'autocomplete-for-relevanssi' ), // Title for the page
			__( 'Autocomplete for Relevanssi', 'autocomplete-for-relevanssi' ), //  Page name in admin menu
			'manage_options', //  Minimum role required to see the page
			'afr_options_page', // unique identifier
			'afr_options_do_page'  // name of function to display the page
		);
		add_action( 'admin_init', 'afr_options_settings' );
	}
}
add_action( 'admin_menu', 'afr_options_add_page' );

/* ************************************************************** */
/* Option page creation                                           */
/* ************************************************************** */

if( !function_exists('afr_options_do_page'))  {
	function afr_options_do_page() {
	?>

	<div class="wrap">

        <h2><?php _e( 'Autocomplete for Relevanssi options', 'autocomplete-for-relevanssi' ) ?></h2>

        <?php
		// Looking if the relevanssi table exists
    	global $wpdb;
		if($wpdb->get_var('SHOW TABLES LIKE "'.$wpdb->prefix.'relevanssi"') == $wpdb->prefix.'relevanssi') {
			$indexed_words = $wpdb->get_var('SELECT COUNT(*) FROM '.$wpdb->prefix.'relevanssi');
			if($indexed_words > 0)
    			$relevanssi_db = true;
			else
				$relevanssi_db = false;
		} else {
			$relevanssi_db = false;
		}
		/* Only if relevanssi db exists */
		if($relevanssi_db) :
        	/*** To debug, here we can print the plugin options **/
        	/*
        	echo '<pre>';
        	$options = get_option( 'afr_settings_options' );
        	print_r($options);
        	echo '</pre>';
        	*/
		?>
			 <p><?php echo '<strong style="color: #46B450">'.$indexed_words.'</strong> '.__(' terms have been found in the Relevanssi index.', 'autocomplete-for-relevanssi'); ?></p>

        	<form method="post" action="options.php">
        	    <?php settings_fields( 'afr_settings_options' ); ?>
			  	<?php do_settings_sections('afr_setting_section'); ?>
			  	<p><input class="button-primary"  name="Submit" type="submit" value="<?php esc_attr_e(__('Save Changes', 'autocomplete-for-relevanssi')); ?>" /></p>
        	</form>
		<?php else: ?>
			<div class="error settings-error notice"> 
<p><?php _e('Sorry, the Relevanssi Database index table hasn\'t been found or no word is indexed. Did you install Relevanssi? Did you build the index?', 'autocomplete-for-relevanssi'); ?></p></div>
		<?php endif; ?>
	
	</div>

<?php
	} // end afr_options_do_page
}


/* ************************************************************** */
/* The options creation and managing                              */
/* ************************************************************** */

/**
 * Init plugin options to white list our options
 */
if( !function_exists('afr_options_settings'))  {
	function afr_options_settings(){
		/* Register simdiaw settings. */
		register_setting(
			'afr_settings_options',  //$option_group , A settings group name. Must exist prior to the register_setting call. This must match what's called in settings_fields()
			'afr_settings_options', // $option_name The name of an option to sanitize and save.
			'afr_options_validate' // $sanitize_callback  A callback function that sanitizes the option's value.
        );

        /** Add help section **/
		add_settings_section(
			'afr_section_options', //  section name unique ID
			'', // Title or name of the section (to be output on the page), you can leave nbsp here if not wished to display
			'afr_section_text',  // callback to display the content of the section itself
			'afr_setting_section' // The page name. This needs to match the text we gave to the do_settings_sections function call
        );

		add_settings_field(
			'afr_style',
			__( 'Autocomplete box style', 'autocomplete-for-relevanssi' ),
			'afr_func_style',
			'afr_setting_section',
			'afr_section_options'
        );

		add_settings_field(
			'afr_min_chars',
			__( 'Minimum chars', 'autocomplete-for-relevanssi' ),
			'afr_func_min_chars',
			'afr_setting_section',
			'afr_section_options'
        );

         add_settings_field(
			'afr_max_suggestions',
			__( 'Max suggestions', 'autocomplete-for-relevanssi' ),
			'afr_func_max_suggestions',
			'afr_setting_section',
			'afr_section_options'
        );
	}
}

/**
 * Output of main sections and options fields
 */

/** the help section output **/
if( !function_exists('afr_help_text'))  {
	function afr_section_text(){
	echo '<p>'.__( 'Here you can define some settings to adapt this plugin to your flavors&hellip;', 'autocomplete-for-relevanssi' )."\n";
	echo '</p>'."\n";
	}
}

/** The box style **/
function afr_func_style() {
	 /* Get the option value from the database. */
	$options = get_option( 'afr_settings_options' );
	$afr_style = (isset($options['afr_style']) && $options['afr_style'] != '') ? $options['afr_style'] : 'default' ;

	/* Echo the field. */ ?>
		<p>
			<label for="afr_style_default">
				<input type="radio" id="afr_style_default" name="afr_settings_options[afr_style]" value="default" 	<?php if($afr_style == 'default') echo ' checked'; ?>/>
				<?php _e('Default style', 'autocomplete-for-relevanssi') ?>
			</label>
		</p>
		<p class="description" style="padding-left: 25px;">
			<?php _e( 'The default afr style. Simple and lightweight.', 'autocomplete-for-relevanssi' ); ?>
		</p>

		<p>
			<label for="afr_style_awesomplete">
				<input type="radio" id="afr_style_awesomplete" name="afr_settings_options[afr_style]" 	value="awesomplete" <?php if($afr_style == 'awesomplete') echo ' checked'; ?>/>
				<?php _e('Awesomplete default style', 'autocomplete-for-relevanssi') ?>
			</label>
		</p>
		<p class="description" style="padding-left: 25px;">
			<?php _e( 'The default Awesomeplete style.', 'autocomplete-for-relevanssi' ); ?>
		</p>

		<p>
			<label for="afr_style_none">
				<input type="radio" id="afr_style_none" name="afr_settings_options[afr_style]" value="none" <?php 	if($afr_style == 'none') echo ' checked'; ?>/>
				<?php _e('No style', 'autocomplete-for-relevanssi') ?>
			</label>
		</p>
		<p class="description" style="padding-left: 25px;">
			<?php _e( 'No style applied. This means no additionnal <code>.css</code> file will be loaded. You can style the box in your theme stylesheet by using the class you can look over in the following HTML format example. This is the format Awesomplete creates for the autocompletion feature (without the <code>aria-</code> attributes).', 'autocomplete-for-relevanssi' ); ?>
		</p>

		
<pre id="format-example" class="language-html" style="background: #E4E1DF; margin: 10px 25px; overflow: auto;"><code class="language-html" style="background: none;">&lt;div class="awesomplete">
	&lt;input class="search-field" value="" name="s" autocomplete="off" type="search">
	&lt;ul>
		&lt;li>si&lt;mark>te&lt;/mark>&lt;/li>
		&lt;li>&lt;mark>te&lt;/mark>chnician&lt;/li>
		&lt;li>&lt;mark>te&lt;/mark>s&lt;mark>te&lt;/mark>d&lt;/li>
		&lt;li>&lt;mark>te&lt;/mark>xting&lt;/li>
		&lt;li>lis&lt;mark>te&lt;/mark>d&lt;/li>
		&lt;li>sui&lt;mark>te&lt;/mark>bale&lt;/li>
	&lt;/ul>
&lt;/div></code></pre>
	</p>
<?php }

/** The min chars **/
function afr_func_min_chars() {
	 /* Get the option value from the database. */
	$options = get_option( 'afr_settings_options' );
	$afr_min_chars = (isset($options['afr_min_chars']) && $options['afr_min_chars'] != '') ? $options['afr_min_chars'] : 2 ;

	/* Echo the field. */ ?>
	<p>
		<label for="afr_min_chars">
			<input type="number" id="afr_min_chars" name="afr_settings_options[afr_min_chars]" value="<?php echo $afr_min_chars ?>" />
		</label>
		<span class="description">
			<?php _e( 'Set the minimum chars the user type in to reveal the autocompletion box.', 'autocomplete-for-relevanssi' ); ?>
		</span>
	</p>
<?php }

/** The max suggestions  **/
function afr_func_max_suggestions() {
	 /* Get the option value from the database. */
	$options = get_option( 'afr_settings_options' );
	$afr_max_suggestions = (isset($options['afr_max_suggestions']) && $options['afr_max_suggestions'] != '') ? $options['afr_max_suggestions'] : 10 ;

	/* Echo the field. */ ?>
	<p>
		<label for="afr_max_suggestions">
			<input type="number" id="afr_max_suggestions" name="afr_settings_options[afr_max_suggestions]" value="<?php echo $afr_max_suggestions ?>" />
		</label>
		<span class="description">
			<?php _e( 'Set the maximum suggestions displayed in autocomplete box.', 'autocomplete-for-relevanssi' ); ?>
		</span>
	</p>
<?php }



/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
if( !function_exists('afr_options_validate'))  {
	function afr_options_validate( $input ) {
	    $options = get_option( 'afr_settings_options' );

	     /** style validation **/
	     if (isset($input['afr_style'])) {
	        $options['afr_style'] = $input['afr_style']; // default, awesomplete or none
        } else {
			$options['afr_style'] = 'default';
		}

	    /** min chars validation **/
	    if (isset($input['afr_min_chars']) && $input['afr_min_chars'] > 0) {
	        $options['afr_min_chars'] = $input['afr_min_chars'];
        } else {
			$options['afr_min_chars'] = 2;
		}

        /** max suggestions validation **/
        if (isset($input['afr_max_suggestions']) && $input['afr_max_suggestions'] > 0) {
	        $options['afr_max_suggestions'] = $input['afr_max_suggestions'];
        } else {
			$options['afr_max_suggestions'] = 10;
		}
		
		return $options;
	}
}

/* Get prism loaded for admin page */
function afr_prism_admin($hook) {
    if($hook != 'settings_page_afr_options_page') {
            return;
    }
    wp_enqueue_style( 'afr-prism-css', plugins_url('awesomplete-gh-pages/prism/prism.css', __FILE__) );
    wp_enqueue_script( 'afr-prism-js', plugins_url( 'awesomplete-gh-pages/prism/prism.js', __FILE__ ), array(), '0.1', false );
}
add_action( 'admin_enqueue_scripts', 'afr_prism_admin' );