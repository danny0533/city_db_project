<?php 
session_start();
?>

<link href="main.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
	include("mysql_connect.inc.php");
	$userid 	= $_SESSION['USER_ID'];
	$username   = $_POST['username'];
	$opassword  = $_POST['opassword'];
	$npassword  = $_POST['npassword'];
	$cpassword  = $_POST['cpassword'];
	
	$UserInfoSQL = "SELECT
						PASSWORD
					FROM 
						$schema.tb_user_info 
					WHERE 
						USER_ID = $userid";
	
	$Result = $conn->query($UserInfoSQL);
	$row = $Result->fetch_assoc();
	// echo $row["PASSWORD"];
	
if($opassword != $row["PASSWORD"] or $npassword != $cpassword){
	echo '<div>Password Fail</div>';
	echo '<meta http-equiv=REFRESH CONTENT=1;url=update_form.php>';
	}else{
	$UpdUserSQL = "	UPDATE 
						$schema.tb_user_info 
					SET
						PASSWORD = '$npassword' 
					WHERE 
						USER_ID = $userid";
					
	$conn->query($UpdUserSQL);
	echo "Profile updated";
	echo '<meta http-equiv=REFRESH CONTENT=1;url=update_form.php>';
}

?>