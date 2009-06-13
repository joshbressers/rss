<html>

<body>
<a href="show_feeds.php">Back</a>
<a href="manage_categories.php">Categories</a>
<hr>
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

run_query('START TRANSACTION', NULL);

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
        $link = $rss->channel['link'];

        $query = 'INSERT INTO rss (url, link, title, last_update) VALUES (?, ?, ?, ?)';
        run_query($query, array($url, $link, $title, 0));

    }

    foreach (array_keys($_POST) as $post_variable) {
        # if /category-%d/
        if (!strncmp($post_variable, 'category-', 9)) {
            $value = $_POST[$post_variable];
            list($null, $rss_id) = split('-', $post_variable);

            # Is this the current value in the db?
            $query = 'SELECT category FROM rss WHERE id = ?';
            $result = run_query($query, array($rss_id));

            if ($result[0]['category'] != $value) {
                # If not, update the db.
                $query = 'UPDATE rss SET category = ? WHERE id = ?';
                run_query($query, array($value, $rss_id));
            }
        }
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

$query = 'SELECT id, url, title, category FROM rss ORDER BY id';
$result = run_query($query, NULL);

$query = 'SELECT id, name FROM categories ORDER by name';
$categories = run_query($query, NULL);

# Format the results into a table

print("<table>\n");
$table_odd = 0;
foreach ($result as $row) {
    if ($table_odd) {
        print("<tr bgcolor=\"#ffffff\"><td>\n");
        $table_odd = 0;
    } else {
        print("<tr bgcolor=\"#eeeeee\"><td>\n");
        $table_odd = 1;
    }
    printf("<input type=\"checkbox\" value=\"%d\" name=\"delete[]\">", $row['id']);
    print("</td><td>\n");
    printf("<a href=\"edit_feed.php?id=%s\">%s</a>", $row['id'], $row['title']);
    printf("</td>");

    foreach ($categories as $c) {
        if ($c['id'] == $row['category']) {
            $checked = 'checked';
        } else {
            $checked = '';
        }
        print("<td>\n");
        printf("<input type=\"radio\" value=\"%d\" name=\"category-%d\" %s>%s",
            $c['id'], $row['id'], $checked, $c['name']);
        print("</td>\n");
    }

    print("</tr>");
}
print("</table>\n");
run_query('COMMIT', NULL);

#####################  End PHP Code ############################
?>

<input type=submit name=Submit value=Submit><br>
</form>
</html>
