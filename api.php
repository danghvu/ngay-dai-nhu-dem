<?php

require_once('common.php');

if (!is_login()) { 
    header('HTTP/1.0 403 Forbidden');
    exit();
}

try {
    $basic = $facebook->api('/me');
} catch (FacebookApiException $e) {
    if (!isset($_POST['token'])) {
        header('HTTP/1.0 403 Forbidden');
        exit();
    }
    $facebook->setAccessToken($_POST['token']);
}

require_once('loadpaper.php');
$loadpaper = new LoadPaperAPI($facebook);
$result_obj = $loadpaper->get_post_no_comment();

?>

        <ul class="friends">
<?php
    foreach ($result_obj as $req) {
        $msg = idx($req, 'message');

        $time = idx($req, 'created_time');
        $time = date("M d Y h:ia",$time);

        $perml = idx($req, 'permalink');

        $user = idx($req, 'owner');
        $pic = idx($user, 'pic_square');
        $id = idx($user, 'uid');

        $email = findemail($msg);
        $can_msg = idx($user, 'can_message');
?>

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
<?php
    } // end foreach
?>
        </ul>
