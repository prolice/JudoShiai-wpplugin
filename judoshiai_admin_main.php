<?php
//require_once plugin_dir_path(__FILE__) .'config.php';
//require plugin_dir_path(__FILE__) . 'lib.php';

/*header("Content-Type:application/json");
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
#$input = file_get_contents('php://input');
$input = json_decode(file_get_contents('php://input'));*/

//session_start();

//print('judoShiaiTemplateFile='.plugin_dir_path(__FILE__) .$judoShiaiTemplateFile.'</ br>');
$judoShiaiTemplateFile = get_option('judoshiai_option_name');
//$dbconn = sqlite3_open($judoShiaiTemplateFile);
$dbconn = new PDO('sqlite:'.plugin_dir_path(__FILE__) .$judoShiaiTemplateFile);

if ($dbconn)
{
	$row = sqlite_getInfo($dbconn);
	$categories = sqlite_getCategories($dbconn);
	$competitors = sqlite_getCompetitors($dbconn, 'christophe@van-beneden.com');
	
}
else 
	print ('Connection to database failed!\n');

print('
	<div class="wrap">
		<h1>'.$row->Competition[0].' | '.$row->Date[0].' | '.$row->Place[0].'</h1>
		<p></p>
	</div> 
');
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
//print_r($categories);print('</BR></BR>');
//print_r($competitors);

?>
