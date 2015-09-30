<!-- Functions -->
<?php
	function f( $x )
	{
		return exp($x) * cos(8 * $x);
	}
	
	function df( $x )
	{
		return exp($x) * cos(8 * $x) - 8 * exp($x) * sin(8 * $x);
	}
	
	function root_search_step( $a, $b, $h, & $arr )
	{
		$arr = array();
		//echo 'h = '.$h,'<br>';
		$count = 0;
		$start = $a;
		
		while ($start < $b)
		{
			if (f($start) * f($start + $h) < 0)
			{
				$count++;
				$arr[] = array($start, $start + $h);
			}
			$start += $h;
		}
		
		return $count;
	}
	
	function get_root_segments( $a, $b, $n, & $arr )
	{
		$h = ($b - $a) / $n;
		
		while (root_search_step($a, $b, $h, $arr) != root_search_step($a, $b, $h / 10, $arr))
			$h /= 10;
		
		return root_search_step($a, $b, $h / 10, $arr);
	}
	
	function bisection( $start, $stop, $eps, & $level )
	{
		if ($stop - $start <= 2 * $eps)
			return ($start + $stop) / 2;
		
		$level++;
		if (f($start) * f(($start + $stop) / 2) < 0)
			return bisection($start, ($start + $stop) / 2, $eps, $level);
		return bisection(($start + $stop) / 2, $stop, $eps, $level);
	}
	
	function Newton_step( $start, $stop, $eps, & $level, $p )
	{
		if (abs($stop - $start) <= 2 * $eps)
			return $start;
			
		$level++;
		
		if (abs(df($stop)) < 1e-7)
			return -1;
			
		return Newton_step($stop, $stop - $p * f($stop) / df($stop), $eps, $level, $p);		
	}
	
	function Newton( $start, $stop, $eps, & $level )
	{
		$p = 1;
		
		while (Newton_step($start, $stop, $eps, $level, $p) == -1)
			$p++;
		
		return Newton_step($start, $stop, $eps, $level, $p);
	}
	
	function ModifiedNewton( $start, $x, $xnext, $eps, & $level )
	{
		if (abs($xnext - $x) <= 2 * $eps)
			return $xnext;

		$level++;

		return ModifiedNewton($start, $xnext, $xnext - f($xnext) / df($start), $eps, $level);
	}
	
	function Chords( $start, $stop, $eps, & $level )
	{
		if (abs($start - $stop) <= $eps)
			return $stop;
		
		$level++;
		
		return Chords($stop, $stop - f($stop) * ($stop - $start) / (f($stop) - f($start)), $eps, $level);
	}
?>

<?php
	$flag = isset($_GET['A']) && ($_GET['A'] != "") && isset($_GET['B']) && ($_GET['B'] != "") && isset($_GET['Eps']) && ($_GET['Eps'] != "") && isset($_GET['N']) && $_GET['N'] != "";
		
	$A = "";
	$B = "";
	$Eps = "";
	$N = "";
	
	if ($flag)
	{
		$A = $_GET['A'];
		$B = $_GET['B'];
		$Eps = $_GET['Eps'];
		$N = $_GET['N'];
	}
?>

<h1 align = 'center'>Numerical analysis of nonlinear algebraic and transcendental equations' solutions</h1>
<p align = 'center'><img src = 'func.jpg'></p>
<form align = 'center' method = 'get' action = 'task1.php'>
	A:<input type = 'text' name = 'A' size = '1' value = '<?php echo $A; ?>'><br><br>
	B:<input type = 'text' name = 'B' size = '1' value = '<?php echo $B; ?>'><br><br>
	N:<input type = 'text' name = 'N' size = '1' value = '<?php echo $N; ?>'><br>
	<p style = 'margin-left: -1'>Eps:<input type = 'text' name = 'Eps' size = '1' value = '<?php echo $Eps; ?>'><br><br></p>
	<input type = 'submit' value = 'Calculate'>
</form>

<?php
	if (!$flag)
		return;
	
	echo '<hr><p align = "center">Segments with odd multiplicity roots: <br>';
	
	$arr = array();
	
	get_root_segments($A, $B, $N, $arr);
	
	echo '['.$arr[0][0].'; '.$arr[0][1].']';
	
	for ($i = 1; $i < count($arr); $i++)
		echo ', ['.$arr[$i][0].'; '.$arr[$i][1].']';
	echo '<br><br><br>';

	for ($i = 0; $i < count($arr); $i++)
	{
		$steps_bis = 0;
		$steps_Newton = 0;
		$steps_modified = 0;
		$steps_chords = 0;
		
		$solution_bis = bisection($arr[$i][0], $arr[$i][1], $Eps, $steps_bis);
		$solution_Newton = Newton($arr[$i][0], $arr[$i][1], $Eps, $steps_Newton);
		$solution_modified = ModifiedNewton($arr[$i][0], $arr[$i][0], $arr[$i][0] - f($arr[$i][0]) / df($arr[$i][0]), $Eps, $steps_modified);
		$solution_chords = Chords($arr[$i][0], $arr[$i][1], $Eps, $steps_chords);
		
		echo '<b>['.$arr[$i][0].'; '.$arr[$i][1].']</b><br>';
		//echo '<table style = " border-style: solid; border-width: 1px;">';
		echo '<table width = "90%" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">';
		echo "<thead><th></th><th>Bisection method</th><th>Newton's method</th><th>Modified Newton's method</th><th>Chords' method</th></thead>";
		echo '<tr><td>Initial root approximation</td><td>'.(($arr[$i][0] + $arr[$i][1]) / 2).'</td><td>'.$arr[$i][0].'</td><td>'.$arr[$i][0].'</td><td>'.$arr[$i][0].'</td></tr>';
		echo '<tr><td>Steps</td><td>'.$steps_bis.'</td><td>'.$steps_Newton.'</td><td>'.$steps_modified.'</td><td>'.$steps_chords.'</td></tr>';
		echo '<tr><td>Approximate solution</td><td>'.$solution_bis.'</td><td>'.$solution_Newton.'</td><td>'.$solution_modified.'</td><td>'.$solution_chords.'</td></tr>';
		echo '<tr><td>Absolute value of discrepance</td><td>'.abs(f($solution_bis)).'</td><td>'.abs(f($solution_Newton)).'</td><td>'.abs(f($solution_modified)).'</td><td>'.abs(f($solution_chords)).'</td></tr>';
		echo '</table><br><br><br>';
	}
	
	echo '</p>';
?>	
