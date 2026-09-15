<?php
session_start();
session_destroy();
setcookie('admin-id',false,time()-7*24*60*60);
setcookie('admin-username',false,time()-7*24*60*60);
setcookie('admin-name',false,time()-7*24*60*60);
header('location:index.php');
?>