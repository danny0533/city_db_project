<?php 
		include("mysql_connect.inc.php");
		// Auto Genarate Schedule After 9 Month
			
		$Date9Mth=new DateTime;
		$Date9Mth->setDate(2016,12,5);
		$vrDate9Mth=$Date9Mth->format('Y-m-d');
		
		$DefaultDate = new DateTime;
		$DefaultDate->setDate(2016,12,1);
		$vrDefaultDate=$DefaultDate->format('Y-m-d');
		
		echo $vrDate9Mth."<br>";
		echo $vrDefaultDate."<br>";
		
		$MaxSchId=0;

		$MaxTime = new DateTime;
		$MaxTime->setTime(23,45,0);

		while ($vrDefaultDate <= $vrDate9Mth){
			echo $vrDefaultDate."<br>";
			$DefaultTime = new DateTime;
			$DefaultTime->setTime(0,0,0);
			
			while ($DefaultTime <= $MaxTime ) {
				$vrDefaultTime=$DefaultTime->format('H:i:s');
				echo $vrDefaultTime."<br>";
				
				$NEW_SLOT_NO = 1;
				while ($NEW_SLOT_NO <= 5 ) {
					$MaxSchId++;
					echo $MaxSchId."<br>";
					$SCH_INSERT_SQL = "	INSERT INTO $schema.tb_slot_sch 
											(SCH_ID,GATE_NO,SCH_DATE,SCH_TIME,REQ_ID)
										VALUES 
											( '$MaxSchId' , 
											'$NEW_SLOT_NO' , 
											'$vrDefaultDate' , 
											'$vrDefaultTime' , 
											NULL )";
											
					if ($conn->query($SCH_INSERT_SQL) === TRUE) {
						echo "";
					} else {
						echo "Error: " . $SCH_INSERT_SQL . "<br>" . $conn->error;
					}
					
					$NEW_SLOT_NO++;
				}
				$DefaultTime->modify('+15 minutes');
			}
			
		$vrDefaultDate=date('Y-m-d',strtotime($vrDefaultDate.'+1 days'));
		}
?>
<?php
$conn->close();
?>