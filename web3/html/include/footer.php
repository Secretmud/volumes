<?php
    $ip = $_SERVER['SERVER_ADDR'];

    echo '<div class="footer">';
    echo "Served by IP: <b>" . $ip . "</b></br>";
    $datetime = date("j F, Y, G:i a");
    echo "on: " . $datetime;
    echo '</div>';

    $dbconn->close();
?>
