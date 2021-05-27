<div class="reset-modal-background">
  <div class="reset-modal-content">

  	<div class="reset-modal-header">
  	  <div class="reset-modal-exit-button" onclick="hideResetModal()">+</div>
  	</div>

  	<div class="reset-modal-fields">
  	  <h2>Reset / Change Password</h2>
  	  <hr/>
      <div class="reset-modal-field-wrapper">
          <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
            <p> Enter your new password </p>
            <div class="reset-input-group">
              <input type="password" id="reset-input-password" name="reset-password" placeholder="Password" pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])\S{6,255}" required title="Password must be at least 6 characters, and contain a capital letter and a number">
              <input type="password" id="reset-input-confirm-password" name="reset-confirm-password" placeholder="Confirm Password" pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])\S{6,255}" required title="Password must be at least 6 characters, and contain a capital letter and a number">
            </div>

            <button type="submit" name="reset-password" id="reset-password-button" onclick="">Change Password</button>
          </form>
      </div>
  	</div>

  </div>
</div>

