<!-- Functions and constants -->
<?php

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

$a = 0;
$b = 1;
$n = "";

if (isset($_GET['n']))
	$n = $_GET['n'];

?>

<h1 align = 'center'>Numerical integration </h1>
<p align = 'center'><img src = 'func5.jpg'></p>
<form align = 'center' method = 'get' action = 'task5.php'>
	n (number of parts):<input type = 'text' name = 'n' size = '1' value = '<?php echo $n; ?>'><br><br>
	<input type = 'submit' value = 'Build table'>
</form>
