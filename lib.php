<?php

require_once 'config.php';

function dbConnectionSqlite($dbname){
	if (file_exists($dbname))
		$db = new PDO('sqlite:'.$dbname);
	else $db = false;
	
	return $db;	
}


function sqlite_getInfo($db) {
  $result = new \stdClass;
  /*$result->Competition = $db->exec("SELECT value FROM info WHERE item=='Competition'");
  $result->Date = $db->exec("SELECT value FROM info WHERE item=='Date'");
  $result->Place = $db->exec("SELECT value FROM info WHERE item=='Place'");*/
  if ($isSqlExec = $db->query("SELECT value FROM info WHERE item ='Competition'"))
	$result->Competition = $isSqlExec->fetch();
  else return false;
  if ($isSqlExec = $db->query("SELECT value FROM info WHERE item ='Date'"))
	$result->Date = $isSqlExec->fetch();
  else return false;
  if ($isSqlExec = $db->query("SELECT value FROM info WHERE item ='Place'"))	
	$result->Place = $isSqlExec->fetch();
  else return false;

  return $result;
}

function sqlite_getCategories($db) {
  $male = array();
  $female = array();
  $queryResults = $db->query("SELECT age, agetext, flags, weight, weighttext FROM catdef");
  if ($queryResults){
     while ($row = $queryResults->fetch()) {
		$map = &$female;
		$sex = "f";
		if ($row['flags'] == 1) {
		  $map = &$male;
		  $sex = "m";
		}
		$age = $row['age'];
		if ((isset($map[$age])) && (($cat = $map[$age]) != NULL)) {
		  $cat->weights[] = $row['weight'];
		  $cat->weightTexts[] = $row['weighttext'];
		} else {
		  $cat = new \stdClass;
		  $cat->age = $age;
		  $cat->agetext = $row['agetext'];
		  $cat->sex = $sex;
		  $cat->weights = array($row['weight']);
		  $cat->weightTexts = array($row['weighttext']);
		  $map[$age] = $cat;
		}
	  }
	}
  $result = new \stdClass;
  $result->male = $male;
  $result->female = $female;
  return $result;
}

function sqlite_getCompetitors($db, $coachid) {
  $results = array();
  $indices = array("firstName", "lastName", "yearOfBirth", "sex", "weight", "category", "club", "coachid");
  $sql = "SELECT first, last, birthyear, deleted, weight, regcategory, club, coachid FROM competitors";
  if ($coachid != 'ALL')
		$sql = $sql . " where coachid = '".$coachid."'";
  $queryResults = $db->query($sql);
  while ($row = $queryResults->fetch()) {
    $competitor = new \stdClass;
    foreach ($row as $k => $content) {
      $key = $indices[$k];
      $competitor->$key = $content;
    }
    $results[] = $competitor;
  }
  return $results;
}

function csv_getClubs($fp) {
  $result = array();
  while (($line = fgetcsv($fp)) !== false) {
    $result[] = $line[0];
  }
  return $result;
}

function csv_getCompetitors($fp, $coachid) {
  $result = array();
  $indices = array("firstName", "lastName", "yearOfBirth", "sex", "weight", "category", "club", "coachid");
  while (($line = fgetcsv($fp)) !== false) {
    $competitor = new \stdClass;
    foreach ($line as $k => $content) {
      $key = $indices[$k];
      $competitor->$key = $content;
    }
    if ($competitor->coachid == $coachid) {
      $result[] = $competitor;
    }
  }
  return $result;
}

function csv_addCompetitor($competitor, $fp, $categories, $coachid) {
  global $maxYearOfBirth;
  global $minYearOfBirth;
  $indices = array("firstName", "lastName", "yearOfBirth", "sex", "weight", "category", "club", "coachid");
  $result = new \stdClass;
  if ($competitor->lastName == "" and $competitor->firstName == "") {
    $result->msg = _("Please enter a name!");
  } elseif ($competitor->club == "") {
    $result->msg = _("Please enter a valid club!");
  } elseif ($competitor->sex != "m" and $competitor->sex != "f") {
    $result->msg = _("Please enter the competitors Sex!");
  } elseif ($competitor->yearOfBirth > $maxYearOfBirth or $competitor->yearOfBirth < $minYearOfBirth) {
    $result->msg = sprintf(_("Year of birth must be between %d and %d!"), $minYearOfBirth, $maxYearOfBirth);
  } else {
    $competitor->category = map_category($competitor->sex, $competitor->yearOfBirth, $competitor->weight, $categories);
    $competitor->coachid = $coachid;
    $line = array();
    foreach ($indices as $key) {
      $line[] = $competitor->$key;
      $result->$key = $competitor->$key;
    }
    if (!fputcsv($fp, $line)) {
      $result->msg = _("Not saved due to internal error!");
    }
  }
  return $result;
}

function csv_updateCompetitorsSid($dataCsv, $sid_old, $sid_new) {
  if (!$input = fopen($dataCsv, 'r')) {
    die('could not open existing csv file');
  }
  if (!$output = fopen('tmp.csv', 'w')) {
    die('could not open temporary output file');
  }
  while (($data = fgetcsv($input)) !== FALSE) {
    if ($data[7] == $sid_old) {
      $data[7] = $sid_new;
    }
    fputcsv($output, $data);
  }
  fclose($input);
  fclose($output);
  unlink($dataCsv);
  rename('tmp.csv', $dataCsv);
}

function map_category($sex, $yearOfBirth, $weight, $categories) {
  global $yearOfTournament;
  $category = NULL;
  $age = $yearOfTournament - $yearOfBirth;
  if ($sex == 'm') {
    $sexCategories = $categories->male;
  }
  if ($sex == 'f') {
    $sexCategories = $categories->female;
  }
  //XXX This code assumes, that the categories within categories.male and categories.female are in ascending order by their max age.
  foreach ($sexCategories as $catAge => $cat) {
    if ($age <= $catAge) {
      $category = $cat;
      break;
    }
  }
  //XXX This code assumes, that the weights within the weight array are in ascending order.
  $weightText = NULL;
  foreach ($category->weights as $k => $w) {
    if ($weight <= $w) {
      $weightText = $category->weightTexts[$k];
      break;
    }
  }
  if ($weight > 1000 and $category != NULL and $weightText != NULL) {
    $result = $category->agetext . $weightText;
  } else {
    $result = "";
  }
  return $result;
}

function getCoachId($sid_ok) {
  $result = "";
  if (isset($_SESSION['coachid']) && trim($_SESSION['coachid']) != "") {
    $result = trim($_SESSION['coachid']);
  } elseif ($sid_ok) {
    $result = session_id();
  }
  return $result;
}

?>
