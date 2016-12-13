<?php
session_start();  
include("mysql_connect.inc.php");
$airline	= $_POST['airline'];
$iata    	= $_POST['iata'];
$telephone  = $_POST['telephone'];
$createtime = date("Y-m-d H:i:s",(time()+8*3600));

$TpyeconuntSQL = "SELECT 
					AIRLINE_IATA_CD, AIRLINE_NAME
				FROM 
					$schema.tb_airline_info
				WHERE
				    AIRLINE_IATA_CD = '$iata' OR AIRLINE_NAME = '$airline'";

$ConuResult = $conn->query($TpyeconuntSQL);
$row = $ConuResult->fetch_assoc();
// echo $row["AIRLINE_IATA_CD"];
// echo $row["AIRLINE_NAME"];

if ($row != null) {
	echo $airline." is existing in System.";
	echo '<meta http-equiv=REFRESH CONTENT=1;url=reg_airline.php>';
	}else{
		$InsAirlineSQL = "INSERT INTO 
		                    $schema.tb_airline_info 
		                    (AIRLINE_ID, AIRLINE_IATA_CD, AIRLINE_NAME, TEL_NO, CREATE_DATE) 
		                    VALUES 
		                    ('', '$iata', '$airline', '$telephone', '$createtime')";
		                    
		$conn->query($InsAirlineSQL);
		
		$AirlineSQL = "SELECT
						AIRLINE_ID
					FROM 
						$schema.tb_airline_info 
					WHERE 
						AIRLINE_IATA_CD = '$iata'";
		
		$conn->query($AirlineSQL);
		$Result = $conn->query($AirlineSQL);
		$row = $Result->fetch_assoc();
		
		echo $airline." airline ID is ".$row["AIRLINE_ID"];
		echo '<meta http-equiv=REFRESH CONTENT=5;url=reg_airline.php>';
}

?>