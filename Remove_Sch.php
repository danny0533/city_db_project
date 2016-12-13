<?php session_start(); ?>
<?php // Database Connection
	include("mysql_connect.inc.php");
?>
<?php
	if(isset($_POST["nm_Remove"])){ 
		$PostSchId	= $_POST["hd_SchId"];
?>
<html>
	<body>
		<?php
		$AirlineIATA = $_SESSION['AIRLINE_IATA_CD'];
		
		$SchReqInfoSQL = "	SELECT 
								SCH_ID, GATE_NO, SCH_DATE, SCH_TIME, SlotSch.REQ_ID , 
								Airport.LOCATION,
								ReqInfo.FLIGHT_ID
							FROM 
								$schema.tb_slot_sch SlotSch
							LEFT OUTER JOIN
								$schema.tb_req_info as ReqInfo 
							ON
								SlotSch.REQ_ID=ReqInfo.REQ_ID
							LEFT OUTER JOIN
								$schema.tb_lkp_airport as Airport
							ON
								ReqInfo.IATA_CD=Airport.IATA_CD
							WHERE
								SCH_ID=$PostSchId";

		$SchReqInfoSQLResult = $conn->query($SchReqInfoSQL);

		if ($SchReqInfoSQLResult->num_rows > 0) {
			// output data of each row
			while($row = $SchReqInfoSQLResult->fetch_assoc()) {
		?>
		<form action="<?php $_PHP_SELF ?>" id="fm_ConfReq" method="POST" name="fm_ConfReq">
							<input type="hidden" name="hd_ReqId" value="<?php echo $row["REQ_ID"] ?>">
			Schedule ID : 	<input type="text" name="tx_SchId" value="<?php echo $row["SCH_ID"]; ?>" readonly><br>
			Schedule Date: 	<input type="text" name="tx_SchDate" value="<?php echo $row["SCH_DATE"]; ?>" readonly><br>
			Schedule Time: 	<input type="text" name="tx_SchTime" value="<?php echo $row["SCH_TIME"]; ?>" readonly><br>
			Gate No: 		<input type="text" name="tx_GateNo" value="<?php echo $row["GATE_NO"]; ?>" readonly><br>
			Location : 		<input type="text" name="tx_Location" value="<?php echo $row["LOCATION"]; ?>" readonly><br>
			Flight : 		<input type="text" name="tx_Location" value="<?php echo $row["FLIGHT_ID"]; ?>" readonly><br>
			<br>
			<input type="submit" value="Confirm" name="nm_ConfirmReq">
		</form>
		<?php 
			}
		}
		?>
	</body>
</html>
<?php
} elseif(isset($_POST["nm_ConfirmReq"])) {
	// Remove Rquest Information
	$NowDate=date('Y-m-d h:i:s');
	$SchIdforUpdate = $_POST["tx_SchId"];
	$ReqIdforUpdate = $_POST["hd_ReqId"];

	// Get Max Schedule ID
	$MaxSchIdSQL = "	SELECT 
						MAX(SCH_ID) AS MAX_SCH_ID
					FROM 
						$schema.tb_slot_sch";
						
	$MaxSchIdSQLResult = $conn->query($MaxSchIdSQL);

	if ($MaxSchIdSQLResult->num_rows > 0) {
		while($row = $MaxSchIdSQLResult->fetch_assoc()) {
			$MaxSchId=$row["MAX_SCH_ID"]+1;
		}
	}
	
	$UpdateReqSQL = "UPDATE 
						$schema.tb_req_info 
					SET 
						STATUS='Canceled' ,
						LAST_UPDATE_TIME='$NowDate'
					WHERE 
						REQ_ID=$ReqIdforUpdate";
							
		if ($conn->query($UpdateReqSQL) === TRUE ) {
			$postGateNo=$_POST["tx_GateNo"];
			$postSchDate=$_POST["tx_SchDate"];
			$postSchTime=$_POST["tx_SchTime"];

			$InsertNewSchSQL = "INSERT INTO $schema.tb_slot_sch 
									(SCH_ID,GATE_NO,SCH_DATE,SCH_TIME,REQ_ID)
								VALUES 
									( '$MaxSchId' , 
									'$postGateNo' , 
									'$postSchDate' , 
									'$postSchTime' , 
									NULL )";
							
			if ($conn->query($InsertNewSchSQL) === TRUE) {
			echo "Schedule Removed <br>";
?>
			<a href="Depart_Slot.php">Back To Slot Page</a>
<?php
			} else {
				echo "Error: " . $InsertNewSchSQL . "<br>" . $conn->error;
			}
		} else {
			echo "Error: " . $UpdateReqSQL . "<br>" . $conn->error;
		}
	} else {
	echo "b";
}
?>
<?php
$conn->close();
?>