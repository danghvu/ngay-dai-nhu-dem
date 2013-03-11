<?php 
require_once('../parser.php');
assert_options(ASSERT_ACTIVE,1);
assert_options(ASSERT_WARNING, 1);
function fail_handler($file, $line, $code)
{
    //echo "FAILED: $file\n$line\n$code";
}   
assert_options(ASSERT_CALLBACK, 'fail_handler');

/* test case for parser.php */
assert('get_verb("download gium bai bao") == "download"');
assert('get_verb("#2 sent") == "sent"');
assert('get_verb("gui roi do") == "gui"');
?>
