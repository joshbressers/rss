<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css"/>
<title>Some News</title>
</head>
<body>

<script>
if (window.innerWidth < 1000 && window.location.href.indexOf("cols=") == -1) {
    if (window.location.href.indexOf("?") != -1) {
        window.location=window.location + "&cols=1";
    } else {
        window.location=window.location + "?cols=1";
    }
}
</script>

<?php
#####################  PHP Code ################################
include_once "DB.php";
include_once "config.inc";
include_once "funcs.inc";

require_once(MAGPIE_DIR.'rss_fetch.inc');

date_default_timezone_set("EST");
if (date("m-d") == "04-01") {
    echo "Don't even bother reading the news today.";
    echo "</html>";
    exit(0);
}

run_query('START TRANSACTION', NULL);

$query = 'SELECT name from categories';
$results = run_query($query, NULL);

printf("<div id='header'>\n");
foreach ($results as $name) {
    $url = $_SERVER['SCRIPT_URI'] . '?category=' . $name['name'];
    if ($_GET['cols']) {
        printf("<a href=\"%s&cols=%d\">%s</a> ", $url, $_GET['cols'],
            $name['name']);
    } else {
        printf("<a href=\"%s\">%s</a>\n", $url, $name['name']);
    }
}
printf("</div>\n");

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

echo '<table align="center" border="0" width="100%">' . "\n";
echo '<tr valign="top">' . "\n";

foreach ($parents as $news) {

    if ($cols++ < $max_cols) {
        echo '<td width="30%">'. "\n";
    } else {
        $cols = 1;
        echo '</tr><tr valign="top"><td width="30\%">' . "\n";
    }

    echo '<div id="pretty_table">' . "\n";

    # grab the feeds
    $query = "SELECT id, title, link FROM feeds WHERE rss_parent = ? ORDER BY id DESC LIMIT 9";
    $feeds = run_query($query, $news['id']);

    echo '<div id="story_header">' . "\n";
    printf("  <div class=\"left\"><a href=\"%s\">%s</a></div><div
class=\"right\"><a href=\"search.php?feed=%s\">all</a></div><br>\n",
        $news['link'], strip_tags($news['title']), $news['id']);
    echo '</div>' . "\n";
    echo '<div id="story_links">' . "\n";

    $count = 0;
    foreach ($feeds as $items) {
        printf("<a href=\"follow_link.php?id=%s\">- %s</a>\n",
            $items['id'], strip_tags($items['title']));
        if ($count++ >= 9) break;
    }

    echo '</div>' . "\n";
    echo '</div>' . "\n";
    echo '</td>' . "\n";
}
echo '</tr>' . "\n";
echo '</table>' . "\n";

printf("<div id='footer'><br>\n");

$query = 'SELECT name from categories';
$results = run_query($query, NULL);

foreach ($results as $name) {
    $url = $_SERVER['SCRIPT_URI'] . '?category=' . $name['name'];
    printf("<a href=\"%s\">%s</a> ", $url, $name['name']);
}

run_query('COMMIT', NULL);

#####################  End PHP Code ############################
?>
<div id="top_links">
<a href="search.php">Search</a>
<a href="manage_feeds.php">Manage Feeds</a>
</div>
</body>
</html>
