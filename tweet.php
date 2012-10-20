<?php
class Tweet {
    public $text;
    public $imageUrl;
    public $profileUsername;
    public $profileImageUrl;
    public $tweetid;

    function __construct($tw) {
        $this->text = $tw->text;
        $this->imageUrl = $tw->instagram[0];
        $this->profileUsername = $tw->from_user;
        $this->profileImageUrl = $tw->profile_image_url;
        $this->tweetid = $tw->id_str;
        $this->store();
    }

    // Save tweets to database
    private function store() {
        $query = "INSERT INTO tweets (text, imageUrl, profileUsername, profileImageUrl, tweetid) VALUES(:text, :img, :user, :userimg, :twid)";
        Database::executeNonQuery($query, array(
            "text" => $this->text, 
            "img" => $this->imageUrl, 
            "user" => $this->profileUsername, 
            "userimg" => $this->profileImageUrl,
            "twid" => $this->tweetid,
        ));
    }

    //Get all stored tweets
    public static function getAll() {
        return Database::executeQuery("SELECT * FROM tweets ORDER BY id DESC");
    }

    // Get a limited number of tweets
    public static function getRecent($count = 20, $last = 0) {
        $params = array("count" => (int)$count, "last" => (int)$last);
        $limit = "LIMIT :count";
        if($last != 0) {
            $limit = "";
            unset($params['count']);
        }
        return Database::executeQuery("SELECT id, imageUrl, profileUsername, profileImageUrl, text FROM tweets WHERE id > :last ORDER BY id DESC $limit", $params);
    }

    // Get tweet by ID
    public static function getTweet($id) {
        return Database::executeQuery("SELECT * FROM tweets WHERE id=:id",
            array("id" => (int)$id));
    }
}
