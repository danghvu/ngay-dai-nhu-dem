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
    return $m[0];
}

?>

