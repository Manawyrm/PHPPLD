<?php
// Magic variables & functions: 
// $input - array of booleans
// integer_value(array, from, to) - returns integer from an array of booleans
// integer_value_msb(array, from, to) - return integer from an array of booleans in most-significant-bit order
// $output - array of booleans

// NOT gate
$output[0] = !$input[0];

// AND gate
$output[1] = $input[1] && $input[2];

// OR gate
$output[2] = $input[3] || $input[4];

// XOR gate
$output[3] = $input[5] xor $input[6];
