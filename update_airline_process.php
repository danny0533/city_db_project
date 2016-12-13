<?php
session_start();  
    // Database Connection
include("mysql_connect.inc.php");
$aid 		= $_POST['aid'];
$iata   	= $_POST['iata'];
$aname 		= $_POST['aname'];
$telephone	= $_POST['telephone'];



            $UpdairlineSQL = "UPDATE 
                                $schema.tb_airline_info 
                            SET
                                AIRLINE_ID = '$aid',
                                AIRLINE_IATA_CD = '$iata',
                                AIRLINE_NAME = '$aname',
                                TEL_NO = '$airline'
                            WHERE 
                                AIRLINE_ID = $aid";
            
            $conn->query($UpdairlineSQL);
            echo "Airline updated";
            echo '<meta http-equiv=REFRESH CONTENT=1;url=update_airline.php>';


?>