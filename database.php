<?php
class Database {
    const MULTIPLE = 0;
    const SINGLE = 1;
    const SCALAR = 2;

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

    static function executeQuery($query, $parameters = array(), $flags = 0) {
        $dbh = self::getConnection();
        $sth = $dbh->prepare($query);

        if(!empty($parameters)) {
            foreach($parameters as $k => $v) {
                $datatype = 2;

                if(is_int($v)) {
                    $datatype = PDO::PARAM_INT;
                }
                else if(is_string($v))
                    $datatype = PDO::PARAM_STR;

                $sth->bindParam(':'.$k, $parameters[$k], $datatype);
            }
        }

        $sth->execute();

        if($flags === 2) 
            $sth->setFetchMode(PDO::FETCH_NUM);
        else
            $sth->setFetchMode(PDO::FETCH_ASSOC);


        if($flags === 0)
            return $sth->fetchAll();

        @$result = $sth->fetchAll();

        if(count($result) == 0)
            return null;

        if($flags === 2) 
            return $result[0][0];

        return $result[0];

    }
}
