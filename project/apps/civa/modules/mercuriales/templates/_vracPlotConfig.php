set terminal svg size 1200,800 enhanced background rgb 'white'
set output "<?php echo $file ?>"
set datafile separator ';'

set grid ytics lc rgb "grey" lw 1 lt 1
set tics scale 0.5,0.5
set border 2+1;
set key outside;
set key right top;

set style arrow 1 nohead lw 1 lc rgb "red" lt 0
<?php 
	$xtics = '';
	$nb = count($datas);
	$i=0;
	$min = 999;
	$max = 0;
	$labels = '';
	foreach ($datas as $data): 
	if (!preg_match('/^[0-9]{7}$/', $data[0])) {
		continue;
	}
	for ($k = 2, $counter = count($data); $k < $counter; $k++) {
		$v = str_replace(',', '.', $data[$k]) * 1;
		if ($v && $v < $min) {
			$min = $v;
		}
		if ($v && $v > $max) {
			$max = $v;
		}
	}
	$i++;
	$date = new DateTime(substr($data[0], 0, 4).'-'.substr($data[0], 4, 2).'-'.substr($data[0], -1).'1');
	$xtics .= (substr($data[0], -1) > 0)? '"'.substr($date->format('M'), 0, 1).'\n'.substr($date->format('j'), 0, 1).'" '.$data[1] : '"'.substr($date->format('M'), 0, 1).'\n " '.$data[1];
	if ($i<$nb-1) {
		$xtics .= ', ';
	}
?>
<?php 
if (preg_match('/^[0-9]{4}12[0-1]{1}$/', $data[0])):
?>
set arrow arrowstyle 1 from "<?php echo $data[1] ?>", graph 0 to "<?php echo $data[1] ?>", graph 1
<?php endif; ?>
<?php 
if (preg_match('/^[0-9]{4}04[0-1]{1}$/', $data[0])) {
	$labels .= "set label '".(substr($data[0], 0, 4) - 1)." / ".substr($data[0], 0, 4)."' at ".($data[1] + 5).",_MIN_ font ', 11'\n";
}
?>
<?php
endforeach;
$min = ($min > 1)? round(($min - 1), 1, PHP_ROUND_HALF_DOWN) : 0;
$max = round(($max + 0.30), 1, PHP_ROUND_HALF_DOWN);

echo str_replace('_MIN_', $min+0.45, $labels);
?>

set xtics nomirror (<?php echo $xtics ?>) font ", 6"


set format y "%.2f €"
set yrange [<?php echo $min ?>:<?php echo $max ?>]
set ytics nomirror <?php echo $min ?>,.1,<?php echo $max ?> font ", 10"
set ylabel "Euros / litre" offset 4,0,0

set style arrow 1 nohead lw 1 lc rgb "red" lt 0

<?php 
foreach ($datas[count($datas) - 1] as $k => $v):
if ($k < 2) { continue; }
if (!$v) { continue; }
?>
set label "<?php echo $v ?>" at <?php echo $datas[count($datas) - 1][1] + 15 ?>,<?php echo $v ?> right font ", 11"
<?php endforeach; ?>

		
set style line 1 pt 2 lw 1 ps 0.8 lc rgb "black"
set style line 2 pt 5 lw 1 ps 0.5 lc rgb "black"
set style line 3 pt 6 lw 1 ps 0.8 lc rgb "black"
plot <?php $i=0; foreach ($cols as $k => $v): $i++; ?> "<?php echo $csvFile ?>" using 2:<?php echo $k + 3 ?> with linespoints ls <?php echo $i ?> title "<?php echo $v ?>"<?php if($i < count($cols)): ?>,<?php endif; ?><?php endforeach; ?>