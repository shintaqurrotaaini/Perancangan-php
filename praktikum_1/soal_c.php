<?php
$soal = [1,9,2,10,3];
function seq_c($deret_awal, $deret_berikutnya = 2) {
	$hasil = $deret_awal;
	for ($i = 0; $i < $deret_berikutnya; $i++) {
		$posisi = count($hasil) + 1;
		if ($posisi % 2 == 1){
			$count_odd = 0;
			for ($j = 0; $j < count($hasil); $j++) {
				if (($j+1) % 2 == 1) $count_odd++;
		}
		$hasil[] = $count_odd + 1; } else {
		$count_even = 0;
		for ($j = 0; $j < count($hasil); $j++) {
			if (($j+1) % 2 == 0) $count_even++;
		}
		$hasil[] = 9 + $count_even;
		}
	}
		return $hasil;
	}
	$jawab = seq_c($soal, 2);
	echo "deret_awal: " . implode(", ", $soal) . "<br>";
	echo "hasil lengkap: " . implode(", ", $jawab) . "<br>";
	echo "dua angka berikutnya: " . $jawab[count($jawab)-2] .",".$jawab[count($jawab)-1];

?>