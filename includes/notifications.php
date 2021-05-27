<?php

if(isset($_POST["reset-password"]))
{
	echo "<script> showNotificationModal('Password Reset', 'Your password has been reset successfully!') </script>";
}

if(isset($_POST["create-account"]))
{
	echo "<script> showNotificationModal('Account Creation', 'Your account has been created successfully! You will receive an email once your account has been activated.') </script>";
}

if(isset($_SESSION["account-inactive"]))
{
	echo "<script> showNotificationModal('Account Inactive', 'Your account is currently locked and is not active. If you believe this is an error contact an administrator.') </script>";
	 	unset($_SESSION['account-inactive']);
}

if(isset($_SESSION["login-incorrect"]))
{
	echo "<script> showNotificationModal('Login Details Incorrect', 'Your username or password is incorrect.') </script>";
	unset($_SESSION['login-incorrect']);
}

if(isset($_SESSION["upload-success"]))
{
	echo "<script> showNotificationModal('Event Upload Successfull', 'The rankings for players will now be calculated. It may take a few moments before the results are ready.') </script>";
	unset($_SESSION['upload-success']);
}

if(isset($_SESSION['club-exp-name']))
{
	echo "<script> showNotificationModal('Club Expiry Set', 'The expiry date for the club " . $_SESSION['club-exp-name'] . " has been updated.') </script>";
	unset($_SESSION['club-exp-name']);
}

if(isset($_SESSION['club-exp']))
{
	echo "<script> showNotificationModal('Subscription Expired', 'Your club\'s subscription has expired. Please contact us to renew. Your subscription expired on " . $_SESSION['club-exp'] . ". You will be unable to upload events until your subscription is renewed.') </script>";
	unset($_SESSION['club-exp']);
}

if (isset($_POST['editEventID']))
{
	echo "<script> showNotificationModal('Editing Event', 'Be advised you are now editing an event. This should only be done when an error has been found and the event is less than a few days old.') </script>";
}

if(isset($_POST["update-account-details"]))
{
	echo "<script> showNotificationModal('Account Details Updated', 'Your account details have now been updated..') </script>";
}

?>
