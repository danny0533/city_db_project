<?php 
	session_start();  
	
	// Database Connection
	include("mysql_connect.inc.php");

	if(isset($_POST["nm_Takeoff"])){ 
		$PostSchId	= $_POST["hd_SchId"];
?>
<html>
	<body>
		<?php
		$AirlineIATA = $_SESSION['AIRLINE_IATA_CD'];
		
		$SchReqInfoSQL = "	SELECT 
								SCH_ID, GATE_NO, SCH_DATE, SCH_TIME, SlotSch.REQ_ID, ReqInfo.IATA_CD, 
                                Airport.AIRPORT_NAME, Airport.LOCATION,  
                                AirlineInfo.AIRLINE_NAME , ReqInfo.USER_ID , ReqInfo.FLIGHT_ID , ReqInfo.STATUS
							FROM 
								$schema.tb_slot_sch SlotSch
							LEFT OUTER JOIN
								$schema.tb_req_info as ReqInfo 
							ON
								SlotSch.REQ_ID=ReqInfo.REQ_ID
							LEFT OUTER JOIN
								$schema.tb_airline_info as AirlineInfo 
							ON
								ReqInfo.AIRLINE_IATA_CD=AirlineInfo.AIRLINE_IATA_CD
							LEFT OUTER JOIN
								$schema.tb_lkp_airport as Airport
							ON
								ReqInfo.IATA_CD=Airport.IATA_CD
							WHERE
								SlotSch.SCH_ID=$PostSchId";

		$SchReqInfoSQLResult = $conn->query($SchReqInfoSQL);

		if ($SchReqInfoSQLResult->num_rows > 0) {
			// output data of each row
			while($row = $SchReqInfoSQLResult->fetch_assoc()) {
		?>
		<form action="<?php $_PHP_SELF ?>" id="fm_ConfReq" method="POST" name="fm_ConfReq">
			Schedule ID : 	<input type="text" name="tx_SchId" value="<?php echo $row["SCH_ID"]; ?>" readonly><br>
			Schedule Date: 	<input type="text" name="tx_SchDate" value="<?php echo $row["SCH_DATE"]; ?>" disabled><br>
			Schedule Time: 	<input type="text" name="tx_SchTime" value="<?php echo $row["SCH_TIME"]; ?>" disabled><br>
			Gate No: 		<input type="text" name="tx_GateNo" value="<?php echo $row["GATE_NO"]; ?>" disabled><br>
			Location : 		<input type="text" name="tx_IataCd" value="<?php echo $row["IATA_CD"]; ?>" disabled><br>
			Flight : 		<input type="text" name="tx_FlightId" value="<?php echo $row["FLIGHT_ID"]; ?>" disabled><br>
		<!--Request ID : --><input type="hidden" name="hd_ReqId" value="<?php echo $row["REQ_ID"] ?>">
			Take-Off Time:  <select name="sl_TakeoffTime">
								<?php
									$SchTime=new DateTime($row["SCH_TIME"]);
									$SchTime=$SchTime->format('H:i:s');
									$FMSchTime=date('H:i:s',strtotime($SchTime.'+15 minutes'));
									
									$Schdate=$row["SCH_DATE"];

									while ($SchTime < $FMSchTime) {
										$UniqTimeSQL = "SELECT 
															DISTINCT ReqInfo.TAKEOFF_TIME
														FROM 
															$schema.tb_req_info as ReqInfo 
														WHERE
															ReqInfo.REQ_ID IN (SELECT REQ_ID FROM $schema.tb_slot_sch SlotSch WHERE SCH_DATE='$Schdate')";
										
										$UniqTimeSQLResult = $conn->query($UniqTimeSQL);
										
										if ($UniqTimeSQLResult->num_rows > 0) {
											while($UniqTimerow = $UniqTimeSQLResult->fetch_assoc()) {
												if ( $SchTime == $UniqTimerow["TAKEOFF_TIME"] ) {
													$HaveFlightFlag='Y';
													break;
												} else {
													$HaveFlightFlag='N';
												}
											}
										}
										
										if ( $HaveFlightFlag == 'N' ) {
											echo "<option name='nm_TakeoffTime' value='".$SchTime."'>".$SchTime."</option>";
										}
										$SchTime=date('H:i:s',strtotime($SchTime.'+1 minutes'));  
									}
								?>
							</select>
								
			
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
	
	$ReqIdforUpdate = $_POST["hd_ReqId"];
	$TakeoffTimeforUpdate = $_POST["sl_TakeoffTime"];
	
	$UpdateTakeoffTimeSQL = "UPDATE 
								$schema.tb_req_info 
							SET 
								STATUS='Est $TakeoffTimeforUpdate , BoardingSoon',
								TAKEOFF_TIME='$TakeoffTimeforUpdate'
							WHERE 
								REQ_ID=$ReqIdforUpdate";
	
	if ($conn->query($UpdateTakeoffTimeSQL) === TRUE) {
				echo "Take-Off Time Submited <br>";
?>
				<a href="Depart_Sch.php">Back To Schedule Page</a>
<?php
	} else {
		echo "Fail";
	}
}
?>
<?php
$conn->close();
?>