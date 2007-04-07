<?php

$db = DB::connect($dsn);
if (DB::isError($db))
    die("Can't connect to database");

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
        die($query . " :Error: " . $result->getMessage());

    if ($result === DB_OK) {
        return array();
    }

    while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
        array_push($return_data, $row);
    }

    return $return_data;
}

?>
