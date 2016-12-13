<?php
session_start();  
	// Database Connection
include("mysql_connect.inc.php");
$userid 	= $_SESSION['USER_ID'];
$username 	= $_POST['username'];
$telephone	= $_POST['telephone'];
$usertype   = $_POST['usertype'];
$airline	= $_POST['airline'];

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

// if ($row != 0) {
// 	echo "Station Manager existing in ".$airline." airline";
// 	echo '<meta http-equiv=REFRESH CONTENT=1;url=update_alluser.php>';
// 	}else{
		    $UpdUserSQL = " UPDATE 
					            $schema.tb_user_info 
                            SET
					            USER_NAME = '$username',
					            TEL_NO = '$telephone',
					            USER_TYPE = '$usertype',
					            AIRLINE_IATA_CD = '$airline'
					            
				            WHERE 
					            USER_ID = $userid";
		    
		    $conn->query($UpdUserSQL);
		    echo "Profile updated";
		    echo '<meta http-equiv=REFRESH CONTENT=1;url=update_alluser.php>';
// }

?>