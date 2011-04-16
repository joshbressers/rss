<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!-- 1140px Grid styles for IE -->
<!--[if lte IE 9]><link rel="stylesheet" href="css/ie.css"
type="text/css" media="screen" /><![endif]-->

<!-- The 1140px Grid - http://cssgrid.net/ -->
<link rel="stylesheet" href="css/1140.css" type="text/css" media="screen" />

<!-- Your styles -->
<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen" />

<!--css3-mediaqueries-js - http://code.google.com/p/css3-mediaqueries-js/ - Enables media queries in some unsupported browsers-->
<script type="text/javascript" src="js/css3-mediaqueries.js"></script>

<link rel="stylesheet" type="text/css" href="style.css"/>
<title>Some News</title>
</head>
<body>

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

$col_type = "fourcol";

printf("<div class=\"container\">\n");
printf(" <div class=\"row\">\n");

foreach ($parents as $news) {

    if ($cols++ < $max_cols) {
        printf("  <div class=\"news-block %s\">\n", $col_type);
    } else {
        $cols = 1;
        printf(" </div><div class=\"row\"><div class=\"news-block %s\">\n", $col_type);
    }

    # grab the feeds
    $query = "SELECT id, title, link FROM feeds WHERE rss_parent = ? ORDER BY id DESC LIMIT 9";
    $feeds = run_query($query, $news['id']);

    printf("   <div class=\"news-block-title\">\n");
    printf("     <div class=\"left\"><a href=\"%s\">%s</a></div><div class=\"right\"><a href=\"search.php?feed=%s\">all</a></div>\n",
        $news['link'], $news['title'], $news['id']);
    printf("   </div>\n");
    printf("   <ul class=\"news-block-items\">\n");
    printf("     <div id=\"story_links\">\n");

    $count = 0;
    foreach ($feeds as $items) {
        printf("      <li class=\"news-block-item\"><a href=\"follow_link.php?id=%s\">- %s</a></li>\n",
            $items['id'], $items['title']);
        if ($count++ >= 9) break;
    }
    printf("     </div>\n");
    printf("    </ul>\n");
    printf("  </div>\n");

}

printf("  </div>\n");
printf("</div>\n");
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
</div>
<div id="top_links">
<a href="search.php">Search</a>
<a href="manage_feeds.php">Manage Feeds</a>
</div>
</body>
</html>
