<?php 
session_start(); 
?>

<link href="main.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
include("mysql_connect.inc.php");
$userid 	= $_POST['userid'];
$password 	= $_POST['password'];
//$schema		= $_POST['schema'];
$lastlogin = date("Y-m-d H:i:s",(time()+8*3600));

$UserInfoSQL = "SELECT
					USER_ID, USER_NAME, PASSWORD, TEL_NO, USER_TYPE, CREATE_DATE, LAST_LOGIN, AIRLINE_IATA_CD
				FROM 
					$schema.tb_user_info 
				WHERE 
					USER_ID = '$userid' and 
					PASSWORD = '$password'";

$UserInfoSQLResult = $conn->query($UserInfoSQL);

if ($UserInfoSQLResult->num_rows > 0) {
	while($row = $UserInfoSQLResult->fetch_assoc()) {
		$_SESSION['USER_ID'] = $row["USER_ID"];
		$_SESSION['username'] = $row["USER_NAME"];
		$_SESSION['USER_TYPE'] = $row["USER_TYPE"];
		$_SESSION['AIRLINE_IATA_CD'] = $row["AIRLINE_IATA_CD"];
		
		?><p><font size="12"><?php echo 'Login Success';?></font></p><?php
		echo '<meta http-equiv=REFRESH CONTENT=1;url=Depart_Sch.php>';
		
		$UpdLastLogTimeSQL = "	UPDATE 
									$schema.tb_user_info 
								SET 
									LAST_LOGIN = '$lastlogin' 
								WHERE 
									USER_ID = '$userid'";
									
		$conn->query($UpdLastLogTimeSQL);
	}
} else {
    ?><p><font size="12"><?php echo 'Login Fail';?></font></p><?php
    echo '<meta http-equiv=REFRESH CONTENT=1;url=login.php>';
}
?>