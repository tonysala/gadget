<?php
include_once 'include/cmsheader.php';
session_destroy();
unset($_SESSION);
setcookie("cmsusername", "" , time()-60*60*24*365*20);
setcookie("cmspassword", "" , time()-60*60*24*365*20);
header("Location:cmslogin.php");
exit();
?>
