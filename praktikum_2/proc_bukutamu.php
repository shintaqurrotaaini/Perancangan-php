<html>
<head>
	<title>buku tamu</title>
</head>
<body>
<?
$nama=$_POST["nama"];
$email=$_POST["email"];
$komentar["komentar"];
?>
<h1> data buku tamu </h1>
<hr>
nama anda : <?echo $nama?>
<br>
email address : <?echo $email?>
<br>
komentar :
<textarea name="komentar" cols="40" rows="5"><?echo $komentar?> </textarea>
<br>
</body>
</html>
