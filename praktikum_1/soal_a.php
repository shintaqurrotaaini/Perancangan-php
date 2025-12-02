<?php
$soal = [4,6,9,13,18];
function soal_a($awal, $selanjutnya = 2) {
	$jawab = $awal;
	$diffs = [];
	for ($i = 1; $i < count($awal); $i++) {
		$diffs[] = $awal[$i] - $awal[$i-1];
	}
	$last_diff = $diffs[count($diffs)-1];
	for ($k = 1; $k <= $selanjutnya; $k++) {
		$last_diff += 1;
		$jawab[] = $jawab[count($jawab)-1] + $last_diff;
	}
	return $jawab;
}
$jawab = soal_a($soal, 2);
echo "<pre>";
print_r($jawab);
echo "</pre>";
?>