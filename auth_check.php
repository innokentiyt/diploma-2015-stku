<?php

$current_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if(!isset( $_SESSION['user_id'] ) ) {
	header("Location: login.php?ref=$current_link");
}

?>