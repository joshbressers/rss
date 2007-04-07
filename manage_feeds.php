<html>

<body>
<a href="show_feeds.php">Back</a><hr>
<?php
#####################  PHP Code ################################

include_once "DB.php";
include_once "config.inc";
include_once "funcs.inc";

require_once(MAGPIE_DIR.'rss_fetch.inc');

# Database connection bits

$db = DB::connect($dsn);
if (DB::isError($db))
    die("Can't connect to database");

if ($_POST['Submit'] == 'Submit') {
    if (count($_POST['delete']) > 0) {
        # Delete some entries

        $query = 'DELETE FROM rss WHERE id = ?';
        $query2 = 'DELETE FROM feeds WHERE rss_parent = ?';

        foreach ($_POST['delete'] as $delete_me) {
            run_query($query2, $delete_me);
            run_query($query, $delete_me);
        }
    }

    if ($_POST['new_feed']) {
        # Add a new feed
        $url = $_POST['new_feed'];
        $rss = fetch_rss($url);
        $title = $rss->channel['title'];

        $query = 'INSERT INTO rss (url, title) VALUES (?, ?)';
        run_query($query, array($url, $title));

    }
}


#####################  End PHP Code ############################
?>

<form method="post">

    Add Feed:<br>
    <input type=text size=50 name="new_feed"><br>

<?php
#####################  PHP Code ################################

# Query the database

$query = 'SELECT id, url, title FROM rss ORDER BY id';

$result = run_query($query, NULL);

# Format the results into a table

foreach ($result as $row) {
    printf("<input type=\"checkbox\" value=\"%d\" name=\"delete[]\">", $row['id']);
    printf("<a href=\"%s\">%s</a><br>", $row['url'], $row['title']);
}

#####################  End PHP Code ############################
?>

<input type=submit name=Submit value=Submit><br>
</form>
</html>
