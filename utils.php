<?php

/**
 * @return the value at $index in $array or $default if $index is not set.
 */
function idx(array $array, $key, $default = null) {
  return array_key_exists($key, $array) ? $array[$key] : $default;
}

function he($str) {
  return htmlentities($str, ENT_QUOTES, "UTF-8");
}

function make_link_shorten($str) {
    $str = $str[0];
    return "<a href=\"$str\" target=\"_blank\">".substr($str,0,80)."...</a>";
}

function linkify($str){
    return preg_replace_callback("#[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]#i", 'make_link_shorten', $str);
}

function findemail($str) {
    preg_match("#[^\s]+@[^\s]+[[:alnum:]]#i", $str, $m); // simple catch email for now
    return ($m!=NULL)? $m[0] : NULL;
}

function deny_access() {
    header('HTTP/1.0 403 Forbidden');
    exit();
}

function clear_cookie() {
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
        }
    }
    session_destroy();
}

?>
