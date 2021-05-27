<div class = "initial-rating-border">
  <div class = "initial-rating-content">
    
    <div class = "initial-rating-wrapper">
      <div class ="initial-rating-header" >
      <div class = "initial-rating-exit-button" onclick = "hideInitialRatingModal()">+</div>
      </div>
      <div class = "initial-rating-body">
      <p>This player does not have an initial rating for this sport. Please put an initial rating.</p>
     <form method = "post"> 
        <div class="initial-rating-content">

          <select class = "player-initial-rating" name ="player-initial-rating" id="player-initial-rating" onclick="prefillTextbox()">
          <option value = "250"> Beginner</option>
          <option value = "500"> Intermediate</option>
          <option value = "1000"> Advanced</option>
          </select> 
        
          <input type = "hidden" id = "hidden-sport-ID"/> 
           <input type= "hidden" id = "hidden-player-ID"/> 
        <input type= "text" id = "initial-mean-ID" name="initial-mean-name" placeholder = "Initial Rating " value = "250"/>+/-
        <input type= "text" id = "initial-sd-ID" name="initial-sd-name" placeholder = "Standard deviation" value = "100"/><br/>
          </div>
        <button type = "button" name = "initial-rating-button" id="initial-rating-button-ID" onclick = "addRating()">Add Rating</button>
        
     </form> 
      </div>
    </div>
  </div>
</div>