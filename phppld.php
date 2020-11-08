<?php 
/**
 * @brief		PHP-PLD
 * @date		2020-11-07
 * @version		1.0
 * @author		Tobias Mädel (@manawyrm) <t.maedel@alfeld.de>
 */


$optionIndex = false; 
$options = getopt ( "i:w:", [], $optionIndex );
if (!$options)
{
	print_usage();
	die();
}
if ($argc == $optionIndex)
{
	error_log("[Error] No input file specified!");
	error_log("");
	print_usage();
	die();
}

$filename = $argv[$optionIndex++];
if (!file_exists($filename))
{
	error_log("[Error] Could not open input file!");
	error_log("");
	print_usage();
	die();
}

if (!isset($options["i"]) || !is_numeric($options["i"]))
{
	error_log("[Error] Invalid number of input bits!");
	error_log("");
	print_usage();
	die();
}

$numberOfInputs = (int) $options["i"];
$numberOfOutputs = 8;

load_pld($filename);
$bitstream = generate_bitstream($numberOfInputs, $numberOfOutputs);

if (isset($options["w"]) && $options["w"] !== false)
{
	file_put_contents($options["w"], $bitstream);
}

function print_usage()
{
	error_log("PHP-PLD v0.1");
	error_log("Written by Tobias Mädel (@manawyrm)");
	error_log("");
	error_log("Usage: php phppld.php <input file>");
	error_log(" -i <number of input bits> (required)");
	error_log(" -w <target filename>");
}

function load_pld($pldFilename)
{
	$pldContent = file_get_contents($pldFilename);
	$pldContent = str_replace("<?php", "", $pldContent);

	$php = 'function pldeval (&$input, &$output) {' . "\n" . $pldContent . "\n}";
	eval($php);
}

function generate_bitstream($numberOfInputs, $numberOfOutputs)
{
	error_log("Generating " . ((2 ** $numberOfInputs) * $numberOfOutputs / 8) . " bytes of bitstream");
	$bitstream = "";

	if ($numberOfOutputs != 8)
	{
		throw new Exception("Number of outputs != 8 not supported (yet)");
	}

	for ($input = 0; $input < (2 ** $numberOfInputs); $input++)
	{ 
		$output_array = evaluate_input($input, $numberOfInputs, $numberOfOutputs);
		$bitstream .= chr(integer_value_msb($output_array));
	}

	return $bitstream;
}

function evaluate_input($input, $numberOfInputs, $numberOfOutputs)
{
	$input_array = integer_to_array($input, $numberOfInputs);
	$output_array = [];
	for ($outputBit=0; $outputBit < $numberOfOutputs; $outputBit++)
	{ 
		$output_array[$outputBit] = 0;
	}

	$return = @pldeval($input_array, $output_array);
	return $output_array;
}

function integer_value($array, $from = 0, $to = false)
{
	if ($to === false)
		$to = count($array);

	$bits = ""; 
	for ($i = $from; $i < $to; $i++)
	{
		$bits .= $array[$i] ? "1" : "0";
	}
	return bindec($bits); 
}
function integer_value_msb($array, $from = 0, $to = false)
{
	if ($to === false)
		$to = count($array);

	$bits = ""; 
	for ($i = $from; $i < $to; $i++)
	{
		$bits .= $array[$i] ? "1" : "0";
	}
	$bits = strrev($bits);
	return bindec($bits); 
}

function integer_to_array($int, $length)
{
	$output = [];
	$bits = sprintf( "%".(int)$length."d", decbin( $int ));
	for ($i=0; $i < $length; $i++)
	{ 
		$output[$i] = ($bits[$i] == "1") ? true : false; 
	}
	return $output;
}