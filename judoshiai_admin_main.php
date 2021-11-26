<?php
$dir = plugin_dir_path(__FILE__) . '/databases/';
$scanResult = scandir($dir);

/*print('<strong>Available database.s in the system</strong></BR>');
foreach ($scanResult as $index => &$file)
{
	if(!is_dir($dir.$file))
  	    print('<pre><strong>'.$file . '</strong></pre></BR>');
}*/


$judoShiaiTemplateFile = get_option('judoshiai_option_name');


$dbconn = dbConnectionSqlite(plugin_dir_path(__FILE__) . '/databases/'. $judoShiaiTemplateFile);


if ($dbconn)
{
	$row = sqlite_getInfo($dbconn);
	$categories = sqlite_getCategories($dbconn);
	//$competitors = sqlite_getCompetitors($dbconn, 'christophe@van-beneden.com');
	if ($row)
	print('
		<div class="wrap">
			<h1 style="color:green">Competition name: '.$row->Competition[0].'</br>Date: '.$row->Date[0].'</br>Place: '.$row->Place[0].'</h1>
			<p></p>
		</div> 
	');
    else print('
		<div class="wrap">
			<h1 style="color:red">NO SUCH TOURNAMENT FILE ...</h1>
			<p></p>
		</div> 
	');
	
	/*if ($categories)
		print_r($categories);*/
}
else 
	print ('<pre><p style="color:red">Connection to database failed!</p></pre>');

/*
<select name="saison_sel" id="saison_sel">
<option <?php if($saison_precedente == $selected){echo selected="selected";} ?> value="<?php echo $saison_precedente; ?>"><?php echo $saison_precedente; ?></option>
<option <?php if($saison_encours == $selected || empty($selected)){echo selected="selected";} ?> value="<?php echo $saison_encours; ?>"><?php echo $saison_encours; ?></option>
<option <?php if($saison_suivante == $selected){echo selected="selected";} ?> value="<?php echo $saison_suivante; ?>"><?php echo $saison_suivante; ?></option>
</select>

<input type="text" id="judoshiai_option_name" name="judoshiai_option_name" value="<?php echo get_option('judoshiai_option_name'); ?>" />
*/
?>
  <div>
  <?php screen_icon(); ?>
  <h2>JudoShiai Settings</h2>
  <form method="post" action="options.php">
  <?php settings_fields( 'judoshiai_plugin_options' ); ?>
  <p>Set your database:</p>
  <table>
  <tr valign="top">
  <th scope="row"><label for="judoshiai_option_name">Database filename [*.shi]: </label></th>
  <td>
	<select name="judoshiai_option_name" id="judoshiai_option_name">
		<?php
		  foreach ($scanResult as $index => &$file){
				if(!is_dir($dir.$file))
					if ($file == get_option('judoshiai_option_name'))
						print('<option selected="selected">'.$file . '</option>');
					else 
						print('<option>'.$file . '</option>');			
			}	

		?>
	</select>
	<?php 
		$filename = plugin_dir_path(__FILE__) . 'databases/'.get_option('judoshiai_option_name');
		/*if (unlink($filename)){
			echo 'The file ' . $filename . ' was deleted successfully!';
		} else {
			echo 'There was a error deleting the file ' . $filename;
		}*/
	?>
  </td>
  </tr>
  <tr valign="top">
  <th scope="row"><label for="judoshiai_option_minYearOfBirth">Minimum Year of Birth: </label></th>
  <td>
	<input name="judoshiai_option_minYearOfBirth" id="judoshiai_option_minYearOfBirth" value="<?php print(get_option('judoshiai_option_minYearOfBirth'));?>"></input>
  </td>
  </tr>
  <tr valign="top">
  <th scope="row"><label for="judoshiai_option_maxYearOfBirth">Maximum Year of Birth: </label></th>
  <td>
	<input name="judoshiai_option_maxYearOfBirth" id="judoshiai_option_maxYearOfBirth" value="<?php print(get_option('judoshiai_option_maxYearOfBirth'));?>"></input>
  </td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
  <div>
  <h1>Upload Your Database File</h1>
<?php
if(isset($_POST['but_submit'])){

  //print_r($_FILES['file']);
  if($_FILES['file']['name'] != ''){
    $uploadedfile = $_FILES['file'];
	$uploadedfilename = $_FILES['file']['tmp_name'];
	$uploadedtargetfilename = $_FILES['file']['name'];
	$path_parts = pathinfo(plugin_dir_path(__FILE__) . 'databases/'.$uploadedtargetfilename);
	
	if ($path_parts['extension'] == 'shi'){	
		$upload_overrides = array( 'test_form' => false );
		//add_filter('upload_dir', plugin_dir_path(__FILE__) . '/databases/');
		$movefile = move_uploaded_file( $uploadedfilename, plugin_dir_path(__FILE__) . 'databases/'.$uploadedtargetfilename );
		//remove_filter('upload_dir', plugin_dir_path(__FILE__) . '/databases/');
		$imageurl = "";
		if ( $movefile ) {
		   $imageurl = plugin_dir_path(__FILE__) . 'databases/'.$uploadedtargetfilename;
		   echo "url : ".$imageurl;
		   } 
		}
		else print('<p style="color:red">[ERROR]</p><strong> Extension not valid! It must be a [*.shi] file</strong>');
	}

}
?>
<!-- Form -->
<form method='post' action='' name='myform' enctype='multipart/form-data'>
  <table>
    <tr>
      <td>Upload a database file</td>
      <td><input type='file' name='file'></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><?php $other_attributes = array( 'value' => 'Submit' ); submit_button('Upload database (*.shi)','submit','but_submit',true,$other_attributes); ?></td>
    </tr>
  </table>
</form>
<table>
    <tr>
      <td>Get Your Database File Back</td>
      <td><a href="<?php echo plugin_dir_url(__FILE__) . 'databases/'.get_option('judoshiai_option_name'); ?>">Download...</a></td>
    </tr>
  </table>
  </div>
  
<?php
//<input type='submit' name='but_submit' value='Submit'>
//print_r($categories);print('</BR></BR>');
//print_r($competitors);

?>
