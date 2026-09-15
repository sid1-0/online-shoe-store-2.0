<?php
function getNameByAdminId($admin_id){
	$connection = new mysqli('localhost','root','','shoes');
	if($connection->connect_errno !=0){
	die('Database Connection Error:'.$connection->connect_error);
}
	//query to select data from table
$sql="SELECT name from admins where id=$admin_id";

//execute query 
$result=$connection->query($sql);

//checking if the databse is empty for fetching the data

	//fetching data from result object using while loop
	$row = $result->fetch_assoc();
		

	}
?>