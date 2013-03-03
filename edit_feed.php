<html>

<body>
<a href="manage_feeds.php">Back</a>
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

$feed_id = $_REQUEST['id'];
$query = 'SELECT id, url, title, category, link FROM rss WHERE id = ?';
$result = run_query($query, array($feed_id));
$row=$result[0];

if ($_POST['Submit'] == 'Submit') {

    # Check our CSRF token
    if (!csrf_validate($_POST['csrf'])) {
        throw new Exception("Bad CSRF token");
    }

    # Figure out what changed
    # $row['url']
    if ($row['url'] != $_POST['url']) {
        $query = 'UPDATE rss SET url = ? WHERE id =?';
        run_query($query, array($_POST['url'], $feed_id));
    }

    # $row['title']
    if ($row['title'] != $_POST['title']) {
        $query = 'UPDATE rss SET title = ? WHERE id =?';
        run_query($query, array($_POST['title'], $feed_id));
    }

    # $row['category']
    if ($row['category'] != $_POST['category']) {
        $query = 'UPDATE rss SET category = ? WHERE id =?';
        run_query($query, array($_POST['category'], $feed_id));
    }

    # $row['link']
    if ($row['link'] != $_POST['link']) {
        $query = 'UPDATE rss SET link = ? WHERE id =?';
        run_query($query, array($_POST['link'], $feed_id));
    }

    # Reread the data from the database, so the users sees what changed
    $query = 'SELECT id, url, title, category, link FROM rss WHERE id = ?';
    $result = run_query($query, array($feed_id));
    $row=$result[0];

}


#####################  End PHP Code ############################
?>

<form method="post">

<?php
#####################  PHP Code ################################

# Add our CSRF token
printf("<input type=\"hidden\" value=\"%s\" name=\"csrf\">\n",
    csrf_get_token());

# Query the database

$query = 'SELECT id, name FROM categories ORDER by name';
$categories = run_query($query, NULL);

# Format the results into a table

printf("<input type=\"hidden\" name=\"id\" value=\"%s\">\n", $feed_id);
print("<table>\n");

if ($row) {

    print("<tr bgcolor=\"#ffffff\"><td>\n");
    printf("Name");
    print("</td><td>\n");
    printf("<input type=\"text\" name=\"title\", value=\"%s\" size=%d>\n",
        $row['title'], strlen($row['title']));
    printf("</td></tr>");

    print("<tr bgcolor=\"#ffffff\"><td>\n");
    printf("<a href=\"%s\">Page URL</a>", $row['link']);
    print("</td><td>\n");
    printf("<input type=\"text\" name=\"link\", value=\"%s\" size=%d>\n",
        $row['link'], strlen($row['link']));
    printf("</td></tr>");

    print("<tr bgcolor=\"#ffffff\"><td>\n");
    printf("<a href=\"%s\">Feed URL</a>", $row['url']);
    print("</td><td>\n");
    printf("<input type=\"text\" name=\"url\", value=\"%s\" size=%d>\n",
        $row['url'], strlen($row['url']));
    printf("</td></tr>");

    printf("<tr><td>\n");
    printf("Category");
    printf("</td><td>");
    foreach ($categories as $c) {
        if ($c['id'] == $row['category']) {
            $checked = 'checked';
        } else {
            $checked = '';
        }
        printf("<input type=\"radio\" value=\"%d\" name=\"category\" %s>%s",
            $c['id'], $checked, $c['name']);
    }

    print("</td></tr>");

}

print("</table>\n");

run_query('COMMIT', NULL);

#####################  End PHP Code ############################
?>

<input type=submit name=Submit value=Submit><br>
</form>
</html>
