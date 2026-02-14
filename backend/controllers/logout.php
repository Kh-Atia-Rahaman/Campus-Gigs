<?php
session_start();
session_destroy();
header("Location: ../../frontend/pages/Login.html?logout=1");
exit;
?>

// Navigation redirects updated
