<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem</title>
</head>
<body>

<h2>LOGIN SISTEM</h2>

<?php if (isset($_GET['error'])): ?>
<p style="color:red;">Username / Password salah!</p>
<?php endif; ?>

<form action="login_process.php" method="POST">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
