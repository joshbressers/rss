<?php

$db = DB::connect($dsn);
if (DB::isError($db))
    die("Can't connect to database");

session_start();

function csrf_validate($input_token) {
    $stored_token = csrf_get_token();
    if ($input_token == $stored_token) {
        return true;
    } else {
        return false;
    }
}

function csrf_get_token() {
    $the_token = '';

    if (!isset($_SESSION['csrf_token'])) {
        # Generate a new token
        $_SESSION['csrf_token'] = hash("sha512",mt_rand(0,mt_getrandmax()));
    }

    $the_token = $_SESSION['csrf_token'];

    # Make sure we have a proper token
    if ($the_token == '') {
        throw new Exception("Bad CSRF token");
    }

    return $the_token;
}

function run_query($query, $query_vars) {
    global $db;

    $return_data = array();

    $sth = $db->prepare($query);
    if ($query_vars == NULL) {
        $result =& $db->execute($sth);
    } else {
        $result =& $db->execute($sth, $query_vars);
    }

    if (DB::isError($result))
        #die($query . " :Error: " . $result->getMessage());
        throw new Exception($query . " :Error: " . $result->getMessage());

    if ($result === DB_OK) {
        return array();
    }

    while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
        array_push($return_data, $row);
    }

    return $return_data;
}

?>
