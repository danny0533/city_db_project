<?php session_start();
	include("mysql_connect.inc.php");

	if(isset($_POST["nm_Update"])){ 
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
				$FlightSchDate=$row["SCH_DATE"];
		?>
		<form action="<?php $_PHP_SELF ?>" id="fm_ConfReq" method="POST" name="fm_ConfReq">
			Schedule ID : 	<input type="text" name="tx_SchId" value="<?php echo $row["SCH_ID"]; ?>" readonly><br>
			Schedule Date: 	<input type="text" name="tx_SchDate" value="<?php echo $row["SCH_DATE"]; ?>" disabled><br>
			Schedule Time: 	<input type="text" name="tx_SchTime" value="<?php echo $row["SCH_TIME"]; ?>" disabled><br>
			Gate No: 		<input type="text" name="tx_GateNo" value="<?php echo $row["GATE_NO"]; ?>" disabled><br>
			Location : 		<input type="text" name="tx_IataCd" value="<?php echo $row["IATA_CD"]; ?>" disabled><br>
		<!--Request ID : --><input type="hidden" name="hd_ReqId" value="<?php echo $row["REQ_ID"] ?>">
			Flight : 		<select name="sl_Flight">
								<?php
								
								echo "<option name='nm_Fligth' value='".$row["FLIGHT_ID"]."'>".$row["FLIGHT_ID"]."</option>";
								
								$FlightSQL = "	SELECT 
													AIRLINE_IATA_CD , FLIGHT_ID
												FROM 
													$schema.tb_flight_info
												WHERE
													AIRLINE_IATA_CD='$AirlineIATA'
												And FLIGHT_ID NOT IN (	SELECT
																			DISTINCT FLIGHT_ID
																		FROM
																			$schema.tb_req_info
																		WHERE 
																			REQ_ID IN (SELECT 
																							REQ_ID 
																						FROM 
																							$schema.tb_slot_sch
																						WHERE
																							SCH_DATE = '$FlightSchDate'
																						)
																	)";

								$FlightSQLResult = $conn->query($FlightSQL);

								if ($FlightSQLResult->num_rows > 0) {
									// output data of each row
									while($Flightrow = $FlightSQLResult->fetch_assoc()) {
										echo "<option name='nm_Fligth' value='".$Flightrow["FLIGHT_ID"]."'>".$Flightrow["FLIGHT_ID"]."</option>";
									}
								}
								?>
							</select><br>
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
		// Update Schedule Request ID
		$ReqIdforUpdate = $_POST["hd_ReqId"];
		$FlightIdforUpdate = $_POST["sl_Flight"];

		$UpdateReqSQL = "UPDATE 
							$schema.tb_req_info 
						SET 
							FLIGHT_ID='$FlightIdforUpdate'
						WHERE 
							REQ_ID=$ReqIdforUpdate";
							
		if ($conn->query($UpdateReqSQL) === TRUE) {
			echo "Schedule Updated <br>";
?>
			<a href="Depart_Sch.php">Back To Schedule Page</a>
<?php
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