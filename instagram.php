<?php

class Instagram {
    static function parseUrl($url) {
        if($result = json_decode(file_get_contents("http://api.instagram.com/oembed?url=".$url)))
            return $result->url;
        return false;
    }
}
