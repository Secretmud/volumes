<?php

function dangerBox($text) {
    $returText = '<div class="info info-danger">';
    $returText .= "<h2>".$text."</h2>";
    $returText .= '</div>';
    
    return $returText;
}

function successBox($text) {
    $returText = '<div class="info info-success">';
    $returText .= "<h2>".$text."</h2>";
    $returText .= '</div>'; 
    
    return $returText;
}

function warningBox($text) {
    $returText = '<div class="info info-warning">';
    $returText .= "<h2>".$text."</h2>";
    $returText .= '</div>';  
    
    return $returText;
}


?>