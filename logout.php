<?php
include_once 'include/header.php';
session_destroy();
session_unset();
unset($_SESSION);
setcookie("username", "" , time()-60*60*24*365*20);
setcookie("password", "" , time()-60*60*24*365*20);
header("Location:login.php?logout");
exit();
include_once 'include/footer.php';
?>
