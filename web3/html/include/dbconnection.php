<?php
/*
    Include this file at top of all pages so as to establish connection with the db.
    Make sure to include the file footer.php at the bottom of all pages to close
    this db connection.
*/
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(0);

$host = "maxscale";
$user = "DATSUSER"; // change to your ALTO user
$passwd = "DATSPASS"; // change to your ALTO password
$db = "studentinfo";


// Call this function on pages when the entire page needs database connection.
function establishDBConnection($host, $user, $passwd, $db) {
    try {
        $dbconn = new mysqli($host, $user, $passwd, $db);
        $dbconn->set_charset("utf8");
        return $dbconn;
    } catch (mysqli_sql_exception $e) {
        echo '<!DOCTYPE html>
            <html>

            <head>
                <meta charset="UTF-8">
                <link rel="stylesheet" type="text/css" href="stylesheet.css">
                <title>Student Information</title>
            </head>

            <body>
                <nav class="navigation">
                    <ul>
                        <a href="index.php"><li class="logo">Student Information</li></a>
                        <a href="index.php" class="active"><li class="link">List All Students</li></a>
                        <a href="create.php"><li class="link">Register Student</li></a>
                    </ul>
                </nav>';

        $returText = '<div class="info info-danger">';
        $returText .= "<h1>Couldn't connect to database.</h1>";
        $returText .= "<h2>Please try again later.</h2>";
 
        $returText .= '</div>';
        echo $returText;

        echo '<div class="footer">';
        echo "Served by: <b>" . $_SERVER['SERVER_ADDR'] . "</b></br>";
        $datetime = date("j F, Y, G:i a");
        echo "on: " . $datetime;
        echo '</div>';
        echo '</body>';
        echo '</html>';
        die();
    }
}
?>
