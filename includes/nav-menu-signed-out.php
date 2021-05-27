<div class="dropdown-menu">
  <div class="dropdown-header">
    <h3>Sign in to your Account</h3>
  </div>   
  <div class="dropdown-signin-fields">
    <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
      <input type="email" id="signin-email" name="email" placeholder="Email" required pattern="{1,75}">
      <input type="password" id="signin-password" name="password" placeholder="Password" pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])\S{1,255}" required>
      <a href="#" class="forgot-password" onclick="showPasswordModal()">Forgotten Password?</a>
      <button type="submit" name="signin-account" class="signin-account-button" onclick="">Sign In</button>
    </form>
    <div class="create-account-wrapper">
      <p>Not a member yet?</p>
    <a href="#" class="create-account-modal" onclick="showRegisterModal()">Create Account</a>
    </div>
  </div>
</div>