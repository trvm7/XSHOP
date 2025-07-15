<?php
session_start();
session_destroy();
header("Location: mine.php");
exit();
?>