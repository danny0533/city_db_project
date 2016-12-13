<?php
session_start();
include("mysql_connect.inc.php");
?>
<html>
<head>
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
<title>UserInfo</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="main.css" media="all" />
</head>
<body>
    <?php include("header.php");?>

<?php
if($_SESSION['USER_TYPE'] != 'RC' ){ 
        echo 'Please contact Ramp Contral!';
        echo '<meta http-equiv=REFRESH CONTENT=1;url=login.php>';
    }else{ ?>
        <table>
            <tr>
                <form action="<?php $_PHP_SELF ?>" method="post" name="fm_update">
                    <td>User ID :  </td>
                    <td>
                        <select name="select_id">
                            <option disabled selected value> -- Select an Option -- </option>
                            <?php
                            $UseridSQL = "  SELECT
                                                USER_ID
                                            FROM
                                                $schema.tb_user_info";
                            $UseridSQLResult = $conn->query($UseridSQL);
                            
                            while($row = $UseridSQLResult->fetch_assoc()){
                                echo "<option name='nm_userid' value='".$row["USER_ID"]."'>".$row["USER_ID"]."</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <button type="submit" name="fm_update" value="fm_update">Select</button>
                    </td>
                </form>
            </tr>
        </table><br><br>
<?php }
?>

<?php
if (isset($_POST['select_id'])){?>
<?php
        $userid = $_POST['select_id'];
        $UserInfoSQL = "SELECT
                			USER_ID, USER_NAME, TEL_NO, USER_TYPE, AIRLINE_IATA_CD
                		FROM 
                			$schema.tb_user_info
                		WHERE
                		    USER_ID = '$userid'";
        $UserInfoSQLResult = $conn->query($UserInfoSQL);
        ?>
        <table>
            <?php while($row1 = $UserInfoSQLResult->fetch_assoc()){?>
            
                <form name="form" method="post" action="update_alluser_process.php">
                    <tr><td><label><b>User ID : </b></label>
                    <input type="text" name="userid" value="<?php echo $row1["USER_ID"];?>" disabled></td></tr>
                    <tr><td><label><b>Username : </b></label>
                    <input type="text" name="username" value="<?php echo $row1["USER_NAME"];?>"required></td></tr>
                    <tr><td><label><b>Telephone : </b></label>
                    <input type="text" name="telephone" value="<?php echo $row1["TEL_NO"];?>"required></td></tr>
                    <tr><td><label><b>Usertype : </b></label><br>
                    <select name="usertype">
								<option name="nm_UserType" value="<?php echo $row1["USER_TYPE"];?>"><?php echo $row1["USER_TYPE"];?></option>
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
                            </select></td></tr>
                    <tr><td><label><b>Airline IATA : </b></label><br>
                    <select name="airline">
								<option name="nm_UserType" value="<?php echo $row1["AIRLINE_IATA_CD"];?>"><?php echo $row1["AIRLINE_IATA_CD"];?></option>
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
                    </select></td></tr>
                    
                    
                    <tr><td><button type="submit" name="nm_submit" value="Submit">Update</button></td></tr>
                    
                </form>
                <form name="rm_form" method="post" action="remove_user.php">
                    <input type="hidden" name="rm_userid" value="<?php echo $row1["USER_ID"];?>">
                    <td><button type="submit" class="cancelbtn" name="delete" value="Submit">Delete</button></td>
                </form>
            <?php}?>
        </table>
<?php
}
?>

<?php
}
?>

</body>
</html>