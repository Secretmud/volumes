<?php

    //--- RegEx functions ---
    function regexStudentID($studentID) {
        $controlStudentID = '/^[s]{1}[0-9]{6}$/';

        if (preg_match($controlStudentID, $studentID)) {
            return true;
        } else {
            return false;
        }
    }

    function regexName($name) {
        $controlName = '/^[a-zæøåA-ZÆØÅ ]{2,25}$/';

        if (preg_match($controlName, $name)) {
            return true;
        } else {
            return false;
        }
    }

    function regexEmail($email) {
        $controlEmail = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        
        if (preg_match($controlEmail, $email)) {
            return true;
        } else {
            return false;
        }
    }

    function regexAddress($address) {
        $controlAddress = '/^[a-zæøåA-ZÆØÅ0-9 ]{2,50}$/';

        if (preg_match($controlAddress, $address)) {
            return true;
        } else {
            return false;
        }
    }

    function regexPostalNr($postalNr, $dbconn) {
        
        $controlPostalNr = '/^[0-9]{4}$/';

        if (preg_match($controlPostalNr, $postalNr)) {

            try {
                $sql = "SELECT postalNr FROM postaltable WHERE postalNr = '".$postalNr."';";
                $dbconn->query($sql);
                $numberOfRows = $dbconn->affected_rows;
                
                if($numberOfRows === 1) { 
                    return true;
                } else {
                    return false;
                }
            } catch (mysqli_sql_exception $e) {
                return "Error with database";
            }
        } else {
            return false;
        }
    }
?>