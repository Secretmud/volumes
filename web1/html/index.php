<?php
// ini_set('display_errors',1);
// ini_set('error_reporting',E_ALL);

include 'include/dbconnection.php';
include 'include/boxFunctions.php';
$dbconn = establishDBConnection($host, $user, $passwd, $db);
?>

<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>DATA2410 Student Info App</title>
</head>

<body>
    <nav class="navigation">
        <ul>
            <a href="index.php"><li class="logo">Student Information</li></a>
            <a href="index.php" class="active"><li class="link">List All Students</li></a>
            <a href="create.php"><li class="link">Register Student</li></a>
        </ul>
    </nav>

    <h1 class="title">List of students</h1>

<?php
    if(isset($_GET["studNr"])) {

        $studentID = mysqli_real_escape_string($dbconn, $_GET["studNr"]);

        try {
            $sql = "SELECT * FROM students WHERE studentID ='".$studentID."';";

            $dbconn->query($sql);
            $rows = $dbconn->affected_rows;

                if($rows != 1) {
                    $text = warningBox("No student with id ".$studentID." found.");
                } else {
                    $sql = "DELETE FROM students WHERE studentID = '".$studentID."';";

                    $dbconn->query($sql);
                    $rows = $dbconn->affected_rows;

                    if($rows === 1) {
                        echo successBox($studentID." removed from the database.");
                        // To ensure that new studentlist dosn't include the deleted student.
                        // Half a second.
                        usleep(500000);
                    }else {
                        echo dangerBox("There was an error communicating with the database. Please try again later.");
                    }
                }
            }catch (mysqli_sql_exception $e) {
                echo dangerBox("There was an error communicating with the database.");
                include 'include/footer.php';
                echo '</body>';
                echo '</html>';
                die();
            }
    }

?>

    <div class="students">
        <table class="studenttable">
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>E-mail</th>
                <th>Address</th>
                <th>Postal Nr</th>
                <th>Postal Address</th>
                <th>Degree</th>
                <th>Edit</th>
            </tr>

            <?php
            $count = 0;
            try {
                $sql = "SELECT * FROM students, postaltable WHERE students.postalNr = postaltable.postalNr";

                $result = $dbconn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo "<td>{$row['studentID']}</td>";
                    echo "<td>{$row['lastName']}, {$row['firstName']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['address']}</td>";
                    echo "<td>{$row['postalNr']}</td>";
                    echo "<td>{$row['postalArea']}</td>";
                    echo "<td>{$row['degree']}</td>";
                    echo '<td class="edit"><a href="update.php?studNr='.$row['studentID'].'" class="btn btn-update">Update</a> ';
                    echo '<a href="delete.php?studNr='.$row['studentID'].'" class="btn btn-delete">Delete</a></td>';
                    $count++;
                    echo '</tr>';
                }

                echo '</table>';
                $result->close();
            } catch (mysqli_sql_exception $e) {
                echo '</table>';
                echo dangerBox("There was an error communicating with the database.");
            }

            echo '<p class="count">Total number of students: '.$count.'</p>';
            ?>

    </div>

    <?php include 'include/footer.php';?>
</body>

</html>
