<?php
class Tweet {
    public $text;
    public $imageUrl;
    public $profileUsername;
    public $profileImageUrl;
    function __construct($tw) {
        $this->text = $tw->text;
        $this->imageUrl = $tw->instagram[0];
        $this->profileUsername = $tw->from_user;
        $this->profileImageUrl = $tw->profile_image_url;
        $this->store();
    }

    private function store() {
        $query = "INSERT INTO tweets (text, imageUrl, profileUsername, profileImageUrl) VALUES(:text, :img, :user, :userimg)";
        Database::executeNonQuery($query, array(
            "text" => $this->text, 
            "img" => $this->imageUrl, 
            "user" => $this->profileUsername, 
            "userimg" => $this->profileImageUrl)
        );
    }

    public static function getAll() {
        return Database::executeQuery("SELECT * FROM tweets ORDER BY id DESC");
    }

    public static function getRecent($count = 20, $last = 0) {
        $params = array("count" => (int)$count, "last" => (int)$last);
        $limit = "LIMIT :count";
        if($last != 0) {
            $limit = "";
            unset($params['count']);
        }
        return Database::executeQuery("SELECT * FROM tweets WHERE id > :last ORDER BY id DESC $limit", $params);
    }

    public static function getTweet($id) {
        return Database::executeQuery("SELECT * FROM tweets WHERE id=:id",
            array("id" => (int)$id));
    }
}
