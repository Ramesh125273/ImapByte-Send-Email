<?php 

$con=mysqli_connect('localhost','root','','users');

if($con==false){
  echo "Connection is not done";
} 
else{
  echo "Connection ok";
}
?>