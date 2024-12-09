<?php
//clears all session variables that are currently registered , this function only works if a session is active
 session_unset();
 //removes the session data from the server
 session_destroy();
 //redirects the user to the welcome.php page after the session has been cleared and destroyed
 header("Location: welcome.php");
 exit;
?>