<?php
$connect_error='Sorry, we\'re experiencing connection problems.';
$connection=@mysqli_connect('localhost','root','','login') or die($connect_error);
?>
