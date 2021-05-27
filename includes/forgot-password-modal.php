<div class="password-modal-background">
  <div class="password-modal-content">

  	<div class="password-modal-header">
  	  <div class="password-modal-exit-button" onclick="hidePasswordModal()">+</div>
  	</div>

  	<div class="password-modal-fields">
  	  <h2>Forgot Password</h2>
  	  <hr/>
      <div class="password-modal-field-wrapper">
          <p>Enter your email address and we'll send you a password reset email</p>
          <div id="email-sent">Email sent!</div>

          <div class="password-input-group">
            <input type="email" id="password-input-email" name="email" placeholder="Email" pattern="{7,75}" required title="Email must not exceed 75 characters"> 
          </div>

          <button type="button" name="reset-password" id="reset-password-button" onclick="resetPassword()">Reset Password</button>
      </div>
  	</div>

  </div>
</div>

