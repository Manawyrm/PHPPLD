<?php
// Magic variables & functions: 
// $input - array of booleans
// integer_value(array, from, to) - returns integer from an array of booleans
// integer_value_msb(array, from, to) - return integer from an array of booleans in most-significant-bit order
// $output - array of booleans

$address = integer_value($input);

if ($address >= 0x20 && $address <= 0x40)
{
	$cs_chip1 = 1; 
}
if ($address >= 0x60 && $address <= 0x65)
{
	$cs_chip2 = 1; 
}

$output[0] = $cs_chip1; 
$output[1] = !$cs_chip2; 
