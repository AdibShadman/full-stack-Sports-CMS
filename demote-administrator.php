<?php

include("./includes/initialize.php");

if((!isset($_POST["accountID"])) || !$account->isLoggedIn())
{
  redirect("./index.php");
}
else
{
  $account->setAccessLevel($_POST["accountID"], 2);
}

?>
  
  
       
    
   
      
      
      
   
 
 
    
    
   
    
 
     
     
  
    
      
        
        
         
