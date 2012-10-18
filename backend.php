<?php 

class Database {
    public static function getConnection() {
        // Create the database if it doesn't exist
        if(!file_exists('db/hipstergram.db')) {
            copy('db/hipstergram.db.dist', 'db/hipstergram.db');
            chmod('db/hipstergram.db', 0700);
        }
            $dbh = new PDO("sqlite:db/hipstergram.db");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbh;
    }

    static function executeNonQuery($query, $parameters = array()) {
        $dbh = self::getConnection();
        $result = self::getResult($dbh, $query, $parameters);
        return $result;
    } 

    private static function getResult($dbh, $query, $parameters) {
        $sth = $dbh->prepare($query); 
        $sth->execute($parameters);
        return $dbh->lastInsertId();
    }

    static function executeQuery($query, $parameters = array(), $singlerow = false) {
        $dbh = self::getConnection();
        $sth = $dbh->prepare($query);

        if(!empty($parameters)) {
            foreach($parameters as $k => $v) {
                $datatype = 2;

                if(is_int($v))
                    $datatype = PDO::PARAM_INT;
                else if(is_string($v))
                    $datatype = PDO::PARAM_TR;

                $sth->bindParam(':'.$k, $parameter[$k], $datatype);
            }
        }

        $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC);

        if(!$singlerow)
            return $sth->fetchAll();

        @$result = $sth->fetchAll();

        if(count($result) == 0)
            return false;

        return $result[0];
    }
}

class Tweet {
    public $text;
    public $imageUrl;
    public $profileUsername;
    public $profileImageUrl;
    function __construct($tw, $in) {
        $this->text = $tw->text;
        $this->imageUrl = $in->url;
        $this->profileUsername = $tw->from_user;
        $this->profileImageUrl = $tw->profile_image_url;
        var_dump($this);
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
}

$since_id = Database::executeQuery("SELECT value FROM settings WHERE key='last_tweet_id'", array(), true);
var_dump($since_id['value']);
$params = array(
    "q" => "-RT #food filter:links",
    "include_entities" => "true",
    "rpp" => 100,
    "since_id" => $since_id['value'],
);

$request = http_build_query($params);
$url = "http://search.twitter.com/search.json?".$request;
$tweets = json_decode(file_get_contents("http://search.twitter.com/search.json?".$request));

var_dump(count($tweets->results));

Database::executeNonQuery("UPDATE settings SET value=:maxid WHERE key='last_tweet_id'", array("maxid" => $tweets->max_id_str));

foreach($tweets->results as $tweet) {
    foreach($tweet->entities->urls as $url)
        if(strstr($url->expanded_url, "instagr.am")) {
            $insta = json_decode(file_get_contents("http://api.instagram.com/oembed?url=".urlencode($url->expanded_url."?size=t")));
            $tweet_objs[] = new Tweet($tweet, $insta);
        }
}
