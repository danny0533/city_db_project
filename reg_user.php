<?php
session_start();  

include("mysql_connect.inc.php");
?>

<!DOCTYPE html>
<html>
<head>
<title>Registration</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="main.css" media="all" />
	<style>
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
		th, td {
			padding: 5px;
			text-align: left;
		}
	</style>

</head>
<body>
<?php
    if($_SESSION['USER_TYPE'] != 'RC' ){ 
    echo 'Please contact Ramp Contral!';
    echo '<meta http-equiv=REFRESH CONTENT=1;url=login.php>';
?>
<?php
	}else{ 
?>
    <?php include("header.php");?>
        <div class="container">
                <form name="form" method="post" action="reg_finish.php" >
                    <div class="imgcontainer"></div>
                        <div class="container">
                            <label><b>Username</b></label>
                            <input type="text" placeholder="Enter Username" name="username" required>
                            <label><b>Password</b></label>
                            <input type="password" placeholder="Enter Password" name="password" required>
                            <label><b>Confirm Password</b></label>
                            <input type="password" placeholder="Confirm Password" name="cpassword" required>
                            <label><b>Telephone</b></label>
                            <input type="text" maxlength="8" placeholder="Telephone" name="telephone" required>
                            <label><b>User Type: </b></label><br>
                            <select name="usertype">
								<option disabled selected value> -- Select an Option -- </option>
								<?php
								$UserTypeSQL = "SELECT 
													DISTINCT USER_TYPE , USER_DESP
												FROM 
													$schema.tb_lkp_user_type";

								$UserTypeSQLResult = $conn->query($UserTypeSQL);

								if ($UserTypeSQLResult->num_rows > 0) {
									// output data of each row
									while($row = $UserTypeSQLResult->fetch_assoc()) {
										echo "<option name='nm_UserType' value='".$row["USER_TYPE"]."'>".$row["USER_DESP"]."</option>";
									}
								}
								?>
                            </select>
                            <br><br>
                            <label><b>Airline: </b></label><br>
                            <select name="airline">
								<option disabled selected value> -- Select an Option -- </option>
								<?php
								$AirlineInfoSQL = "SELECT 
													DISTINCT AIRLINE_IATA_CD , AIRLINE_NAME
												FROM 
													$schema.tb_airline_info";

								$AirlineInfoSQLResult = $conn->query($AirlineInfoSQL);

								if ($AirlineInfoSQLResult->num_rows > 0) {
									// output data of each row
									while($row = $AirlineInfoSQLResult->fetch_assoc()) {
										echo "<option name='nm_AirlineInfo' value='".$row["AIRLINE_IATA_CD"]."'>".$row["AIRLINE_NAME"]."</option>";
									}
								}
								?>
                            </select>
                        <button type="submit" name="nm_submit" value="Submit">Submit</button>
                    </div>
                </form>
        </div>
<?php
}
?>
</body>
</html>

