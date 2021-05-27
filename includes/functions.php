<?php

function redirect($url)
{
	header("location: " . $url);
	exit();
}

?>