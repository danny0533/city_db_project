<?php 
session_start();

if(isset($_SESSION['username']))
{
	include("mysql_connect.inc.php");
	include("Depart_Status.php");
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
			
			table > tbody tr:hover{
				 background-color: #f5f5f5
			}
			
			#fm_Reset{
				margin-top:15px;
			}
		</style>
			<script>
		function startTime() {
		    var today = new Date();
		    var h = today.getHours();
		    var m = today.getMinutes();
		    var s = today.getSeconds();
		    m = checkTime(m);
		    s = checkTime(s);
		    document.getElementById('divWatch').innerHTML = h + ":" + m + ":" + s;
		    var t = setTimeout(startTime, 500);
		}
		
		function checkTime(i) {
		    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
		    return i;
		}
		</script>
		
		<a href="update_form.php">
			<?php 
			include("header.php");
			$AirlineIATA=$_SESSION['AIRLINE_IATA_CD'];
			$userType = $_SESSION['USER_TYPE'];
			// echo $userType;
			$isSM = $userType == "SM";
			$isRC = $userType == "RC";
			// echo $isRC;
			?>
		</a>
	</head>
	<body onload="startTime();">

		<?php
		date_default_timezone_set('Asia/Hong_Kong');
		$date = date('Y-m-d H:i:s');
		echo "Current Time : <div id='divWatch'>".$date."</div>";
		?>
		
		<!--disable search for station manager-->
		<?php 
			if(!$isSM){
		?>		
		
		<!-- Table for Search Engine -->
		<table>
			<tr>
				<form action="<?php $_PHP_SELF ?>" id="fm_Gate" method="post" name="fm_Gate">
				<td> Date : </td>
				<td>
					<!-- Search By Date -->
					<select name="sl_Date">
						<option disabled selected value> -- Select an Option -- </option>
						<?php
						$SearchDateIATA=$_SESSION['AIRLINE_IATA_CD'];
						
						$SchDateSQL = "	SELECT 
											DISTINCT SCH_DATE
										FROM 
											$schema.tb_slot_sch";

						$SchDateSQLResult = $conn->query($SchDateSQL);

						if ($SchDateSQLResult->num_rows > 0) {
							// output data of each row
							while($row = $SchDateSQLResult->fetch_assoc()) {
								echo "<option name='nm_Date' value='".$row["SCH_DATE"]."'>".$row["SCH_DATE"]."</option>";
							}
						}
						?>
					</select>
				</td>
				<td> Time : </td>
				<td>
					<!-- Search By Airline -->
					<select name="sl_Time">
						<option disabled selected value> -- Select an Option -- </option>
						<?php
						$SchTimeSQL = "	SELECT 
											DISTINCT SCH_TIME
										FROM 
											$schema.tb_slot_sch";

						$SchTimeSQLResult = $conn->query($SchTimeSQL);

						if ($SchTimeSQLResult->num_rows > 0) {
							// output data of each row
							while($row = $SchTimeSQLResult->fetch_assoc()) {
								echo "<option name='nm_Time' value='".$row["SCH_TIME"]."'>".$row["SCH_TIME"]."</option>";
							}
						}
						?>
					</select>
				</td>
				<td> Airline : </td>
				<td>
					<!-- Search By Airline -->
					<select name="sl_Airline">
						<option disabled selected value> -- Select an Option -- </option>
						<?php
						$SchAirlineSQL = "	SELECT 
											DISTINCT AIRLINE_IATA_CD , AIRLINE_NAME
										FROM 
											$schema.tb_airline_info";

						$SchAirlineSQLResult = $conn->query($SchAirlineSQL);

						if ($SchAirlineSQLResult->num_rows > 0) {
							// output data of each row
							while($row = $SchAirlineSQLResult->fetch_assoc()) {
								echo "<option name='nm_Airline' value='".$row["AIRLINE_IATA_CD"]."'>".$row["AIRLINE_IATA_CD"]."</option>";
							}
						}
						?>r
					</select>
				</td>
				<td>
					<input type="submit" value="Submit">
				</td>
				</form>
				<td>
					<!-- Reset Button -->
					<form action="<?php $_PHP_SELF ?>" id="fm_Reset" method="post" name="fm_Reset">
						<input type="submit" value="Reset" name="nm_Reset">
					</form>
				</td>
			</tr>
		</table>
		
		<?php 
			//disable search for sm
			}
		?>
		
		<!-- Table for Airline Slot Schedule -->
		<table>
			<caption><h2>Airline Slot Schedule</h2></caption>
			<tr>
				<th>Date</th>
				<th>Time</th>
				<th>Flight</th>
				<th>Destination</th>
				<th>Gate</th>
				<th>Airline</th>
				<th>Status</th>
				<th>Exchanging Status</th>
				<th>Submit By</th>
				<th>Schedule Request</th>
				<th>Update</th>
				<th>Take-Off Request</th>
				<th>Cancel</th>
			</tr>
			
			<?php
			// Set Sub Query for select data
			if(isset($_POST["nm_Reset"])){ 
				if($isSM){
					$SchDataSubQuery = "WHERE ReqInfo.AIRLINE_IATA_CD='$AirlineIATA'";
				}else{
					$SchDataSubQuery = "WHERE 1=1";
				}
			} else {
				if(isset($_POST["sl_Date"])){
					$postDate=$_POST["sl_Date"];
					$SchDataSubQuery = "WHERE SCH_DATE='$postDate'";
				} elseif(isset($_POST["sl_Time"])){
					$postTime=$_POST["sl_Time"];
					$SchDataSubQuery = "WHERE SCH_TIME='$postTime'";
				} elseif(isset($_POST["sl_Airline"])){
					$postAirline=$_POST["sl_Airline"];
					$SchDataSubQuery = "WHERE ReqInfo.AIRLINE_IATA_CD='$postAirline'";
				} else {
					if($isSM){
						$SchDataSubQuery = "WHERE ReqInfo.AIRLINE_IATA_CD='$AirlineIATA'";
					}else{
						$SchDataSubQuery = "WHERE 1=1";
					}
				}
			}
			
			$NowDate=date("Y-m-d" , strtotime( '-1 days' ));
			
			$SchDataSQL = "	SELECT 
								SCH_ID, GATE_NO, SCH_DATE, SCH_TIME, SlotSch.REQ_ID, ReqInfo.IATA_CD, 
                                Airport.AIRPORT_NAME, Airport.LOCATION,  
                                AirlineInfo.AIRLINE_NAME , 
								ReqInfo.USER_ID , ReqInfo.FLIGHT_ID, ReqInfo.AIRLINE_IATA_CD,ReqInfo.STATUS,ReqInfo.TAKEOFF_TIME,
								UserInfo.USER_NAME,
								CASE WHEN 
									CURDATE() = SCH_DATE AND CURTIME() BETWEEN DATE_ADD(SCH_TIME,INTERVAL -2 HOUR) AND SCH_TIME 
								THEN 
									'Y'
								ELSE
									'N'
								END AS TAKEOFF_FLAG , 
								CASE WHEN 
									SCH_DATE > CURDATE()
								THEN 
									'Y'
								ELSE
									'N'
								END AS CURDATE_FLAG , 
								CASE WHEN 
									SCH_DATE = CURDATE() And SCH_TIME >= CURTIME()
								THEN 
									'Y'
								ELSE
									'N'
								END AS CURTIME_FLAG ,
								CASE WHEN
									ExchangeLog.STATUS = 'Exchanging Earlier Slot' OR ExchangeLog.STATUS = 'Exchanging Later Slot'
								THEN
									'Exchanging'
								ELSE
									ExchangeLog.STATUS
								END As Exchanging_FLAG,
								ExchangeLog.IS_APPROVED,
								ExchangeLog.STATUS as ExchangeStatus,
								CURTIME() as CURR
							FROM 
								$schema.tb_slot_sch SlotSch
							LEFT OUTER JOIN
								$schema.tb_req_info as ReqInfo 
							ON
								SlotSch.REQ_ID=ReqInfo.REQ_ID
							LEFT OUTER JOIN
								$schema.tb_exchange_slot_log as ExchangeLog
							ON
								ExchangeLog.Ori_Sch_ID = SlotSch.Sch_ID
							LEFT OUTER JOIN
								$schema.tb_airline_info as AirlineInfo 
							ON
								ReqInfo.AIRLINE_IATA_CD=AirlineInfo.AIRLINE_IATA_CD
							LEFT OUTER JOIN
								$schema.tb_lkp_airport as Airport
							ON
								ReqInfo.IATA_CD=Airport.IATA_CD
							LEFT OUTER JOIN 
								$schema.tb_user_info as UserInfo
							ON
								UserInfo.USER_ID=ReqInfo.USER_ID
							$SchDataSubQuery
							And ((SlotSch.SCH_DATE = CURDATE() And (DATE_ADD(SCH_TIME,INTERVAL -2 HOUR) >= CURTIME() Or SlotSch.REQ_ID IS NOT NULL))
							Or (SlotSch.SCH_DATE = '$NowDate' And SlotSch.REQ_ID IS NOT NULL )
							Or SlotSch.SCH_DATE > CURDATE())
							ORDER BY 
								SCH_DATE , SCH_TIME , GATE_NO , SCH_ID";
								
			$SchDataSQLResult = $conn->query($SchDataSQL);

			if ($SchDataSQLResult->num_rows > 0) {
				// output data of each row

				while($row = $SchDataSQLResult->fetch_assoc()) {
					$CheckReq=$row["REQ_ID"];
					$CancelTag=$row["STATUS"];
					$isExchanging = $row["Exchanging_FLAG"] == 'Exchanging';
					$isAccepted = 
					$hasExchangeApproval = $row['Exchanging_FLAG'] == 'Accepted';
					
					echo "<tr><td>".$row["SCH_DATE"]."</td>";
					echo "    <td>".$row["SCH_TIME"]."</td>";
					echo "    <td>".$row["FLIGHT_ID"]."</td>";
					echo "    <td>".$row["LOCATION"]."</td>";
					echo "    <td>".$row["GATE_NO"]."</td>";
					echo "    <td>".$row["AIRLINE_NAME"]."</td>";
					echo "    <td>".$row["STATUS"]."</td>";
                    echo "    <td>".$row["Exchanging_FLAG"]."</td>";
                    
					// Check Masked
					if (( $row["CURDATE_FLAG"] == 'Y' || $row["CURTIME_FLAG"] == 'Y') ) {
						if ( $CancelTag == 'Canceled' ) {
							if ( "$AirlineIATA" == $row["AIRLINE_IATA_CD"] ){
								echo "<td>".$row["USER_NAME"]."</td>";
							} else {
								echo "<td></td>";
							}
							echo "<td></td>";
							echo "<td></td>";
							echo "<td></td>";
							echo "<td></td>";
						} elseif ( empty($CheckReq)) {
							echo "<td></td>";
							echo "    <td>"; 
							if ( !$isRC ) {?>
								<form action="Request_Sch.php" id="fm_Request" method="post" name="fm_Request">
									<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
									<input type="submit" value="Request" name="nm_Request">
								</form>
					<?php	}
							echo "	  </td>";
							echo "<td></td>";
							echo "<td></td>";
							echo "<td></td>";
						}elseif ( "$AirlineIATA" == $row["AIRLINE_IATA_CD"] || $isRC ){
								// Masked User Name
								echo "<td>".$row["USER_NAME"]."</td>";
								// Masked Button Function
								echo "<td></td>";
								echo "    <td>"; 
								if ( !$isRC ) {
									if (!$isSM && $row["TAKEOFF_FLAG"] == 'N' ){?>
										<form action="Update_Sch.php" id="fm_Update" method="post" name="Update">
											<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
											<input type="submit" value="Update" name="nm_Update">
										</form>
						<?php		}
								}
								echo "    </td><td>";
									if ( $row["TAKEOFF_FLAG"] == 'Y'){
										if (empty($row["TAKEOFF_TIME"]) & !$isRC){
										?>
										<form action="Takeoff.php" id="fm_Takeoff" method="post" name="Takeoff">
											<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
											<input type="submit" value="Takeoff" name="nm_Takeoff">
										</form>
						<?php			} elseif($isSM == 1 && $isExchanging != 1 ){ // SM Function 
											?>
												<form action="Request_Earlier_Slot.php" id="fm_RequestExchangeEarlier" method="post" name="fm_RequestExchangeEarlier">
													<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
													<input type="submit" value="Request Earlier Slot" name="nm_Request">
												</form>
												<form action="Request_Later_Slot.php" id="fm_RequestExchangeLater" method="post" name="fm_RequestExchangeLater">
													<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
													<input type="submit" value="Request Later Slot" name="nm_Request">
												</form>
											<?php 
											}elseif ($isSM == 1 && ($hasExchangeApproval || $isExchanging)){
												?>
												 <form action="Withdraw_Exchange.php" id="fm_WithdrawExchange" method="post" name="fm_WithdrawExchange">
													<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
													<input type="submit" value="Withdraw Exchange" name="btnWithdrawExchange">
												</form>
												<?php
											}
											if($isRC && $hasExchangeApproval == 1){
												?>
												 <form action="Approve_Exchange.php" id="fm_ApproveExchange" method="post" name="fm_ApproveExchange">
													<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
													<input type="submit" value="Approve Exchange" name="btnApproveExchange">
												</form>
													 <form action="Reject_Exchange.php" id="fm_RejectExchange" method="post" name="fm_RejectExchange">
													<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
													<input type="submit" value="Reject Exchange" name="btnRejectExchange">
												</form>
												<?php
											}
									}
									
								echo "    </td><td>"; 
								IF (!$isRC) {
									if (!$isSM){ ?>	
										<form action="Remove_Sch.php" id="fm_Remove" method="post" name="Remove">
											<input type="hidden" name="hd_SchId" value="<?php echo $row["SCH_ID"] ?>">
											<input type="submit" value="Cancel" name="nm_Remove">
										</form>
							<?php	}	}
								echo "	  </td>";
							} else {
								echo "<td></td>";
								echo "<td></td>";
							    echo "<td></td>";
								echo "<td></td>";
								echo "<td></td>";
							}
					} else {
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
					}
					echo "</tr>";
				}
			}
			?>
		</table>
	</body>
</html>

<?php
$conn->close();
?>
<?php
}
else
{
    echo 'Please Use Login Page';
    echo '<meta http-equiv=REFRESH CONTENT=2;url=login.php>';
}
?>