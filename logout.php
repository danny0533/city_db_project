<?php
session_start();
session_unset();
session_destroy(); 
?><p><font size="12"><?php echo 'Loging out....';?></font></p><?php
echo '<meta http-equiv=REFRESH CONTENT=1;url=index.php>';
?>