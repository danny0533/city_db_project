<?php
session_start();  
	// Database Connection
include("mysql_connect.inc.php");
$aid = $_POST['rm_aid'];

		    $rmUserSQL = " DELETE FROM 
					            $schema.tb_airline_info 
				            WHERE 
					            AIRLINE_ID = $aid";
		    
		    $conn->query($rmUserSQL);
		    echo "Airline removed";
		    echo '<meta http-equiv=REFRESH CONTENT=1;url=update_alluser.php>';

?>