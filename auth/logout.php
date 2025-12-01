<?php
session_start();
session_destroy();
header('Location: /attendance-management-system/auth/login.php');
exit;
?>
