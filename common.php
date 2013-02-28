<?php
require_once('AppInfo.php');

if (file_exists('../development')) $dev = true;

// Enforce https on production
//if (!$dev && substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
//    header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//    exit();
//}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');

if (!$dev)
    require_once('sdk/src/facebook.php');
else
    require_once('../sdk/src/facebook.php');

$facebook = new Facebook(array(
    'appId'  => AppInfo::appID(),
    'secret' => AppInfo::appSecret(),
    // 'sharedSession' => true,
    // 'trustForwarded' => true,
));

$user_id = $facebook->getUser();

// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());
$app_name = idx($app_info, 'name', '');

function is_login() {
    global $user_id;
    return $user_id;
}

?>
