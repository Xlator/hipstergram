<?php

if(!function_exists("__autoload")) {
    die("http://".$_SERVER['HTTP_HOST']."/query");
}
$settings = json_decode(file_get_contents("settings.json"));
$query =  "-RT filter:links ";
$query .= str_replace(" ", " OR ", $settings->tags);
$params = array(
    "q" => $query,
    "rpp" => $settings->tweets,
    "include_entities" => "true",
);

$t = new TwitterQuery($params);
if(!$t->run()) {
    /* header("Content-Type: application/json", true, 500); */
    header(':', true, 500);
    die(json_encode("Query error"));
}

foreach($t->tweets as $tweet) {
    new Tweet($tweet);
}

// Update the last tweet ID
if(!isset($_GET['debug'])) {
    $t->saveLastId();
    header(':', true, 200);
    die(json_encode("Ok"));
}

// Show the tweets if $_GET['debug'] is set
ini_set("html_errors", 1);
die(var_dump($t));
