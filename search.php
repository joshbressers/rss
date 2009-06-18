<html>
<head>
    <title>Search News</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<div id="top_links">
<a href="show_feeds.php">Back</a>
<hr>
<?php
#####################  PHP Code ################################

include_once "DB.php";
include_once "config.inc";
include_once "funcs.inc";

# Database connection bits

$db = DB::connect($dsn);
if (DB::isError($db))
    die("Can't connect to database");

run_query('START TRANSACTION', NULL);

if ($_POST['Submit'] == 'Submit') {
    # Show the search results

    if ($_POST['search_text']) {
        # Add a new feed

        $query = 'SELECT id, title, link FROM feeds WHERE title REGEXP ?  ORDER BY id DESC';
        $feeds = run_query($query, array($_POST['search_text']));

        foreach ($feeds as $items) {
            printf("<a href=\"follow_link.php?id=%s\">- %s</a><br>\n",
                $items['id'], $items['title']);
        }
        echo "<hr>\n";
    }

}

run_query('COMMIT', NULL);

#####################  End PHP Code ############################
?>

<form method="post">

    Search:<br>
    <input type=text size=50 name="search_text"><br>


<input type=submit name=Submit value=Submit><br>
</form>
</div>
</html>
