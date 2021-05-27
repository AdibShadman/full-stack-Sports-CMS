<?php
    require("./includes/initialize.php");

   if(isset($_POST["create-account"]))
    {
        $success = $account->register($_POST["given-name"], $_POST["family-name"], $_POST["organisation-name"], $_POST["email"], $_POST["password"]);
    }

    if(isset($_POST["signin-account"]))
    {
    	  $account->login($_POST["email"], $_POST["password"]);
    }

    if(isset($_POST["signout-account"]))
    {
        $account->logout();
    }

    if(isset($_POST["reset-password"]))
    {
        $account->changePassword($_SESSION['reset-email'], $_POST['reset-confirm-password']);
        unset($_SESSION['reset-email']);
    }

    if(isset($_GET['email']) && isset($_GET['token']))
    {
        if($account->tokenVerified($_GET['email'], $_GET['token']))
        {
            include("./includes/reset-password-modal.php");
            $_SESSION['reset-email'] = $_GET['email'];
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="A website devoted to rating players and teams in sports">
  <meta name="keywords" content="Sport, Rating, Match, Player, Team">
  <meta name="author" content="Grant Upson, Yusuf Uzun, James Watkins, Mingxin Wen, Marcus Grantham, Harry Singh, Adib Ornob"> 

  <link rel="stylesheet" href="./resources/css/styles.css">
  <link rel="stylesheet" href="./resources/css/jquery-ui.min.css">

<link rel="stylesheet" href="./resources/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  
  <link rel="icon" href="./resources/images/favicon.ico">

  <title> <?php echo $title; ?> </title>

</head>

<body>
