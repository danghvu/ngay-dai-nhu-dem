<?php 
/**
 * extract the main verb of a phrase 
 */

$keyverbs = array("sent","gui","done");
function get_verb($term)
{
    global $keyverbs;
    $term = strtolower($term);
    for($i = 0 ; $i < sizeof($keyverbs); $i++){
        if(strpos($term, $keyverbs[$i]) !== false ){
            return $keyverbs[$i];
        }
    }
    //return $term;
    return "";
}

/**
 * evaluate a verb if it's negative or positive 
 * negative: pending request, new request, .. 
 * positive: sent, done, gui...
 */
function eval_verb($verb)
{

}



?>
