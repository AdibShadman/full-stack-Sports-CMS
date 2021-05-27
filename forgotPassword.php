<?php
  $title = "Peterman Ratings | Forgot Password";
  
  include("./includes/header.php");
  include("./includes/navigation.php");

  if(isset($_POST['resetPassword']))
  {
      $email = $_POST['resetPassword'];

      if($account->emailExists($email))
      {
          $token = "aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ1234567890";
          $token = str_shuffle($token);
          $token = substr($token, 0, 25);
          $account->setToken($email, $token);
          $account->sendRecoveryEmail($email, $token);
      }
  }
  else
  {
      redirect("index.php");
  }

?>


    
  
    
      
 
     
       
  
  
       
    
   
      
      
      
   
 
 
    
    
   
    
 
     
     
  
    
      
        
        
         
