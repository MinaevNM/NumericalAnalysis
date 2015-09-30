<!-- Functions and constants -->
<?php
	$a = 0;
	$b = 5;
	$eps = 1e-8; // minimal possible difference between two data points
	$L_flag = false;

	function f( $x )
	{
		//return $x * exp(-$x * $x);
		return $x * $x * $x * $x + 3 * $x * $x + 5;
	}

	function not_in_table( $dataset, $newx )
	{
		global $eps;

		for ($i = 0; $i < count($dataset); $i++)
			if (abs($dataset[$i] - $newx) < $eps)
				return false;
		return true;
	}

	function build_table( $m )
	{
		global $a, $b;

		$dataset = array();

		while (count($dataset) <= $m)
		{
			$newx = rand() / getrandmax() * ($b - $a) + $a;

			if (not_in_table($dataset, $newx))
				$dataset[] = $newx;
		}

		return $dataset;
	}

	function cmp_by_dist( $a, $b )
	{
		global $x;

		$dist1 = abs($a - $x);
		$dist2 = abs($b - $x);

		if ($dist1 == $dist2)
			return 0;
		return ($dist1 < $dist2) ? -1 : 1;
	}

	function Lagrange( $dataset, $x, $n )
	{
		global $eps;
		global $L_flag;

		for ($i = 0; $i < count($dataset); $i++)
			if (abs($x - $dataset[$i]) < $eps)
			{
				$L_flag = true;
				return f($dataset[$i]);
			}

		$res = 0;

		for ($i = 0; $i <= $n; $i++)
		{
			$base_pol_i = 1;
			for ($j = 0; $j <= $n; $j++)
				if ($i != $j)
					$base_pol_i *= ($x - $dataset[$j]) / ($dataset[$i] - $dataset[$j]);

			$res += f($dataset[$i]) * $base_pol_i;
		}

		return $res;
	}

	function Newton( $dataset, $x, $n )
	{
		$differences = array();
		$res_coeff = array();

		for ($i = 0; $i <= $n; $i++)
			$differences[$i] = f($dataset[$i]);

		for ($i = 1; $i <= $n; $i++)
		{
			$res_coeff[] = $differences[0];
			for ($j = 0; $j <= $n - $i; $j++)
				$differences[$j] = ($differences[$j + 1] - $differences[$j]) / ($dataset[$i + $j] - $dataset[$j]);
		}

		$res_coeff[] = $differences[0];

		$result = 0;
		$multiplier = 1;

		for ($i = 0; $i <= $n; $i++)
		{
			$result += $res_coeff[$i] * $multiplier;
			$multiplier *= ($x - $dataset[$i]);
		}

		return $result;
	}
?>

<?php
	$m = "";
	$x = "";
	$n = "";
	$state = 0;

	if (isset($_GET['m']) && $_GET['m'] != "")
	{
		$state = 1;
		if (isset($_GET['x']) && isset($_GET['n']) && $_GET['x'] != "" && $_GET['n'] != "")
			$state = 2;
	}

	if ($state)
	{
		$m = $_GET['m'];

		if ($state == 2)
		{
			$x = $_GET['x'];
			$n = $_GET['n'];
		}
	}
?>

<h1 align = 'center'>Polynomial interpolation</h1>
<h2 align = 'center'>Lagrange's and Newton's polynomials</h2>
<p align = 'center'><img src = 'func2.jpg'></p>
<form align = 'center' method = 'get' action = 'task2.php'>
	Number of values minus 1 (m):<input type = 'text' name = 'm' size = '1' value = '<?php echo $m; ?>'><br><br>
	<input type = 'submit' value = 'Build table'>
</form>

<?php
	if (!$state)
		return;
?>

<hr><p align = "center">Table of data points: <br>
	<table width = "20%" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
		<thead><th>x<font size = '1'>i</font></th><th>f(x<font size = '1'>i</font>)</th></thead>
		<?php
			if ($state == 1)
				$dataset = build_table($m);
			else if ($state == 2)
			{
				$dataset = array();

				$i = 0;

				while (isset($_GET['x'.$i]) && $_GET['x'.$i] != "")
				{
					$dataset[$i] = $_GET['x' . $i];
					$i++;
				}
			}

			for ($i = 0; $i < count($dataset); $i++)
				echo '<tr><td>'.$dataset[$i].'</td><td>'.f($dataset[$i]).'</td></tr>';
		?>
	</table><br><br>

	<form align = 'center' method = 'get' action = 'task2.php'>
		<?php
			for ($i = 0; $i < count($dataset); $i++)
				echo '<input type = "hidden" name = "x'.$i.'" value = "'.$dataset[$i].'"> ';
		?>
		<input type = 'hidden' name = 'm' value = '<?php echo $m; ?>'>
		x: <input type = 'text' name = 'x' value = '<?php echo $x; ?>'><br>
		<p style = 'margin-left: -17%;'>Interpolation polynomial's degree (n): <input type = 'text' name = 'n' value = '<?php echo $n; ?>'></p>
		<input type = 'submit' value = 'Find polynomial'>
	</form>
</p>

<?php
	if ($state != 2)
		return;

	if ($n > $m)
	{
		echo "<hr><p align = 'center'>Interpolation polynomial's degree should be less than number of values (n <= m)</p>";
		return;
	}

	usort($dataset, 'cmp_by_dist');
?>
<hr><p align = "center">Table of data points, sorted by distance: <br>
	<table width = "20%" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
		<thead><th>x<font size = '1'>i</font></th><th>f(x<font size = '1'>i</font>)</th></thead>
		<?php
			for ($i = 0; $i < count($dataset); $i++)
				echo '<tr><td>'.$dataset[$i].'</td><td>'.f($dataset[$i]).'</td></tr>';
		?>
	</table><br><br>
	<?php
		$L_res = Lagrange($dataset, $x, $n);
		$N_res = Newton($dataset, $x, $n);

		if ($L_flag)
			echo "Chosen x is one of dataset's points<br><br>";
	?>
	Result with Lagrange's polynomial<br>
	P<font size = '1'>n</font>(x) = P<font size = '1'><?php echo $n; ?></font>(<?php echo $x;?>) = <?php echo $L_res; ?><br>
	Error ef<font size = '1'>n</font>(x) = <?php echo abs(f($x) - $L_res); ?><br><br><br>
	Result with Newton's polynomial<br>
	P<font size = '1'>n</font>(x) = P<font size = '1'><?php echo $n; ?></font>(<?php echo $x;?>) = <?php echo $N_res; ?><br>
	Error ef<font size = '1'>n</font>(x) = <?php echo abs(f($x) - $N_res); ?><br>
</p>
