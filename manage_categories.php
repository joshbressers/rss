<html>

<body>
<a href="manage_feeds.php">Back</a><hr>
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

        $query = 'DELETE FROM categories WHERE id = ?';

        foreach ($_POST['delete'] as $delete_me) {
            run_query($query, $delete_me);
        }
    }

    if ($_POST['new_category']) {
        # Add a new feed
        $name = $_POST['new_category'];

        $query = 'INSERT INTO categories (name) VALUES (?)';
        run_query($query, $name);

    }
}


#####################  End PHP Code ############################
?>

<form method="post">

    Add Category:<br>
    <input type=text size=20 name="new_category"><br>

<?php
#####################  PHP Code ################################

# Query the database

$query = 'SELECT id, name FROM categories';

$result = run_query($query, NULL);

# Format the results into a table

print("<table>\n");
$table_odd = 0;
foreach ($result as $row) {
    if ($table_odd) {
        print("<tr><td bgcolor=\"#ffffff\">\n");
        $table_odd = 0;
    } else {
        print("<tr><td bgcolor=\"#eeeeee\">\n");
        $table_odd = 1;
    }

    printf("<input type=\"checkbox\" value=\"%d\" name=\"delete[]\">", $row['id']);
    printf("%s</td></tr>\n", $row['name']);
}
print("</table>\n");
run_query('COMMIT', NULL);

#####################  End PHP Code ############################
?>

<input type=submit name=Submit value=Submit><br>
</form>
</html>
