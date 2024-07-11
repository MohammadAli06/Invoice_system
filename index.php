<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice System Login</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'>
  <link rel="stylesheet" href="./assets/css/index.css">
</head>
<body>
<?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
<div class="container" id="container">
  <div class="form-container sign-up-container">
    <form action="login.php" method="POST">
      <h1>User Login</h1>
      <span></span>
      <input type="text" name="username" placeholder="username" required />
      <input type="password" name="password" placeholder="password" required />
      <input type="hidden" name="role" value="user" />
      <button type="submit">Sign In</button>
    </form>
  </div>
  <div class="form-container sign-in-container">
    <form action="login.php" method="POST">
      <h1>Admin Login</h1>
      <span></span>
      <input type="text" name="username" placeholder="username" required />
      <input type="password" name="password" placeholder="password" required />
      <input type="hidden" name="role" value="admin" />
      <button type="submit">Sign In</button>
    </form>
  </div>
  <div class="overlay-container">
    <div class="overlay">
      <div class="overlay-panel overlay-left">
        <h1>Welcome Back!</h1>
        <p>Login As An Admin</p>
        <button class="ghost" id="signIn">Sign In</button>
      </div>
      <div class="overlay-panel overlay-right">
        <h1>Welcome Back!</h1>
        <p>Login As A User</p>
        <button class="ghost" id="signUp">Sign In</button>
      </div>
    </div>
  </div>
</div>

<script src="./assets/js/index.js"></script>

</body>
</html>
