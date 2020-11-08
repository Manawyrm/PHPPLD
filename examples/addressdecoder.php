<?php
// Magic variables & functions: 
// $input - array of booleans
// integer_value(array, from, to) - returns integer from an array of booleans
// integer_value_msb(array, from, to) - return integer from an array of booleans in most-significant-bit order
// $output - array of booleans

$address = integer_value($input) << 8;

if ($address == 0x300)
{
	$cs_chip1 = 1; 
}

$output[0] = $cs_chip1; 