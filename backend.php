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

// Run the query and retrieve relevant tweets. Quit if the query fails.
if(!$t->run()) {
    header(':', true, 500);
    die(json_encode("Query error"));
}

// Store our tweets in the database
foreach($t->tweets as $tweet) {
    new Tweet($tweet);
}

// Update the last tweet ID
    $t->saveLastId();

// Finish (unless debug is on)
if(!isset($_GET['debug'])) {
    header(':', true, 200);
    die(json_encode("Ok"));
}

// Show the tweets if $_GET['debug'] is set
ini_set("html_errors", 1);
die(var_dump($t));
