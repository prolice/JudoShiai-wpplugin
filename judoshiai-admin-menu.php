<?php

require_once plugin_dir_path(__FILE__) .'config.php';
require plugin_dir_path(__FILE__) . 'lib.php';
/*
 * Add my new menu to the Admin Control Panel
 */
//add_menu_page( 'Info', 'Info', 'manage_options', 'LINK', '', plugins_url( 'myplugin/images/icon.png' ), 10 );
// Hook the 'admin_menu' action hook, run the function named 'judoshiai_signup_admin_link()'
add_action( 'admin_menu', 'judoshiai_signup_admin_link' );
 
// Add a new top level menu link to the ACP
function judoshiai_signup_admin_link()
{
      add_menu_page(
        'Judoshiai Manager', // Title of the page
        'Judoshiai Signup', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'judoshiai-signup/judoshiai_admin_main.php', // The 'slug' - file to display when clicking the link
        '',
        plugins_url( 'judoshiai-signup/judoshiai-4.png' ),
        10       
    );
}

//add_shortcode( ‘njengah_contact_form, ‘render_njengah_contact_form’);
add_shortcode( 'judoshiai_competitor', 'render_judoshiai_competitor');

function render_judoshiai_competitor(){ 

$judoShiaiTemplateFile = get_option('judoshiai_option_name');
//print('sqlite:'.plugin_dir_path(__FILE__) .$judoShiaiTemplateFile);

$dbconn = new PDO('sqlite:'.plugin_dir_path(__FILE__) .$judoShiaiTemplateFile);

if ($dbconn)
{
	$row = sqlite_getInfo($dbconn);
	$categories = sqlite_getCategories($dbconn);
}
else 
	print ('Connection to database failed!\n');

print('
	<div class="wrap">
		<h3>'.$row->Competition[0].' | '.$row->Date[0].' | '.$row->Place[0].'</h3>
		<p></p>
	</div> 
');

}

function judoshiai_plugin_register_settings() {
	add_option('judoshiai_option_name','default.shi');
	register_setting('judoshiai_plugin_options', 'judoshiai_option_name');
}

add_action('admin_init', 'judoshiai_plugin_register_settings');

/*function judoshiai_register_options_page() {
  add_options_page('Judoshiai Options', 'Judoshiai', 'manage_options', 'judoshiai-signup', 'judoshiai_options_page');
}
add_action('admin_menu', 'judoshiai_register_options_page');


function judoshiai_options_page()
{
?>
  <div>
  <?php screen_icon(); ?>
  <h2>JudoShiai Settings</h2>
  <form method="post" action="options.php">
  <?php settings_fields( 'judoshiai_plugin_options' ); ?>
  <p>Set your database:</p>
  <table>
  <tr valign="top">
  <th scope="row"><label for="judoshiai_option_name">Database filename (have to be uploaded by hand) [*.shi]: </label></th>
  <td><input type="text" id="judoshiai_option_name" name="judoshiai_option_name" value="<?php echo get_option('judoshiai_option_name'); ?>" /></td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php
}*/

?>
