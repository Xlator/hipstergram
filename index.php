<?php 
function __autoload($class) {
   require sprintf("%s.php", $class);
}

if(substr($_SERVER['REDIRECT_URL'], -4) == "json")
    header("Content-Type: application/json");

/* ini_set("html_errors", 1); */
/* var_dump($_SERVER); */
/* die(); */

switch(substr($_SERVER['REDIRECT_URL'], 1)) { 
    case "all.json":
        print json_encode(Tweet::getAll());
        break;

    case "recent.json":
        if(isset($_GET['last']))
            $tweets = Tweet::getRecent(0, $_GET['last']);
        else
            $tweets = Tweet::getRecent(20);
        print json_encode($tweets);
        break;
    case "tweet.json":
        if(isset($_GET['tweet'])) {
            print json_encode(Tweet::getTweet($_GET['tweet']));
        }
        break;
    case "query":
        require "backend.php";
        break;

    default:
        include "pics.html";
        break;
}

?>
