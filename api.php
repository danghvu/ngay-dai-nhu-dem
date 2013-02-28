<?php

require_once('common.php');

if (!is_login()) { 
    header('HTTP/1.0 403 Forbidden');
    exit();
}

// header('P3P: CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

require_once('loadpaper.php');
try {
    $loadpaper = new LoadPaperAPI($facebook);
    $result_obj = $loadpaper->get_post_no_comment();
} catch (FacebookApiException $e) {
    die($e);
}

echo "ACCESS_TOKEN: ".($facebook->getAccessToken());

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
?>

            <li><div class="imgmsg"><img src="<?php echo $pic; ?>"/><span></span></div><div class="outermsg">
            <span class="title"><a href="<?php echo $perml; ?>" target="_blank"><?php echo $time; ?> - <?php echo he(idx($user, 'name')); ?></a></span>
            <span class="message"><?php echo linkify(he($msg)); ?></span></div>
            </li>
<?php
    } // end foreach
?>
        </ul>
