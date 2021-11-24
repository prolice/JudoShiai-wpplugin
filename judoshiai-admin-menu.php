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

add_shortcode( 'judoshiai_info', 'render_judoshiai_info');

function render_judoshiai_info(){ 

	$judoShiaiTemplateFile = get_option('judoshiai_option_name');

	$dbconn = dbConnectionSqlite(plugin_dir_path(__FILE__) .'/databases/'. $judoShiaiTemplateFile);

	if ($dbconn)
	{
		$row = sqlite_getInfo($dbconn);
		//$categories = sqlite_getCategories($dbconn);
	}
	else 
		print ('Connection to database failed!\n');

	if ($row)
		print('
			<div class="wrap">
				<h3>Competition name: '.$row->Competition[0].'</br>Date: '.$row->Date[0].'</br>Place: '.$row->Place[0].'</h3>
				<p></p>
			</div> 
		');
	else print('
			<div class="wrap">
				<h3 style="color:red">'.$judoShiaiTemplateFile.' not found !!</h3>
				<p></p>
			</div> 
		');

}

add_shortcode( 'judoshiai_competitors_full_list', 'render_judoshiai_competitors_full_list');
function render_judoshiai_competitors_full_list(){ 

	$judoShiaiTemplateFile = get_option('judoshiai_option_name');
	$dbconn = dbConnectionSqlite(plugin_dir_path(__FILE__) .'/databases/'. $judoShiaiTemplateFile);

	if ($dbconn){
		$array_competitors = sqlite_getCompetitors($dbconn,"");
	}
	else 
		print ('Connection to database failed!\n');

	if ($array_competitors)
	{
		//print_r($array_competitors);
		print('<table><th>Firstname</th><th>Lastname</th><th>Year of Birth</th><th>Category</th><th>Club</th></tr>');
		foreach ($array_competitors as $competitor)
		{
			foreach ($competitor as $item);
			  print_r('<td>'.$item.'</td>');
			  
			print('</tr>');
			//print('<td>'.$competitor["firstName"].'</td><td>'.$competitor["lastName"].'</td><td>'.$competitor["yearOfBirth"].'</td><td>'.$competitor["category"].'</td><td>'.$competitor["club"].'</td>');
		}
		print('</table>');
	}

}



function judoshiai_plugin_register_settings() {
	add_option('judoshiai_option_name','default.shi');
	register_setting('judoshiai_plugin_options', 'judoshiai_option_name');
}

add_action('admin_init', 'judoshiai_plugin_register_settings');

?>
