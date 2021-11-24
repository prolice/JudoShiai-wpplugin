<?php
require_once 'config.php';
require 'lib.php';

header("Content-Type:application/json");
// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
#$input = file_get_contents('php://input');
$input = json_decode(file_get_contents('php://input'));
session_start();

// create SQL based on HTTP method
switch ($method) {
  case 'GET':
    get($request);
  break;
  case 'PUT':
    put($request,$input);
    break;
  case 'POST':
    post($request,$input);
  break;
  case 'DELETE':
    _delete($request,$input);
  break;
  default:
    methodNotAllowed();
}

function get($request) {
  $table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
  switch ($table) {
    case 'info':
      get_info();
      break;
    case 'categories':
      get_categories();
      break;
    case 'clubs':
      get_clubs();
      break;
    case 'competitors':
      get_competitors();
      break;
    case 'coachid':
      get_coachid();
      break;
    default:
      http_response_code(404);
  }
}

function post($request,$input) {
  $table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
  switch ($table) {
    case 'competitors':
      post_competitors($input);
      break;
    case 'coachid':
      post_coachid($input);
      break;
    default:
      http_response_code(404);
  }
}

function put($request,$input) {
  $table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
  switch ($table) {
    case 'coachid':
      put_coachid($input);
      break;
    default:
      http_response_code(404);
  }
}

function _delete($request,$input) {
  $table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
  switch ($table) {
    case 'coachid':
      delete_coachid();
      break;
    default:
      http_response_code(404);
  }
}

function get_info(){
  global $judoShiaiTemplateFile;
  echo $judoShiaiTemplateFile;
  $db = new SQLite3($judoShiaiTemplateFile);
  echo json_encode(sqlite_getInfo($db));
  $db->close();
}

function get_categories(){
  global $judoShiaiTemplateFile;
  $db = new SQLite3($judoShiaiTemplateFile);
  echo json_encode(sqlite_getCategories($db));
  $db->close();
}

function get_clubs(){
  global $clubsTxt;
  $fp=fopen($clubsTxt, "r") or die("Unable to open file!");
  echo json_encode(csv_getClubs($fp));
  fclose($fp);
}

function get_competitors(){
  global $judoShiaiTemplateFile;
  global $dataCsv;
  // $db = new SQLite3($judoShiaiTemplateFile);
  // if (isset($_SESSION['coachid']) && trim($_SESSION['coachid'])!="" ){
    // echo json_encode(sqlite_getCompetitors(new SQLite3($judoShiaiTemplateFile),trim($_SESSION['coachid'])));
  // } else {
    // echo json_encode(sqlite_getCompetitors(new SQLite3($judoShiaiTemplateFile),session_id()));
  // }
  // $db->close();
  $fp=fopen($dataCsv,'r') or die("Unable to open file!");
  if (isset($_SESSION['coachid']) && trim($_SESSION['coachid'])!="" ){
    echo json_encode(csv_getCompetitors($fp,trim($_SESSION['coachid'])));
  } else {
    echo json_encode(csv_getCompetitors($fp,session_id()));
  }
  fclose($fp);
}

function get_coachid(){
  echo json_encode(getCoachId(false));
}

function post_competitors($input){
  global $judoShiaiTemplateFile;
  global $dataCsv;
  $fp = fopen($dataCsv, "a") or die("Unable to open file!");// $fp is now the file pointer to file $filename
  $db = new SQLite3($judoShiaiTemplateFile);
  $categories=sqlite_getCategories($db);
  $db->close();
  $coachid=getCoachId(true);
  if (!$input){
    http_response_code(400);
    $msg="Input could not be parsed as JSON.";
  } elseif ( is_object($input) ) {
    $result = csv_addCompetitor($input,$fp,$categories,$coachid);
    if ($result->msg) {
      http_response_code(200);
    } else {
      http_response_code(201);
#      http_response_code(301);
    }
  } elseif ( is_array($input) ) {
    foreach( $input as $competitor ){
      csv_addCompetitor($competitor,$fp,$categories,$coachid);
    }
  } else {
    http_response_code(400);
    $msg="This is neither an object nor an array of Objects.";
  }
  if ($fp) {
    fclose($fp); // Close the file
  }
  echo $msg;
  echo json_encode($result);
}

function post_coachid($input){
  $result = new \stdClass;
  global $dataCsv;
  $sid_old=getCoachId(true);
  $sid_new=trim($input);
  $result->new_sid=$sid_new;
  $result->old_sid=$sid_old;
  if ($sid_new=='' || !preg_match('/[A-Za-z0-9]+/',$sid_new)){
    http_response_code(200);
    $result->msg=sprintf( _('"%s" is not a valid Coach Id. A Coach Id should consist of at least two characters out of [A-Za-z0-9]'),$sid_new);
  } else {
    http_response_code(201);
    csv_updateCompetitorsSid($dataCsv, $sid_old,$sid_new);
    $_SESSION["coachid"]=$sid_new;
  }
  echo json_encode($result);
}

function put_coachid($input){
  $result = new \stdClass;
  $sid_new=trim($input);
  $sid_old=getCoachId(true);
  if ($sid_new=='' || !preg_match('/[A-Za-z0-9]+/',$sid_new)){
    http_response_code(200);
    $result->msg=sprintf( _('"%s" is not a valid Coach Id. A Coach Id should consist of at least two characters out of [A-Za-z0-9]'),$sid_new);
  } else {
    $_SESSION["coachid"]=$sid_new;
    $result->new_sid=$sid_new;
    $result->old_sid=$sid_old;
    http_response_code(200);
  }
  echo json_encode($result);
}

function delete_coachid(){
  unset($_SESSION["coachid"]);
  http_response_code(204);
}

function methodNotAllowed(){
  http_response_code(405);
}
?>