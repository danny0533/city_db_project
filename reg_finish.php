<?php
session_start();
// Database Connection
include("mysql_connect.inc.php");
$username 	= $_POST['username'];
$password 	= $_POST['password'];
$cpassword  = $_POST['cpassword'];
$telephone	= $_POST['telephone'];
$usertype   = $_POST['usertype'];
$airline	= $_POST['airline'];
$createtime = date("Y-m-d H:i:s",(time()+8*3600));

//Select and Count SM in each airline.
$TpyeconuntSQL = "SELECT 
					COUNT(1) as countUsrType 
				FROM 
					$schema.tb_user_info 
				WHERE 
					AIRLINE_IATA_CD = '$airline' AND USER_TYPE = 'SM'
				GROUP BY 
					AIRLINE_IATA_CD";

$ConuResult = $conn->query($TpyeconuntSQL);
$row = $ConuResult->fetch_assoc();
// echo $row["countUsrType"];

if ($row != 0) {
	echo "Station Manager existing in ".$airline." airline";
	echo '<meta http-equiv=REFRESH CONTENT=1;url=register.php>';
}
elseif ($password = $cpassword && $usertype != null) {
	$InsUserSQL = "INSERT INTO $schema.tb_user_info 
						(USER_ID, USER_NAME, PASSWORD, TEL_NO, USER_TYPE, CREATE_DATE, LAST_LOGIN, AIRLINE_IATA_CD) 
						VALUES 
						('', '$username', '$password', '$telephone', '$usertype', '$createtime', '', '$airline')";
	$conn->query($InsUserSQL);
	
	$UIDSQL = "SELECT
						USER_ID
					FROM 
						$schema.tb_user_info 
					WHERE 
						USER_NAME = '$username'";
	
	$conn->query($UIDSQL);
	$Result = $conn->query($UIDSQL);
	$row = $Result->fetch_assoc();
	
	echo $username."ID is".$row["USER_ID"];
	echo '<meta http-equiv=REFRESH CONTENT=1;url=reg_user.php>';
}
else{
	echo '<div>Password problem</div>';
	echo '<meta http-equiv=REFRESH CONTENT=1;url=reg_user.php>';
}

?>