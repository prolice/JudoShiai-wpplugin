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
	else {
		$result = 'Connection to database failed!';
		return $result ;
	}

	if ($row){
		$result = '
			<div class="wrap">
				<h3>Competition name: '.$row->Competition[0].'</br>Date: '.$row->Date[0].'</br>Place: '.$row->Place[0].'</h3>
				<p></p>
			</div> 
		';
		$yearOfTournament = date('Y', $dateOfTournamentDate);
	}	
	else $result = '
			<div class="wrap">
				<h3 style="color:red">'.$judoShiaiTemplateFile.' not found !!</h3>
				<p></p>
			</div> 
		';
		
	return $result;

}

add_shortcode( 'judoshiai_competitor_add', 'render_judoshiai_competitor_add');

function render_judoshiai_competitor_add(){ 

	//$judoShiaiTemplateFile = get_option('judoshiai_option_name');
	$minYearOfBirth = get_option('judoshiai_option_minYearOfBirth');
	$maxYearOfBirth = get_option('judoshiai_option_maxYearOfBirth');
	//$yearOfTournament = 2022;
	//$dbconn = dbConnectionSqlite(plugin_dir_path(__FILE__) .'/databases/'. $judoShiaiTemplateFile);
    
	$result = '
		<p>$yearOfTournament</p>
		<div class="row">
            <div class="col-md form-group"><label for="input_firstName">Firstname</label><input required name="firstName" id="input_firstName" type="text" class="form-control"></div>
            <div class="col-md form-group"><label for="input_lastName">Lastname</label><input required name="lastName" id="input_lastName" type="text" class="form-control"></div>
        </div>
		<div class="row" style="display:flex;">
            <fieldset class="col-md form-group">
              <legend class="label">Sex</legend>
              <div id="btn_group_sex" class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                <label style="display:inline-block;margin-bottom:.5rem;font-size:1rem;" class="btn btn-outline-primary active w-100" for="input_male">
                  <input style="-moz-appearance: textfield;-webkit-appearance: none;margin: 0;" type="radio" name="sex" id="input_male" value="m" checked="" autocomplete="off">male
                </label>
                <label style="display:inline-block;margin-bottom:.5rem;font-size:1rem;" class="btn btn-outline-primary w-100" for="input_female">
                  <input style="-moz-appearance: textfield;-webkit-appearance: none;margin: 0;" type="radio" name="sex" id="input_female" value="f" autocomplete="off">female
                </label>
              </div>
            </fieldset>
            <div class="col-md form-group">
              <label style="display:inline-block;margin-bottom:.5rem;font-size:1rem;" for="input_yearOfBirth">Year of Birth</label>
              <div class="input-group" style="display:flex;">
                <div class="input-group-prepend">
                  <button id="btn_dec_yearOfBirth" class="btn btn-outline-primary" type="button" tabindex="-1">-</button>
                </div>
                <input style="-moz-appearance: textfield;margin: 0;" name="yearOfBirth" id="input_yearOfBirth" type="number" required value="'.floor(($minYearOfBirth + $maxYearOfBirth) / 2).'" min="'.$minYearOfBirth.'" max="'.$maxYearOfBirth.'" class="form-control input-number–noSpinners">
                <div class="input-group-append">
                  <button id="btn_inc_yearOfBirth" class="btn btn-outline-primary" type="button" tabindex="-1">+</button>
                </div>
              </div>
              <div class="row" style="display:flex;">
            <div class="col-md form-group">
              <label for="labelAgeCat"><?php echo(_("Age Category")); ?></label>
              <input id="labelAgeCat" type="text" readonly class="form-control-plaintext" value="">
            </div>
            <div class="col-md form-group"><label for="input_weight"><?php echo(_("Weight Category")); ?></label>
              <select name="weight" id="input_weight" class="form-control" <?php echo($attributesInputWeight)?>>
                <option value="" selected disabled><?php echo(_("Select year of birth and sex first.")); ?></option>
              </select>
            </div>
          </div>
            </div>
		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script>
		var category = "";
    function updateWeights(e) {
      var newcategory = "";
      var weights = null;
      var sex = $(\'input[name=sex]:checked\').val();
      var yearOfBirth = $(\'input[name=yearOfBirth]\').val();
      var age = yearOfTournament - yearOfBirth;
      if (sex === "m") {
        var sexCategories = categories.male;
        //    $("#test-output").text(function(i,text){return text + "♂"});
      }
      if (sex === "f") {
        var sexCategories = categories.female;
        //    $("#test-output").text(function(i,text){return text + "♀"});
      }
      //XXX This code assumes, that the categories within categories.male and categories.female are in ascending order by their max age.
      for (var cat in sexCategories) {
        if (age <= cat) {
          //      $("#test-output").text(function(i,text){return text + cat});
          var newcategory = sexCategories[cat].agetext;
          weights = sexCategories[cat].weights;
          var weightTexts = sexCategories[cat].weightTexts;
          break;
        }
      }
      if (newcategory === category) {
        //    $("#test-output").text(function(i,text){return text + "."});
      } else {
        //    $("#test-output").text(function(i,text){return text + "#"});
        category = newcategory;
        $("#labelAgeCat").val(category).text(category);
        $("#input_weight").empty();
        if (weights === null) {
          $("<option/>").text("Select year of birth and sex first.").appendTo("#input_weight");
        } else {
          $("<option/>").val("").text("Please choose a weight category.").appendTo("#input_weight");
          weightTexts.forEach(function (item, index) {
            $("<option/>").val(weights[index]).text(item).appendTo("#input_weight");
          });
        }
      }
    }

    $.fn.mouseheld = function (step) {
      var nextTime = 0;
      var delay = 160;
      var running = true;

      function runStep(time) {
        if (running)
          requestAnimationFrame(runStep);
        if (time < nextTime)
          return;
        nextTime = time + delay;

        step();
      }
      this.mousedown(function () {
        running = true;
        nextTime = 0;
        requestAnimationFrame(runStep);
      }).bind(\'mouseup mouseleave\', function () {
        running = false;
      });
    };

    $("#btn_inc_yearOfBirth").mouseheld(function (e) {
      var elem = $("#input_yearOfBirth");
      if (elem.attr(\'max\') > elem.val()) {
        elem.val(+elem.val() + 1);
        elem.change();
      }
    });

    $("#btn_dec_yearOfBirth").mouseheld(function (e) {
      var elem = $("#input_yearOfBirth");
      if (elem.attr(\'min\') < elem.val()) {
        elem.val(+elem.val() - 1);
        elem.change();
      }
    });

    $("input[name=\'sex\']").change(updateWeights);
    $("input[name=\'yearOfBirth\']").change(updateWeights);
    updateWeights();

    function toggleField(hideObj, showObj) {
      hideObj.disabled = true;
      hideObj.style.display = \'none\';
      showObj.disabled = false;
      showObj.style.display = \'inline\';
      showObj.focus();
    }
		</script>
		';
	
	return $result;

}

add_shortcode( 'judoshiai_competitors_full_list', 'render_judoshiai_competitors_full_list');
function render_judoshiai_competitors_full_list($atts =[], $content = null, $tag = '' ){ 

	$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	
	$wporg_atts = shortcode_atts(
        array(
            'title' => 'WordPress.org',
			'coach_id' => 'ALL',
        ), $atts, $tag
    );
	
	if ($wporg_atts['coach_id']	!= 'ALL' && $wporg_atts['coach_id']	!= 'USER') return 'Wrong value for coach_id: use (ALL or USER).';
	
	$judoShiaiTemplateFile = get_option('judoshiai_option_name');
	$dbconn = dbConnectionSqlite(plugin_dir_path(__FILE__) .'/databases/'. $judoShiaiTemplateFile);
	
	$user_id = get_current_user_id();
	if ($wporg_atts['coach_id']	!= 'ALL' && $user_id != 0) $wporg_atts['coach_id'] = $user_id;
	else if ($wporg_atts['coach_id'] != 'ALL' && $user_id == 0) return 'You are currently not logged in.';
		
	$result=''; 	
	if ($dbconn){
		$array_competitors = sqlite_getCompetitors($dbconn,$wporg_atts['coach_id']);
	}
	else {
		$result = 'Connection to database failed!';
		return $result;
	}

	if ($array_competitors)
	{
		//print_r($array_competitors);
		$result = '<table><thead><tr><th>'.$wporg_atts['title'].'</th></tr><tr><th>Firstname</th><th>Lastname</th><th>Year of Birth</th><th>Category</th><th>Club</th></tr></thead>';
		foreach ($array_competitors as $competitor)
		{
			$result = $result.'<tbody><tr>';
			$result = $result.'<td>'.$competitor->firstName.'</td><td>'.$competitor->lastName.'</td><td>'.$competitor->yearOfBirth.'</td><td>'.$competitor->category.'</td><td>'.$competitor->club.'</td>';
			$result = $result.'</tr>';

		}
		$result = $result.'</table></tbody>';
	}
	else $result = 'No competitor has been registered for you [id:'.$user_id.']';
	return $result; 
}


function judoshiai_plugin_register_settings() {
	/*	
	$minYearOfBirth = 1980;
	$maxYearOfBirth = 2016;
	$yearOfTournament = 2022;
	*/
	
	add_option('judoshiai_option_name','default.shi');
	register_setting('judoshiai_plugin_options', 'judoshiai_option_name');
	
	add_option('judoshiai_option_minYearOfBirth','1980');
	register_setting('judoshiai_plugin_options', 'judoshiai_option_minYearOfBirth');
	
	add_option('judoshiai_option_maxYearOfBirth','2016');
	register_setting('judoshiai_plugin_options', 'judoshiai_option_maxYearOfBirth');
}

add_action('admin_init', 'judoshiai_plugin_register_settings');

?>
