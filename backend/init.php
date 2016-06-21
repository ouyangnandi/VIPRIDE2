<?php

class DatabaseConn {

    private static $con;
    private static $databaseHost = "localhost";
    private static $databaseName = "vipride";
    private static $databaseUsername = "root";
    private static $databasePass = "";

    public static function getConn() {        
        if (!isset($con)) {
            $con = mysqli_connect(DatabaseConn::$databaseHost, DatabaseConn::$databaseUsername, DatabaseConn::$databasePass, DatabaseConn::$databaseName);
            }
        
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            return null;
        } else {
            return $con;
        }
    }

}

function IsNullOrEmptyString($question){
    return (!isset($question) || trim($question)==='');
}

function inject_check($sql_str) { 
    return preg_match('/^(\s)select\s|(\s)insert\s|(\s)and(\s)|(\s)or(\s)|(\s)update(\s)|(\s)delete(\s)|\'|\/\*|\*|(\s)union(\s)|(\s)into(\s)|(\s)load_file(\s)|(\s)outfile(\s)/i', $sql_str);
} 

?>