<?php 
include 'include/dbconnection.php';
include 'include/boxFunctions.php';
include 'include/regex.php';

$dbconn = establishDBConnection($host, $user, $passwd, $db);
?>


<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>Update</title>
</head>

<body>
    <nav class="navigation">
        <ul>
            <a href="index.php">
                <li class="logo">Student Information</li>
            </a>
            <a href="index.php" class="active">
                <li class="link">List All Students</li>
            </a>
            <a href="create.php">
                <li class="link">Register Student</li>
            </a>
        </ul>
    </nav>

    <h1 class="title">Update Student Information</h1>

    <?php 
        
        if(isset($_GET["studNr"])) {
            try {
                $studentID = mysqli_real_escape_string($dbconn, $_GET["studNr"]);

                $sql = "SELECT studentID FROM students WHERE studentID = '".$studentID."';";
                $dbconn->query($sql); 
                $rows = $dbconn->affected_rows;
                if($rows != 1) {
                    echo warningBox("No student given with nr: '".$_GET["studNr"]."'. </br>Please go to the <a href='index.php'>homepage</a> and select student you want to change.");
                    include 'include/footer.php';
                    echo '</body>';
                    echo '</html>';
                    die();
                } 
            } catch (mysqli_sql_exception $e) {
                echo dangerBox("There was an error communicating with the database. Please try again later!");
                include 'include/footer.php';
                echo '</body>';
                echo '</html>';
                die();
            }
        } else {
            echo warningBox("No student given. </br>Please go to the <a href='index.php'>homepage</a> and select student you want to change.");
            include 'include/footer.php';
            echo '</body>';
            echo '</html>';
            die();
        }
    
        /*
            If the $_POST["update"] variable isset, perform an update of the given students information.
        */
        if(isset($_POST["update"])) {
            
            // protect against mysql-injection
            $firstName = mysqli_real_escape_string($dbconn, $_POST['firstName']);
            $lastName = mysqli_real_escape_string($dbconn, $_POST['lastName']);
            $email = mysqli_real_escape_string($dbconn, $_POST['email']);
            $address = mysqli_real_escape_string($dbconn, $_POST['address']);
            $postalNr = mysqli_real_escape_string($dbconn, $_POST['postalNr']);
            $degree = mysqli_real_escape_string($dbconn, $_POST['degree']);
            
            // update sql-statement
            $sql = "UPDATE students SET firstName = '$firstName', lastName = '$lastName', email = '$email', address = '$address', postalNr = '$postalNr', degree = '$degree'";
            $sql .= " WHERE studentID = '$studentID';";  
            
            
            // regex for inputs
            $regexOK = true;
            $warning = "";
            
            //--- Regex for input ---
            if(!regexStudentID($studentID)) {
                $regexOK = false;
                $warning .= "Student ID invalid. </br>";
            }
            
            if(!regexName($firstName)) {
                $regexOK = false;
                $warning .= "First name invalid. </br>";
            }
            
            if(!regexName($lastName)) {
                $regexOK = false;
                $warning .= "Last name invalid. </br>";
                
            }
            
            if(!regexEmail($email)) {
                $regexOK = false;
                $warning .= "Email invalid. </br>";
                
            }
            
            if(!regexAddress($address)) {
                $regexOK = false;
                $warning .= "Address invalid. </br>";
            }
            
            $resultPostalNrRegex = regexPostalNr($postalNr, $dbconn);

            if($resultPostalNrRegex === "Error with database") {
                echo dangerBox("There was an error communicating with the database. No updates made.");
                include 'include/footer.php';
                echo "</body>";
                echo "</html>";
                die();
            }
            
            if(!$resultPostalNrRegex) {
                $regexOK = false;
                $warning .= "Postal nr invalid. </br>";
            }
            
            // perform update
            if($regexOK){
                try {
                    $dbconn->query($sql);
                    $rows = $dbconn->affected_rows;
                    if($rows === 0) {
                        echo warningBox('Didn\'t find any changes for student '.$studentID.'.');
                    }else{
                        echo successBox('Information for student '.$studentID.' was updated in the database.');
                    }
                    
                } catch (mysqli_sql_exception $e) { // if update failed, create an error message.
                    echo dangerBox('Something went wrong when updating information for '.$studentID.'. Please try again.');
                    include 'include/footer.php';
                    echo '</body>';
                    echo '</html>';
                    die();
                }
                                       
            }else{
                // if an update has been performed, echo out the result of said update.
                echo warningBox($warning);
            }
        }
        
        /*
            Get the current information for the specified student
        */
    if (!isset($_POST["update"])) {
        try {
            $sql = "SELECT * FROM students WHERE studentID ='".$studentID."';";
            $result = $dbconn->query($sql);
            if($dbconn->affected_rows === 1) { // student found for given student id.
                $row = $result->fetch_assoc();
                $firstName = $row['firstName'];
                $lastName = $row['lastName'];
                $email = $row['email'];
                $address = $row['address'];
                $postalNr = $row['postalNr'];
                $degree = $row['degree'];
            } else { // no student found matching the given student id.
                echo dangerBox("No student with student nr ".$_GET["studNr"]." found.");
                include 'include/footer.php';
                echo '</body>';
                echo '</html>';
                die();
            }
            $result->close();
        
        /*
            If a DB error occours, create an error message, complete the HTML, and kill the remainder of the page.
        */
        } catch (mysqli_sql_exception $e) {
            echo dangerBox("There was an error communicating with the database. Please try again later!");
            include 'include/footer.php';
            echo '</body>';
            echo '</html>';
            die();
        }
    }
    /*
    Put all degrees currently in the database into an array called $allDegrees. This array is used
    to populate the degrees drop down box in the form.
    */
    try{
        $sql =  "SELECT * FROM degree;";
        $result = $dbconn->query($sql);
        $allDegrees = array();
        while($row = $result->fetch_assoc()) {
            array_push($allDegrees, $row["degree"]);
        }
        $result->close();
    } catch (mysqli_sql_exception $e) {
        echo dangerBox("There was an error communicating with the database. Please try again later!");
        include 'include/footer.php';
        echo '</body>';
        echo '</html>';
        die();
    }
        
    ?>

    <form action="" method="post" class="form">
        <table class="formtable">
            <tr>
                <td>Student ID:</td>
                <td><input type='text' name='studentID' value='<?php echo $studentID?>' disabled/></td>
            </tr>
            <tr>
                <td>First name:</td>
                <td><input type='text' name='firstName' value='<?php echo $firstName?>' maxlength='25' /></td>
            </tr>
            <tr>
                <td>Last name:</td>
                <td><input type='text' name='lastName' value='<?php echo $lastName?>' maxlength='25' /></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input type='text' name='email' value='<?php echo $email?>' maxlength='140' /></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><input type='text' name='address' value='<?php echo $address?>' maxlength='50' /></td>
            </tr>
            <tr>
                <td>Postal nr:</td>
                <td><input type='text' name='postalNr' value='<?php echo $postalNr?>' maxlength='4' /></td>
            </tr>
            <tr>
                <td>Degree:</td>
                <td><select name='degree'>
                    <?php
                        // create a select box containing the study programs currently in the db.
                        foreach($allDegrees as $d) {
                            if($d === $degree) {
                                echo '<option value="'.$d.'" selected="selected">'.$d.'</option>';
                            } else {
                                echo '<option value="'.$d.'">'.$d.'</option>';
                            }
                        }
                    ?>
                </select></td>
            </tr>
            <tr>
                <td>
                </td>
                <td class="buttons">
                    <input type='submit' name='update' value='Update' />
                    <input type='submit' formaction="index.php" value='Cancel' />
                </td>
            </tr>
        </table>
    </form>


</body>
<?php include 'include/footer.php';  ?>

</html>
