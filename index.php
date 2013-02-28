<?php

require_once('common.php');

if (is_login()) {
    try {
        // Fetch the viewer's basic information
        $basic = $facebook->api('/me');
    } catch (FacebookApiException $e) {
        // If the call fails we check if we still have a user. The user will be
        // cleared if the error is because of an invalid accesstoken
        if (!$facebook->getUser()) {
            // unset all cookie before relogin
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                }
            }
            header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
            exit();
        }
    }
    // Fetch the list of request

    $group_id = '188053074599163'; // load_paper_id
    $queries = '{
        "app_using":"SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1",
    }';

    $rs_obj_multi = $facebook->api(
        array( 
            'method'=>'fql.multiquery',
            'queries'=>$queries,
        )
    );

    foreach( $rs_obj_multi as &$rs ) {
        $data = $rs['fql_result_set'];
        if ($rs['name'] === 'app_using')
            $app_using_friends = $data;
    }

}
?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />

    <title><?php echo he($app_name); ?></title>
    <link rel="stylesheet" href="stylesheets/screen.css" media="Screen" type="text/css" />
    <link rel="stylesheet" href="stylesheets/mobile.css" media="handheld, only screen and (max-width: 480px), only screen and (max-device-width: 480px)" type="text/css" />

    <!--[if IEMobile]>
    <link rel="stylesheet" href="mobile.css" media="screen" type="text/css"  />
    <![endif]-->

    <!-- These are Open Graph tags.  They add meta data to your  -->
    <!-- site that facebook uses when your content is shared     -->
    <!-- over facebook.  You should fill these tags in with      -->
    <!-- your data.  To learn more about Open Graph, visit       -->
    <!-- 'https://developers.facebook.com/docs/opengraph/'       -->
    <meta property="og:title" content="<?php echo he($app_name); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
    <meta property="og:image" content="<?php echo AppInfo::getUrl('/logo.png'); ?>" />
    <meta property="og:site_name" content="<?php echo he($app_name); ?>" />
    <meta property="og:description" content="Công cụ hỗ trợ tảo báo" />
    <meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />

    <script type="text/javascript" src="javascript/jquery-1.7.1.min.js"></script>

<script type="text/javascript">
function logResponse(response) {
    if (console && console.log) {
        console.log('The response was', response);
    }
}
</script>

    <!--[if IE]>
<script type="text/javascript">
var tags = ['header', 'section'];
while(tags.length)
    document.createElement(tags.pop());
</script>
    <![endif]-->
  </head>
  <body>
    <div id="fb-root"></div>
    <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '<?php echo AppInfo::appID(); ?>', // App ID
                channelUrl : '//<?php echo $_SERVER["HTTP_HOST"]; ?>/channel.html', // Channel File
                status     : true, // check login status
                cookie     : true, // enable cookies to allow the server to access the session
                xfbml      : true // parse XFBML
            });

            // Listen to the auth.login which will be called when the user logs in
            // using the Login button
            FB.Event.subscribe('auth.login', function(response) {
                // We want to reload the page now so PHP can read the cookie that the
                // Javascript SDK sat. But we don't want to use
                // window.location.reload() because if this is in a canvas there was a
                // post made to this page and a reload will trigger a message to the
                // user asking if they want to send data again.
                window.location = window.location;
            });

            load_no_comment();

            FB.Canvas.setAutoGrow();
        };

        // Load the SDK Asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <header class="clearfix">
      <?php if (isset($basic)) { ?>
      <p id="picture" style="background-image: url(https://graph.facebook.com/<?php echo he($user_id); ?>/picture?type=normal)"></p>

      <div>
        <h1>Chào <strong><?php echo he(idx($basic, 'name')); ?></strong></h1>
        <p class="tagline">
          <a href="<?php echo he(idx($app_info, 'link'));?>" target="_top">Ứng dụng <?php echo he($app_name); ?></a><br />
          <a href="https://www.facebook.com/groups/loadpapersteam/">Nhóm tải báo</a>
        </p>
      </div>

      <?php } else { ?>

      <div>
        <h1>Welcome</h1>
        <div class="fb-login-button" data-scope="read_stream"></div>
      </div>

      <?php } ?>
    </header>

<?php
if (is_login()) {
?>
    <section class="clearfix" id="samples">
        <div class="search">
            <h3>Tìm kiếm:</h3>
            <form action='https://www.facebook.com/groups/loadpapersteam/search/?' method='GET' target='_blank'>
            <input type='text' name='query' size=80 placeholder='nhập thông tin cần tìm'>
            <button type='submit'>Tìm</button>
            </form>
        </div>
    </section>

    <section class="clearfix" id="samples">
        <h3><a href="#" title="click to reload" onclick='load_no_comment()'>Yêu cầu chưa có trả lời</a></h3>
        <div class="list" id="request_no_comment">
        </div>
    </section>

    <section id="samples" class="clearfix">
      <h3>Bạn bè sử dụng ứng dụng này </h3>
      <div class="list">
        <ul class="friends">
<?php
    foreach ($app_using_friends as $auf) {
        // Extract the pieces of info we need from the requests above
        $id = idx($auf, 'uid');
        $name = idx($auf, 'name');
?>
          <li>
            <a href="https://www.facebook.com/<?php echo he($id); ?>" target="_top">
              <img src="https://graph.facebook.com/<?php echo he($id) ?>/picture?type=square" alt="<?php echo he($name); ?>">
              <?php echo he($name); ?>
            </a>
          </li>
<?php
    } // end foreach
?>
        </ul>
      </div>
    </section>

<?php
} // end if (userid)
?>

<script language='javascript'>
function load_no_comment() {
    $('#request_no_comment').html("Đang tải ...");

    FB.getLoginStatus(function (response) {
        if (response.authResponse) {
            var token = response.authResponse.accessToken;
            var obj = $.get('api.php', {'token':token}, function(data) {
                $('#request_no_comment').html(data);
            });
        }
    });
}
</script>

  </body>
</html>
