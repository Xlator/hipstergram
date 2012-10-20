<?php

class TwitterQuery {
    public $last_tweet_id;
    public $request;
    public $tweets;

    function __construct($params) {
        $lastid_query = "SELECT value FROM settings WHERE key='last_tweet_id'";
        $params['since_id'] = Database::executeQuery($lastid_query, array(), Database::SCALAR);
        $this->request = http_build_query($params);
    }    
    
    function run() {
        $url = sprintf("http://search.twitter.com/search.json?%s", $this->request);
        if($result = file_get_contents($url)) {
            $result = json_decode($result);
            $this->last_tweet_id = $result->max_id_str;
            $this->tweets = $result->results;
            $this->filterTweets(); 
            return $this;
        }

        return false;
    }

    private function filterTweets() {
        foreach($this->tweets as $key => $tweet) {
            $urls = $this->checkUrl($tweet);
            if(!empty($urls)) { 
               $this->tweets[$key]->instagram = array_map("Instagram::parseUrl", $urls);
            }
            else
                unset($this->tweets[$key]);    
        }
    }

    private function checkUrl($tweet) {
        $urls = $tweet->entities->urls;
        $valid = array();
        foreach($urls as $url) {
            if(preg_match('#^http://instagr[am\.com|\.am]#', $url->expanded_url))
                $valid[] = $url->expanded_url;
        }
        return $valid;
    }

    /* private function */

    public function saveLastId() {
        Database::executeNonQuery("UPDATE settings SET value=:id WHERE key='last_tweet_id'", array("id" => $this->last_tweet_id));
    }
}
