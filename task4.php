<!-- Functions and constants -->
<?php

function f( $x )
{
	return pow($x, 3) / exp($x);
}

function deriv_f( $x )
{
	return exp(-$x) * (3 * pow($x, 2) - pow($x, 3));
}

function deriv2_f( $x )
{
	return exp(-$x) * (pow($x, 3) - 6 * pow($x, 2) + 6 * $x);
}

function appr1( $x, $h )
{
	return (f($x + $h) - f($x)) / $h - $h / 2;
}

function appr2( $x, $h )
{
	return (f($x) - f($x - $h)) / $h + $h / 2;
}

function appr3( $x, $h )
{
	return (f($x + $h) - f($x - $h)) / 2 / $h - pow($h, 2) / 6;
}

function appr4( $x, $h )
{
	return (-3 * f($x) + 4 * f($x + $h) - f($x + 2 * $h)) / 2 / $h + pow($h, 2) / 3;
}

function appr5( $x, $h )
{
	return (3 * f($x) - 4 * f($x - $h) + f($x - 2 * $h)) / 2 / $h + pow($h, 2) / 3;
}

function appr6( $x, $h )
{
	return (f($x + $h) - 2 * f($x) + f($x - $h)) / pow($h, 2) - pow($h, 2) / 12;
}

$approx = array('appr1', 'appr2', 'appr3', 'appr4', 'appr5', 'appr6');

$x = "";
$h = "";
$a = 0;
$b = 8;

$flag = false;

if (isset($_GET['x']))
{
	$x = $_GET['x'];
	if ($x < $a || $x > $b)
	{
		echo 'Please input x from ['.$a.'; '.$b.']';
		return;
	}
}
if (isset($_GET['h']))
	$h = $_GET['h'];

if ($x != "" && $h != "")
	$flag = true;
?>

<h1 align = 'center'>Numerical differentiation</h1>
<p align = 'center'><img src = 'func4.jpg'></p>
<form align = 'center' method = 'get' action = 'task4.php'>
	h:<input type = 'text' name = 'h' size = '1' value = '<?php echo $h; ?>'>
	<p style = 'margin-left: -75;'>x from [<?php echo $a; ?>; <?php echo $b; ?>]:<input type = 'text' name = 'x' size = '1' value = '<?php echo $x; ?>'></p><br><br>
	<input type = 'submit' value = 'Build table'>
</form>

<?php
	if (!$flag)
		return;
?>

<h3 align = 'center'>Approx. table</h3>
<table align = 'center' width = "20%" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
	<thead>
		<th>#</th>
		<th>Result</th>
	</thead>
	<tbody>
		<?php
			for ($i = 0; $i < count($approx); $i++)
				echo '<tr><td>'.($i + 1).'</td><td>'.call_user_func($approx[$i], $x, $h).'</td></tr>';
		?>
	</tbody>
</table>

<h3 align = 'center'>Differences between approximations and derivatives values</h3>
<table align = 'center' width = "20%" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
	<thead>
		<th>#</th>
		<th>Difference</th>
	</thead>
	<tbody>
	<?php
	for ($i = 0; $i < count($approx) - 1; $i++)
		echo '<tr><td>'.($i + 1).'</td><td>'.abs(call_user_func($approx[$i], $x, $h) - deriv_f($x)).'</td></tr>';
	?>
	</tbody>
</table>

<h3 align = 'center'>Difference between approximation and second derivative value</h3>
<p align = 'center'><?php echo abs(call_user_func($approx[count($approx) - 1], $x, $h) - deriv2_f($x)); ?></p>

<h3 align = 'center'>Segments containing optimal h value for each approximation</h3>
<table align = 'center' width = '40%' border = '1' cellpadding = '0' cellspacing = '0' style = 'border-collapse: collapse;'>
	<thead>
		<th>#</th>
		<th>Segment</th>
		<th>Difference in the end of segment</th>
	</thead>
	<tbody>
		<?php
			for ($i = 0; $i < count($approx) - 1; $i++)
			{
				$h = 1.0;
				$diff = abs(call_user_func($approx[$i], $x, $h) - deriv_f($x));

				while (abs(call_user_func($approx[$i], $x, $h / 10) - deriv_f($x)) < $diff)
				{
					$h /= 10;
					$diff = abs(call_user_func($approx[$i], $x, $h) - deriv_f($x));
				}

				echo '<tr><td>'.($i + 1).'</td><td>['.($h / 10).'; '.$h.']</td><td>'.$diff.'</td></tr>';
			}

			$h = 1.0;
			$diff = abs(call_user_func($approx[count($approx) - 1], $x, $h) - deriv2_f($x));

			while (abs(call_user_func($approx[count($approx) - 1], $x, $h / 10) - deriv2_f($x)) < $diff)
			{
				$h /= 10;
				$diff = abs(call_user_func($approx[count($approx) - 1], $x, $h) - deriv2_f($x));
			}

			echo '<tr><td>'.count($approx).'</td><td>['.($h / 10).'; '.$h.']</td><td>'.$diff.'</td></tr>';
		?>
	</tbody>
</table>
