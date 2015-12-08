<!-- Functions and constants -->
<?php
$a = 0;
$b = 1;
$n = "";

if (isset($_GET['n']))
	$n = $_GET['n'];

function f( $x, $d )
{
	if ($d == 5)
		return 1 / (pow($x, 2) + 20);
	else
		return pow($x, $d);
}

function integrate( $d )
{
	if ($d == 5)
		return atan(1.0 / 2 / sqrt(5)) / 2 / sqrt(5);
	return 1.0 / ($d + 1);
}

// delta = 0 if left rectangles, 0.5 if central, 1 if right
function rectangle_method( $d, $delta )
{
	global $a, $b, $n;

	$res = 0;
	$h = ($b - $a) / $n;

	for ($i = 0; $i < $n; $i++)
		$res += f($a + $h * ($delta + $i), $d);

	return $h * $res;
}

function trapeze_method( $d )
{
	global $a, $b, $n;

	$res = f($a, $d) + f($b, $d);
	$h = ($b - $a) / $n;

	for ($i = 1; $i < $n; $i++)
		$res += 2 * f($a + $i * $h, $d);

	return $h * $res / 2;
}

function Simpson_method( $d )
{
	global $a, $b, $n;

	$res = f($a, $d) + f($b, $d);
	$h = ($b - $a) / $n / 2;

	for ($i = 1; $i < 2 * $n; $i += 2)
		$res += 4 * f($a + $i * $h, $d);
	for ($i = 2; $i < 2 * $n; $i += 2)
		$res += 2 * f($a + $i * $h, $d);

	return $h * $res / 3;
}

function error_est( $C, $d )
{
	global $a, $b, $n;

	$res = $C * ($b - $a) * pow(($b - $a) / $n, $d + 1) / pow(sqrt(5), $d + 3);

	for ($i = 2; $i <= $d + 1; $i++)
		$res *= $i;

	return $res;
}
?>

<h1 align = 'center'>Numerical integration </h1>
<p align = 'center'><img src = 'func5.jpg'></p>
<form align = 'center' method = 'get' action = 'task5.php'>
	n (number of parts):<input type = 'text' name = 'n' size = '1' value = '<?php echo $n; ?>'><br><br>
	<input type = 'submit' value = 'Build table'>
</form>

<?php
	if ($n == "")
		return;
	$real_value = integrate(5);
?>

<h3 align = 'center'>Integral value</h3>
<table align = 'center' width = "50%" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
	<thead>
		<th>Method</th>
		<th>Result</th>
		<th>Absolute value of difference</th>
		<th>Error estimate</th>
	</thead>
	<tbody>
		<tr><td>Left rectangles</td><td><?php echo rectangle_method(5, 0); ?></td><td><?php echo abs($real_value - rectangle_method(5, 0)); ?></td><td><?php echo error_est(0.5, 0); ?></td></tr>
		<tr><td>Central rectangles</td><td><?php echo rectangle_method(5, 0.5); ?></td><td><?php echo abs($real_value - rectangle_method(5, 0.5)); ?></td><td><?php echo error_est(0.5, 0); ?></td></tr>
		<tr><td>Right rectangles</td><td><?php echo rectangle_method(5, 1); ?></td><td><?php echo abs($real_value - rectangle_method(5, 1)); ?></td><td><?php echo error_est(1 / 24., 1); ?></td></tr>
		<tr><td>Trapeze</td><td><?php echo trapeze_method(5); ?></td><td><?php echo abs($real_value - trapeze_method(5)); ?></td><td><?php echo error_est(1 / 12., 1); ?></td></tr>
		<tr><td>Simpson</td><td><?php echo Simpson_method(5); ?></td><td><?php echo abs($real_value - Simpson_method(5)); ?></td><td><?php echo error_est(1 / 2880., 3); ?></td></tr>
	</tbody>
</table>

<h3 align = 'center'>Accuracy degree check</h3>
<table align = 'center' width = "70%" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
	<thead>
		<th>Method\Degree</th>
		<th>0</th>
		<th>1</th>
		<th>2</th>
		<th>3</th>
		<th>4</th>
		<th>f(x)</th>
	</thead>
	<tbody>
		<tr><td>Left rectangles</td><?php for ($i = 0; $i < 6; $i++) echo '<td>'.abs(integrate($i) - rectangle_method($i, 0)).'</td>'; ?></tr>
		<tr><td>Central rectangles</td><?php for ($i = 0; $i < 6; $i++) echo '<td>'.abs(integrate($i) - rectangle_method($i, 0.5)).'</td>'; ?></tr>
		<tr><td>Right rectangles</td><?php for ($i = 0; $i < 6; $i++) echo '<td>'.abs(integrate($i) - rectangle_method($i, 1)).'</td>'; ?></tr>
		<tr><td>Trapeze</td><?php for ($i = 0; $i < 6; $i++) echo '<td>'.abs(integrate($i) - trapeze_method($i)).'</td>'; ?></tr>
		<tr><td>Simpson</td><?php for ($i = 0; $i < 6; $i++) echo '<td>'.abs(integrate($i) - Simpson_method($i)).'</td>'; ?></tr>
	</tbody>
</table>
