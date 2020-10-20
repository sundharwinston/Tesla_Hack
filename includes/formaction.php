<?php
require_once '../config/config.php';

$return_res = array();
$return_res['status'] = 'Not an authorized request!';
if (!isset($_POST)){
	$return_res['error'] = 'Not a post request!';
	submitview($return_res);
}
if (!isset($_SERVER['HTTP_REFERER'])) {
	$return_res['error'] = 'HTTP_REFERER ERROR!';
	submitview($return_res);
}
if (!isset($_POST["act___s"])) {
	$return_res['error'] = 'No action found';
	submitview($return_res);
}
if (!isset($_POST['token___s'])) {
	$return_res['error'] = 'No token found';
	submitview($return_res);
}

$http_refer = $_SERVER['HTTP_REFERER'];
if (stripos($http_refer, ABS_URL) === false) {
	$return_res['error'] = 'HTTP_REFERER Error';
	submitview($return_res);
}

$session_id = trim($_POST['token___s']);
session_id($session_id);
if (!isset($_SESSION['api___k'])) {
	$return_res['error'] = 'No submitkey found';
	submitview($return_res);
}
if ($_SESSION['api___k'] !== '0gcFYhruyYiU4lm5Y231AFSLmNulUKDb') {
	$return_res['error'] = 'No match submitkey found';
	submitview($return_res);
}
$return_res['action'] = $action = $_POST['act___s'];
switch ($action) {
	case 'dac___act': demoaccount();
		break;
	case 'lac___act': liveaccount();
		break;
	case 'nac___act': newsletter();
		break;
	default: noaction();
		break;
}
submitview($return_res);

function noaction() {
    global $return_res;
    $return_res['status'] = 'No such action!';
}
function demoaccount(){
	ob_start();
	global $return_res;
	$demoaccount = new DemoAccount();
	$demoaccountResult = $demoaccount->Register($skipcaptcha=0);
	if ($demoaccountResult === true) {
		$return_res['status'] = "success";
		$return_res['hash'] = md5(date("u"));
	}elseif($demoaccountResult === false){
		$return_res['status'] = $demoaccount->Message;
	}
}
function liveaccount(){
	ob_start();
	global $return_res;
	$liveaccount = new LiveAccount();
	$liveaccountResult = $liveaccount->Register();
	if ($liveaccountResult === true) {
		$return_res['status'] = "success";
		$return_res['hash'] = md5(date("u"));
	}elseif($liveaccountResult === false){
		$return_res['status'] = $liveaccount->Message;
	}
}

function newsletter(){
	ob_start();
	global $return_res;
	$newsletter = new NewsletterSubscription();
	$newsletterResult = $newsletter->Register();
	if ($newsletterResult === true) {
		$return_res['status'] = "success";
		$return_res['hash'] = md5(date("u"));
	}elseif($newsletterResult === false){
		$return_res['status'] = $newsletter->Message;
                        
	}
}
function submitview($result){
	ob_end_clean();
	header('Content-Type: application/json');
	echo json_encode($result);
	exit;
}