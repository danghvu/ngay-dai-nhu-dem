<?php
header("Content-Type: application/json");
ini_set('display_errors','0');
require_once('common.php');

if (!is_authorized()) { 
    deny_access();
}

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

        /*

            <li><div class="imgmsg"><a href="https://www.facebook.com/<?php echo $id ?>" target="_blank"><img src="<?php echo $pic; ?>"/></a></div>
            <div class="outermsg">
            <span class="title"><a href="<?php echo $perml; ?>" target="_blank"><?php echo $time; ?> - <?php echo he(idx($user, 'name')); ?></a></span>
            <span class="message"><?php echo linkify(he($msg)); ?></span>
            <span class="tools">
                <?php if ($email) { ?>
                <a href="mailto:<?php echo $email; ?>">Gửi qua Email</a>
                <?php } ?>
                <a href="https://www.facebook.com/<?php echo $id ?>" target="_blank">Gửi tin nhắn riêng</a>
                <?php if (true || $can_msg) { ?>
                <a href="#" onclick="sendmsg('<?php echo $id; ?>')">Gửi link trực tiếp</a>
                <?php } ?>
            </span>
            </div>
            </li>
    */
    } // end foreach

    echo json_encode($result_obj);
?>
