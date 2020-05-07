<?php 
//ini_set('display_errors',1);
//ini_set('error_reporting',E_ALL);

include 'include/dbconnection.php';
include 'include/boxFunctions.php';
$dbconn = establishDBConnection($host, $user, $passwd, $db);
?>

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
    </nav>
    
    <div class="delete-question">
    <?php
        if(isset($_GET["studNr"])) {
            $studentID = mysqli_real_escape_string($dbconn, $_GET["studNr"]);
            
            echo "<h2>Are you sure you want to delete ".$studentID."? </h2></br>";
            echo '<a href="index.php?studNr='.$studentID.'" class="btn btn-yes">Yes</a>';
            echo '<a href="index.php" class="btn btn-no">No</a>';
 
        } else {
            echo warningBox("Something went wrong! Please try again.");
        }
    ?>
    </div>
 
        
    <?php include 'include/footer.php';?>
</body>

</html>
