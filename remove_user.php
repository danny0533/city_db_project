<?php
session_start();  
	// Database Connection
include("mysql_connect.inc.php");
$userid = $_POST['rm_userid'];

		    $rmUserSQL = " DELETE FROM 
					            $schema.tb_user_info 
				            WHERE 
					            USER_ID = $userid";
		    
		    $conn->query($rmUserSQL);
		    echo "User removed";
		    echo '<meta http-equiv=REFRESH CONTENT=1;url=update_alluser.php>';

?>