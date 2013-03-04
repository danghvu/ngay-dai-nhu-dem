<?php
header("Content-Type: application/json");
ini_set('display_errors','0');
require_once('common.php');

/*
if (!is_authorized()) { 
    error_log('not yet authorized');
    deny_access();
}
*/

if (token_expired()) {
    if (isset($_POST['token'])) {
        $facebook->setAccessToken($_POST['token']);
    }
}

// try again
if (token_expired()) {
    deny_access();
}

require_once('loadpaper.php');
$loadpaper = new LoadPaperAPI($facebook);
$result_obj = $loadpaper->get_post_no_comment();

    foreach ($result_obj as &$req) {
        $msg = $req['message'];
        $email = findemail($msg);
        if($email)
            $req['email'] = $email; 
        #remove comments
        if(array_key_exists('comments',$req)){
            unset($req['comments']);
        }

        $req['owner']['name'] = he($req['owner']['name']);
        $req['message'] = linkify(he($msg));
    } // end foreach

    echo json_encode($result_obj);
?>
