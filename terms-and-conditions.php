<?php 
    $title = "Peterman Ratings | Terms and Conditions";

    include("./includes/header.php");
    include("./includes/navigation.php");
?>

<article class="aboutUs-privacy-and-terms-articles">
  
<?php
	echo nl2br( file_get_contents('./editable/terms-and-conditions.html') );
?>

</article>

<?php
    include("./includes/footer.php");
?>
