<?php
session_start();
echo "quantity : " .  $_SESSION['quantity']; 
echo "priceId : "  . $_SESSION['priceTypeId']; 
echo "code : " . $_SESSION['elementId'];
?>