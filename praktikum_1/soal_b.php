<?php
$soal = [2,2,3,3,4];
function seq_b($deret_awal, $tambahan = 2) {
	$hasil = $deret_awal;
	for ($i = 0; $i < $tambahan; $i++) {
		$last = $hasil[count($hasil)-1];
		$count_repeat = 0;
		for ($j = count($hasil)-1; $j >= 0; $j--) {
			if($hasil[$j] === $last) $count_repeat++;
			else break;
		}
		if ($count_repeat < 2) {
			$hasil[] = $last;
			} else {
			$hasil[] = $last + 1;
			}
		}
		return $hasil;
	}
	$jawab = seq_b($soal, 2);
	echo "deret awal : " . implode(",",$soal) . "<br>";
	echo "hasil lengkap : " . implode(",",$jawab) . "<br>";
	echo "dua angka berikutnya : " .
	$jawab[count($jawab)-2] . ", ". $jawab[count($jawab)-1];
?>
