<?php

require_once('common.php');

if (is_authorized()) {
    if (token_expired()) {
        clear_cookie();
        header('Location: '. AppInfo::getUrl($_SERVER['REQUEST_URI']));
        exit();
    }

    // Fetch the list of request
    //$group_id = '188053074599163'; // load_paper_id
    /*
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
    */
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
    <script type="text/javascript" src="javascript/dust-full-0.3.0.min.js"></script>
    <script type="text/javascript" src="javascript/date.format.js"></script>
<script type="text/javascript" src="https://www.dropbox.com/static/api/1/dropins.js" id="dropboxjs" data-app-key="<?php echo $dropBoxKey; ?>"></script>

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
    
    <header class="clearfix">
      <?php if (isset($basic)) { ?>
      <p id="picture" style="background-image: url(https://graph.facebook.com/<?php echo he($user_id); ?>/picture?type=normal)"></p>

      <div>
        <h1>Chào <strong><?php echo he(idx($basic, 'name')); ?></strong></h1>
        <p class="tagline">
          <a href="<?php echo he(idx($app_info, 'link'));?>" target="_top">Ứng dụng <?php echo he($app_name); ?></a><br />
          <a href="https://www.facebook.com/groups/loadpapersteam/">Nhóm tải báo</a>
          <br/>
          <a href="https://www.facebook.com/dialog/pagetab?app_id=<?php echo AppInfo::appID(); ?>&next=<?php echo AppInfo::appUrl(); ?>">Add App to Page Tab</a>
        </p>
      </div>

      <?php } else { ?>

      <div>
        <h1>Welcome</h1>
        <div class="fb-login-button" data-scope="read_stream,publish_actions" ></div>
      </div>

      <?php } ?>
    </header>

<?php
if (isset($basic)) {
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
      <div class="list" id="friend-use-app">

      </div>
    </section>

<?php
} // end if (userid)
?>

<script language='javascript'>
function initApp(token){
    window.accessToken = token;
    if(accessToken){
        load_no_comment();
        loadAppFriends();
    }else{
        FB.getLoginStatus(function (response) {
            if(response.authResponse){
                window.accessToken = response.authResponse.accessToken;
                load_no_comment();
                loadAppFriends();
            }
        });
    }
}

function load_no_comment() {
    $('#request_no_comment').html("Đang tải ...");
    if(accessToken){
        var obj = $.post('api.php', {'token':accessToken}, function(data) {
            renderRequests({requests:data});
        });
    }
}

function sendmsg(uid, post_id) {
    var getlink = prompt("Nhập link:");
    FB.ui({
        method: 'send',
        to: uid,
        link: getlink,
    });
    if (getlink) {
        show_comment_box(post_id);
        set_comment_box(post_id, getlink); 
    }
}

function dropbox(uid, post_id) {
    var dblink = '';
    Dropbox.choose({
        success: function(files) {
            dblink = files[0].link;
            FB.ui({
                method: 'send',
                to: uid,
                link: dblink,
            });
            show_comment_box(post_id);
            set_comment_box(post_id, dblink);
        },
        cancel:  function() {}
    });
}

function loadAppFriends(){
    //load friends who use app 
    FB.api('/fql',{q:"SELECT uid, name FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND is_app_user = 1"},function(resp){
        if(resp.data){
            //render friends list
            renderFriends({friends:resp.data});
        }
    }) ;  
}


var compiledTemplate;
function renderRequests(context){

    if(!compiledTemplate){
        //compile dustjs template 
        compiledTemplate = dust.compile($('#request-template').html(), "request-list");
        dust.loadSource(compiledTemplate);
    }    

    //format timestamp based on client timezone
    for(req in context.requests){
        try{
            var request = context.requests[req];
            var timestamp = new Date(Number(request.created_time) *1000);        
            request.created_time = timestamp.format( "mmm, dd yyyy h:MM TT");
        }catch(err){
            console.log(err);
        }
    }

    //rendering
    dust.render("request-list", context, function(err, out){
        if(err){
            //handle error here 
        }
        else{
            $('#request_no_comment').html(out);
        }
    });
}

var friendTemplate;
function renderFriends(context){
    if(!friendTemplate){
        friendTemplate = dust.compile($('#friend-template').html(), "friend-list");
        dust.loadSource(friendTemplate);      
    }

    dust.render("friend-list", context, function(err, out){
        $('#friend-use-app').html(out);
    });
}

function show_comment_box(post_id) {
    $("#comment_box_"+post_id).show();
}

function set_comment_box(post_id, value) {
    $("#comment_box_"+post_id+" textarea").val(value);
}

function submit_comment(post_id) {
    var content = $("#comment_box_"+post_id+" textarea").val();
    FB.api('/'+post_id+'/comments', 'post', {message: content}, 
        function(response) {
            if (!response || response.error) {
                alert('Error occured');
            } else {
                $('#list_'+post_id+' .tools').hide();
            }
        }
    );
}

</script>

<!--
--------------------- UI TEMPLATE ---------------------------------
-->
<script type="text/x-template" id="request-template">
<ul class="friends">
{#requests}
    <li id="list_{post_id}"><div class="imgmsg"><a href="https://www.facebook.com/{owner.uid}" target="_blank"><img src="{owner.pic_square|s}"/></a></div>
            <div class="outermsg">
            <span class="title"><a href="{permalink|s}" target="_blank">{created_time} - {owner.name|s}</a></span>
            <span class="message">{message|s}</span>
            <span class="tools">                
                {#email}
                    <a href="mailto:{email|s}">Gửi qua Email</a>                
                {/email}
                <a href="https://www.facebook.com/{owner.uid}" target="_blank">Gửi tin nhắn riêng</a>                
                <a href="#" onclick="sendmsg('{owner.uid}','{post_id}')">Gửi link trực tiếp</a>
                <a href="#" onclick="dropbox('{owner.uid}','{post_id}')">Gửi link dropbox</a>
                <a href="#" onclick="show_comment_box('{post_id}')">Đã gửi ?</a>
                <span class="comment_box" id="comment_box_{post_id}" style="display:none;">
                    <textarea placeholder="Viết trả lời"></textarea>
                    <button onclick="submit_comment('{post_id}')">Để lại lời nhắn hoàn thành</button>
                </span>
            </span>
            </div>
    </li>
{/requests}
</ul>
</script>
<script type="text/x-template" id="friend-template">
<ul class="friends">
{#friends}
  <li>
    <a href="https://www.facebook.com/{uid}" target="_top">
      <img src="https://graph.facebook.com/{uid}/picture?type=square" alt="{name}">
      {name|s}
    </a>
  </li>
{/friends}
</ul>
</script>

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

                // just in case need additional permission, do not reload but force click login button
                // maybe switch to on-demand later
                var perms = ['read_stream', 'publish_actions'];

                FB.api('/me/permissions', function (response) {
                    for (var i=0;i<perms.length;i++){ 
                        if (!response.data[0][perms[i]]) {
                            return;
                        }
                    }
                    window.location = window.location;
                });
            });

            //load_no_comment();
            initApp("<?php echo isset($accessToken)? $accessToken:''; ?>");

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

        $(".dropbox").each(function(){ 
            this.addEventListener("DbxChooserSuccess", function(e) { alert("Here's the chosen file: " + e.files[0].link)}, false);
        });

</script>

  </body>
</html>
