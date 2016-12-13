<?php session_start(); 
if(isset($_SESSION['username']))
{
include("mysql_connect.inc.php");
?>
<html>
	<head>
		<style>
			
			input{visibility:hidden;}
		</style>
		<?php
			// posted data.
			
			$oriSchID = $_POST['hd_SchId'];
			//$newSchID = $_POST['hd_NewSchId'];
			//$reqID = $_POST['hd_reqID'];
			
			$requestType = $_POST['hd_RequestType'];
			$AirlineIATA=$_SESSION['AIRLINE_IATA_CD'];
			// k+
			$userType = $_SESSION['user_type'];
			$isRC = $userType == "RC";
	
		?>
	</head>
	<body>
		<div>
			<h2>
				Approving Departure Slot Exchanging
			</h2>
			
			Updating...
		</div>
		
		<?php
		if($requestType == 'EARLIER'){
		
			?>
		
		<form action="Request_Earlier_Slot.php" id="fm_RequestExchangeEarlier" method="post" name="fm_RequestExchangeEarlier">
            <input type="hidden" name="hd_SchId" value="<?php echo $oriSchID ?>">
            <input type="submit" value="Request Earlier Slot" name="nm_Request">
        </form>
        <?php
		}else{
			?>
					<form action="Request_Later_Slot.php" id="fm_RequestExchangeLater" method="post" name="fm_RequestExchangeLater">
            <input type="hidden" name="hd_SchId" value="<?php echo $oriSchID ?>">
            <input type="submit" value="Request Later Slot" name="nm_Request" >
        </form>
			<?php
		}
             ?>           
		<?php
				// get origin slot info
				$sql = "SELECT  ss.SCH_DATE, ss.SCH_TIME, ri.FLIGHT_ID , ri.STATUS AS STATUS, Airport.AIRPORT_NAME, Airport.LOCATION,  
                                AirlineInfo.AIRLINE_NAME  , ss.GATE_NO,
								UserInfo.USER_NAME, ri.REQ_ID
                FROM $schema.tb_slot_sch ss 
				INNER JOIN $schema.tb_req_info ri  on  ss.req_id = ri.req_id 
				LEFT OUTER JOIN
								$schema.tb_airline_info as AirlineInfo 
							ON
								ri.AIRLINE_IATA_CD=AirlineInfo.AIRLINE_IATA_CD
							LEFT OUTER JOIN
								$schema.tb_lkp_airport as Airport
							ON
								ri.IATA_CD=Airport.IATA_CD
							LEFT OUTER JOIN 
								$schema.tb_user_info as UserInfo
							ON
								UserInfo.USER_ID=ri.USER_ID
			
				WHERE ss.SCH_ID = ".$oriSchID;
				$sqlResult = $conn->query($sql);
				if ($sqlResult->num_rows > 0) {
					while($row = $sqlResult->fetch_assoc()) {
						$reqID = $row['REQ_ID'];
					}
				}
				
				// get log
				$sql = "SELECT * FROM $schema.tb_exchange_slot_log WHERE ORI_SCH_ID = ".$oriSchID;
				// echo $sql;
				$sqlResult = $conn->query($sql);
				if ($sqlResult->num_rows > 0) {
					while($row = $sqlResult->fetch_assoc()) {
						// $oriSchID = $row['ORI_SCH_ID'];
						$newSchID = $row['CUR_ASSIGN_SCH_ID'];
					}
				}
				
				// assign new sch record with req info
				$sql = "UPDATE $schema.tb_slot_sch set req_id = ".$reqID." WHERE SCH_ID = ".$newSchID;
				// echo $sql;
				if ($conn->query($sql) === TRUE) {
				    $isSuccess = TRUE;
				}else{
				    echo "Error updating record: " . $conn->error;
				}
				
				
				// empty ori sch record
				$sql = "UPDATE $schema.tb_slot_sch set req_id = null WHERE SCH_ID = ".$oriSchID;
				// echo $sql;
				if ($conn->query($sql) === TRUE) {
				    $isSuccess = TRUE;
				}else{
				    echo "Error updating record: " . $conn->error;
				}
				
				// empty ori req record
				$sql = "UPDATE $schema.tb_req_info set STATUS = null, TAKEOFF_TIME = NULL WHERE REQ_ID = ".$reqID;
				// echo $sql;
				if ($conn->query($sql) === TRUE) {
				    $isSuccess = TRUE;
				}else{
				    echo "Error updating record: " . $conn->error;
				}
				
				
				
				// update current status in exchange log tb
				$sql = "UPDATE $schema.tb_exchange_slot_log set IS_APPROVED = 1, STATUS = 'Exchanged' WHERE ORI_SCH_ID = ".$oriSchID;
				if ($conn->query($sql) === TRUE) {
				    $isSuccess = TRUE;
				}else{
				    echo "Error updating record: " . $conn->error;
				}
				
				
				// delete log records 
				// $sql = "DELETE FROM $schema.tb_exchange_slot_log WHERE ORI_SCH_ID = ".$oriSchID;
				// if ($conn->query($sql) === TRUE) {
				//     $isSuccess = TRUE;
				// }else{
				//     echo "Error updating record: " . $conn->error;
				// }
			
		if($isSuccess){
			echo '<script type="text/javascript">
        		window.location = "Depart_Slot.php";
    		</script>';
		}
		
		?>
	
		
	</body>
</html>
                 
 <?php
$conn->close();
}
else
{
    echo 'Please Use Login Page';
    echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
}
?>
