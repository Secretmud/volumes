<?php 
include 'include/dbconnection.php'; 
include 'include/regex.php'; 
include 'include/boxFunctions.php'; 

$dbconn = establishDBConnection($host, $user, $passwd, $db);
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <title>Add Student</title>
</head>

<body>
    <nav class="navigation">
        <ul>
            <a href="index.php"><li class="logo">Student Information</li></a>
            <a href="index.php"><li class="link">List All Students</li></a>
            <a href="create.php" class="active"><li class="link">Register Student</li></a>
        </ul>
    </nav>
    
    <h1 class="title">Register New Student</h1>
    
    <?php
        //--- If create butten is pressed ---
        if(isset($_POST["create"])) {
            $studentID = mysqli_real_escape_string($dbconn, $_POST["studentID"]);
            $firstName = mysqli_real_escape_string($dbconn, $_POST['firstName']);
            $lastName = mysqli_real_escape_string($dbconn, $_POST['lastName']);
            $email = mysqli_real_escape_string($dbconn, $_POST['email']);
            $address = mysqli_real_escape_string($dbconn, $_POST['address']);
            $postalNr = mysqli_real_escape_string($dbconn, $_POST['postalNr']);
            $degree = mysqli_real_escape_string($dbconn, $_POST['degree']);
            
            $regexOK = true;
            $warning = "";
            
            //--- Regex for input ---
            if(!regexStudentID($studentID)) {
                $regexOK = false;
                $warning .= "Student ID invalid.  </br>";
            } else {
                // Check studentID already exists.
                try {
                    $sql = "SELECT studentID FROM students WHERE studentID = '".$studentID."';";  

                    $dbconn->query($sql);
                    
                    if ($dbconn->affected_rows === 1) {
                        $regexOK = false;
                        $warning .= "Student ID already exists.  </br>";
                    }
                }catch (mysqli_sql_exception $e) {
                    echo dangerBox("There was an error communicating with the database.");
                }
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
                echo dangerBox("There was an error communicating with the database.");
                include 'include/footer.php';
                echo "</body>";
                echo "</html>";
                die();
            }
            
            if(!$resultPostalNrRegex) {
                $regexOK = false;
                $warning .= "Postal nr invalid. </br>";
            }
            
            //--- Registrations of new student ---
            if($regexOK) {
                try {
                    $sql = "INSERT INTO students (studentID,firstName,lastName,email,address,postalNr,degree) VALUES ('".$studentID."','".$firstName."','".$lastName."','".$email."','".$address."','".$postalNr."','".$degree."');";  

                    $dbconn->query($sql);
                    $updateRows = $dbconn->affected_rows;

                    if ($updateRows === 1) {
                        echo successBox("Information for student ".$studentID." is saved in the database.");
                    } else {
                        echo dangerBox("Something went wrong when creating new student ".$studentID.". Please try again.");
                    }

                }catch (mysqli_sql_exception $e) {
                    echo dangerBox("There was an error communicating with the database.");
                }
            }else {
                // Echoes out which input(s) form the user that has failed the regex
                echo warningBox($warning);
            }
        }
    ?>
    
    <form action="" method="post" class="form">
        <table class="formtable">
            <tr>
                <td>Student ID:</td>
                <td><input type='text' name='studentID' maxlength='7' placeholder='Format: s123456'/></td>
            </tr>
            <tr>
                <td>First name:</td>
                <td><input type='text' name='firstName' maxlength='25' /></td>
            </tr>
            <tr>
                <td>Last name:</td>
                <td><input type='text' name='lastName' maxlength='25' /></td>
            </tr>
            <tr>
                <td>E-mail:</td>
                <td><input type='text' name='email' maxlength='140'/></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><input type='text' name='address' maxlength='50' /></td>
            </tr>
            <tr>
                <td>Postal nr:</td>
                <td><input type='text' name='postalNr' maxlength='4' /></td>
            </tr>
            <tr>
                <td>Degree:</td>
                <td><select name='degree'>
                    <?php
                    try {
                        $sql = "SELECT * FROM degree;";
                        $result = $dbconn->query($sql);
                        while($row = $result->fetch_assoc()) {
                   
                            echo '<option value="'.$row["degree"].'">'.$row["degree"].'</option>';
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo '<option>Error connection w/database.</option>';
                    }
                    ?>
                </select></td>
            </tr>
            <tr>
                <td>
                    
                </td>
                <td class="buttons">
                    <input type='submit' name='create' value='Create' />
                    <input type='submit' formaction="index.php" value='Cancel' />
                </td>
            </tr>
        </table>
    </form>

</body>
<?php include 'include/footer.php';  ?>

</html>
