<?php
$hname="localhost";
$uname="root";
$pass="";
$dbname="db_socialmedia";

$conn=mysqli_connect($hname,$uname,$pass,$dbname);
if(!$conn)
{
echo "erro";
die();
}
?>