<!DOCTYPE html>
<html>
<head>
	<title>Form Users</title>
</head>
<body>
	<h2>Tambah Data User</h2>
	<form action="koneksi.php" method="POST">
		<label for="username">username:</label><br>
		<input type="text" id="username" name="username" required><br><br>
		<label for="email">email:</label><br>
		<input type="email" id="email" name="email" required><br><br>
		<label for="password">password:</label><br>
		<input type="text" id="password" name="password" required><br><br>
		<button type="submit">simpan</button>
	</form>
</body>
</html>
