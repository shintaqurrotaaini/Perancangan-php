<html>
<head>
	<title>proses input</title>
</head>
<body>
<?
$bil1=$_POST["bil1"];
$bil2=$_POST["bil2"];
?>
<h1>perbandingan bilangan</h1>
<hr>
bil I : <?echo $bil1?>
<br>
bil II : <?echo $bil2?>
<br>
<?
if ($bil1<bil2) {
echo "$bil1 lebih kecil dari $bil2";
}
elseif ($bil1>$bil2) {
echo $bil1 lebih besar dari $bil2";
}
else {
echo "$bil1 sama dengan $bil2";
}
?>
</body>
</html>
