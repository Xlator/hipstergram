<?php 

class Tweet {
    public $text;
    public $imageUrl;
    public $profileUsername;
    public $profileImageUrl;
    function __construct($tw, $in) {
        $this->text = $tw->text;
        $this->imageUrl = $in->url;
        $this->profileUsername = $tw->from_user;
    }
}

$tweet_objs = array();

$query = urlencode("-RT #food filter:links");
$tweets = json_decode(file_get_contents("http://search.twitter.com/search.json?q=".$query."&include_entities=true&rpp=10"));

foreach($tweets->results as $tweet) {
    foreach($tweet->entities->urls as $url)
        if(strstr($url->expanded_url, "instagr.am")) {
            $insta = json_decode(file_get_contents("http://api.instagram.com/oembed?url=".urlencode($url->expanded_url."?size=t")));
            $tweet_objs[] = new Tweet($tweet, $insta);
        }
}

header("Content-Type: application/json");
print json_encode($tweet_objs);
