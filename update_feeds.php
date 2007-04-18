<?php

include_once "DB.php";
include_once "config.inc";
include_once "funcs.inc";

require_once(MAGPIE_DIR.'rss_fetch.inc');

# Load the rss URLs that need to be updated.
$query = "START TRANSACTION";
$ids = run_query($query, NULL);

$query = "SELECT id, url, title FROM rss WHERE last_update < ( NOW() - 1800 )";
$ids = run_query($query, NULL);

# Loop over the URLs, loading the data for each
foreach ($ids as $url) {

    # Wipe the old entries
    $query = "DELETE FROM feeds WHERE rss_parent = ? AND timestamp < (NOW() - 7200)";
    run_query($query, $url['id']);

    # Read the new RSS data
    $rss = fetch_rss($url['url']);
    foreach ($rss->items as $item) {
        $href = $item['link'];
        $title = $item['title'];
        # Write the new entries into the database
        $query = "INSERT INTO feeds (title, link, rss_parent) VALUES (?, ?, ?)";
        run_query($query, array($title, $href, $url['id']));
    }

    # Mark the URL as being updated
    $query = "UPDATE rss SET last_update = NOW() where id = ?";
    run_query($query, $url['id']);

}

$query = "COMMIT";
run_query($query, NULL);

header("Location: show_feeds.php");

?>
