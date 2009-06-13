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

$query = 'SELECT clicks FROM rss WHERE id = ?';
$result = run_query($query, $parent_id);
$clicks = $result[0]['clicks'];

$query = 'UPDATE rss SET clicks = ?, last_click = NOW() WHERE id = ?';
run_query($query, array($clicks + 1, $parent_id));

run_query('COMMIT', NULL);

header("Location: " . $url);

?>
