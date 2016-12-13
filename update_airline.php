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
<title>Update Airline</title>
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
                    <td>Airline ID :  </td>
                    <td>
                        <select name="select_id">
                            <option disabled selected value> -- Select an Option -- </option>
                            <?php
                            $aidSQL = "  SELECT
                                                AIRLINE_ID
                                            FROM
                                                $schema.tb_airline_info";
                            $aidSQLResult = $conn->query($aidSQL);
                            
                            while($row = $aidSQLResult->fetch_assoc()){
                                echo "<option name='nm_aid' value='".$row["AIRLINE_ID"]."'>".$row["AIRLINE_ID"]."</option>";
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
        $aid = $_POST['select_id'];
        $aInfoSQL = "SELECT
                			AIRLINE_ID, AIRLINE_IATA_CD, AIRLINE_NAME, TEL_NO
                		FROM 
                			$schema.tb_airline_info
                		WHERE
                		    AIRLINE_ID = $aid";
        $aInfoSQLResult = $conn->query($aInfoSQL);
        ?>
        <table>
            <?php while($row1 = $aInfoSQLResult->fetch_assoc()){?>
            
                <form name="ud_form" method="post" action="update_airline_process.php">
                    <tr><td><label><b>Airline ID : </b></label>
                    <input type="text" name="aid" value="<?php echo $row1["AIRLINE_ID"];?>" disabled></td></tr>
                    <tr><td><label><b>AIRLINE IATA CD : </b></label><br>
                    <input type="text" name="iata" value="<?php echo $row1["AIRLINE_IATA_CD"];?>"required></td></tr>
                    <tr><td><label><b>Airline Name : </b></label>
                    <input type="text" name="aname" value="<?php echo $row1["AIRLINE_NAME"];?>"required></td></tr>
                    <tr><td><label><b>Telephone : </b></label>
                    <input type="text" name="telephone" value="<?php echo $row1["TEL_NO"];?>"required></td></tr>

                    <tr><td><button type="submit" name="nm_submit" value="Submit">Update</button></td></tr>
                </form>
                
                <form name="rm_form" method="post" action="remove_airline.php">
                    <input type="hidden" name="rm_aid" value="<?php echo $row1["AIRLINE_ID"];?>">
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