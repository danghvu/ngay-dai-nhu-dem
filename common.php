<?php
require_once('AppInfo.php');

if (AppInfo::appID() === '212124665595942') $dev = true;

// Enforce https on production
//if (!$dev && substr(AppInfo::getUrl(), 0, 8) != 'https://' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
//    header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//    exit();
//}

// This provides access to helper functions defined in 'utils.php'
require_once('utils.php');
require_once('sdk/src/facebook.php');

if (!$dev) {
    $dropBoxKey = 'e2pip5zpoxx1bo2';
}
else {
    $dropBoxKey = 'd2qg6iuy7tpm4ni';
}

$facebook = new Facebook(array(
    'appId'  => AppInfo::appID(),
    'secret' => AppInfo::appSecret(),
    'sharedSession' => true,
    'trustForwarded' => true,
));

$perm_list = array( 'publish_actions', 'read_stream' );

$user_id = $facebook->getUser();
$accessToken = $facebook->getAccessToken();
// Fetch the basic info of the app that they are using
$app_info = $facebook->api('/'. AppInfo::appID());
$app_name = idx($app_info, 'name', '');

function is_authorized() {
    global $user_id;
    return $user_id;
}

function token_expired() {
    global $basic, $facebook, $perm_list;
    try {
        $basic = $facebook->api('/me');
    } catch (FacebookApiException $e) {
        return true;
    }
    
    // also check permissions
    $permissions = $facebook->api("/me/permissions");
    foreach ($perm_list as &$p) {
        if (!array_key_exists($p, $permissions['data'][0])) {
            return true;
        }
    }

    return false;
}

?>
