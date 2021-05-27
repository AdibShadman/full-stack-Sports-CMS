<?php
  $title = "Peterman Ratings | Administration";
  
  include("./includes/header.php");
  include("./includes/navigation.php");

  if(!($account->isLoggedIn() && $account->getAccessLevel() == 0))
  {
  	redirect("./index.php");
  }
?>

<article>
  
  
</article>

<?php
  include("./includes/footer.php");
?>
    
      
    
    
       
 
    
    
  
    
      
 
     
       
  
  
       
    
   
      
      
      
   
 
 
    
    
   
    
 
     
     
  
    
      
        
        
         
