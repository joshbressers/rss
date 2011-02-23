<?php

include_once "DB.php";
include_once "config.inc";
include_once "funcs.inc";

require_once(MAGPIE_DIR.'rss_fetch.inc');

run_query('START TRANSACTION', NULL);

$story_id = $_GET['id'];
$query = 'SELECT link, rss_parent FROM feeds WHERE id = ?';
$result = run_query($query, $story_id);
$url = $result[0]['link'];
$parent_id = $result[0]['rss_parent'];


try {
    $query = 'UPDATE rss SET clicks = clicks + 1, last_click = NOW() WHERE id = ?';
    run_query($query, array($parent_id));

    run_query('COMMIT', NULL);
} catch (Exception $e) {
    # Do nothing
}

header("Location: " . $url);

?>
