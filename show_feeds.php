<html>
<head>
<title>Some News</title>
<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>

<?php
#####################  PHP Code ################################
include_once "DB.php";
include_once "config.inc";
include_once "funcs.inc";

require_once(MAGPIE_DIR.'rss_fetch.inc');

$query = 'SELECT name from categories';
$results = run_query($query, NULL);

foreach ($results as $name) {
    $url = $_SERVER['SCRIPT_URI'] . '?category=' . $name['name'];
    printf("<a href=\"%s\">%s</a> ", $url, $name['name']);
}

echo '<table align="center" border="0" width="100%">';
echo '<tr valign="top">';

if ($category = $_GET['category']) {
    $query = "SELECT rss.id, rss.title, rss.link FROM rss
        JOIN categories ON rss.category = categories.id
        WHERE categories.name = ?
        ORDER BY clicks DESC, id";
    $parents = run_query($query, $_GET['category']);

} else {
    # grab the rss feed parents
    $query = "SELECT id, title, link FROM rss ORDER BY clicks DESC, id";
    $parents = run_query($query, NULL);
}

# loop over the feed parents

$cols = 0;

if ($_GET['cols']) {
    $max_cols = $_GET['cols'];
} else {
    $max_cols = 3;
}

$width = intval(100/$max_cols - 3);
foreach ($parents as $news) {

    if ($cols++ < $max_cols) {
        printf("<td width=\"%s%%\">\n", $width);
    } else {
        $cols = 1;
        printf("</tr><tr valign=\"top\"><td width=\"%s%%\">\n", $width);
    }
    echo '<div id="pretty_table">' . "\n";

    # grab the feeds
    $query = "SELECT id, title, link FROM feeds WHERE rss_parent = ? ORDER BY id DESC";
    $feeds = run_query($query, $news['id']);

    echo '<div id="story_header">' . "\n";
    printf("<a href=\"%s\">%s</a><br>\n", $news['link'], $news['title']);
    echo '</div>' . "\n";
    echo '<div id="story_links">' . "\n";

    $count = 0;
    foreach ($feeds as $items) {
        printf("<a href=\"follow_link.php?id=%s\">- %s</a>\n",
            $items['id'], $items['title']);
        if ($count++ >= 9) break;
    }

    echo '</div>' . "\n";
    echo '</div>' . "\n";
    echo '</td>' . "\n";

}

print("</tr></table>\n");

$query = 'SELECT name from categories';
$results = run_query($query, NULL);

foreach ($results as $name) {
    $url = $_SERVER['SCRIPT_URI'] . '?category=' . $name['name'];
    printf("<a href=\"%s\">%s</a> ", $url, $name['name']);
}

#####################  End PHP Code ############################
?>

<div id="top_links">
<a href="manage_feeds.php">Manage Feeds</a>
</div>
</body>
</html>
