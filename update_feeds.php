<?php

include_once "DB.php";
include_once "config.inc";
include_once "funcs.inc";

require_once(MAGPIE_DIR.'rss_fetch.inc');

if(php_sapi_name() == 'cli' || empty($_SERVER['REMOTE_ADDR'])) {
    echo "shell\n";
} else {
    echo "go away";
    exit(0);
}

#error_reporting(0);

# Load the rss URLs that need to be updated.

$query = "SELECT id, url, title FROM rss WHERE last_update < ( NOW() - 1800 )";
$ids = run_query($query, NULL);

# Loop over the URLs, loading the data for each
foreach ($ids as $url) {

    # Wipe the old entries
#    $query = "DELETE FROM feeds WHERE rss_parent = ? AND timestamp < (NOW() - 14400)";
#    run_query($query, $url['id']);

    #error_log($url['url']);
    # Read the new RSS data
    $rss = fetch_rss($url['url']);
    if (!$rss) continue;
    $rss_items = array_reverse($rss->items);

    $query = "START TRANSACTION";
    $ids = run_query($query, NULL);

    foreach ($rss_items as $item) {
        $href = $item['link'];
        $title = $item['title'];
        # See if the URL already exists
        $query = "SELECT id FROM feeds WHERE (link = ? or title = ?) and rss_parent = ?";
        $result = run_query($query, array($href, $title, $url['id']));
        if (count($result) >= 1) {
            # This URL exists, let's skip it
            continue;
        }
        # Write the new entries into the database
        $query = "INSERT INTO feeds (title, link, rss_parent) VALUES (?, ?, ?)";
        run_query($query, array($title, $href, $url['id']));
    }

    # Mark the URL as being updated
    $query = "UPDATE rss SET last_update = NOW() where id = ?";
    run_query($query, $url['id']);

    $query = "COMMIT";
    run_query($query, NULL);

}


#header("Location: show_feeds.php");

?>
