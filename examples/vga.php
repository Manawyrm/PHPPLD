<?php

// EEPROM size: 131072 bytes (128 KiB)
//	600 * 100 pixel effective resolution
//  628 * 132 pixel internal resolution = 82896 pixels
//
// Timing parameters: 
// Horizontal timing
$horizontal_visible_area = 100; 
$horizontal_front_porch = 5; 
$horizontal_sync_pulse = 16; 
$horizontal_back_porch = 11; 
$horizontal_total_length =  $horizontal_visible_area + 
							$horizontal_front_porch + 
							$horizontal_sync_pulse + 
							$horizontal_back_porch;

// Vertical timings
$vertical_visible_area = 600; 
$vertical_front_porch = 1; 
$vertical_sync_pulse = 4; 
$vertical_back_porch = 23; 
$vertical_total_length =    $vertical_visible_area + 
							$vertical_front_porch + 
							$vertical_sync_pulse + 
							$vertical_back_porch;

$total_pixel_count = $vertical_total_length * $horizontal_total_length;

//error_log("Vertical total length: " . $vertical_total_length);
//error_log("Horizontal total length: " . $horizontal_total_length);
//error_log("Total pixel count: " . $total_pixel_count); 

$red = 0; 
$green = 0; 
$blue = 0;
$hsync = false;  // Horizontal sync output
$vsync = false;  // Vertical sync output
$hblank = false; // Horizontal blank output
$vblank = false; // Vertical blank output
$hvisible = false; // in Horizontal visible area
$vvisible = false; // in Vertical visible area
$finished = false;

$current_pixel = integer_value($input);

// Which line are we on? 
$current_line  = intdiv($current_pixel, $horizontal_total_length);

// Which position of the line are we on? 
$current_horizontal_position = $current_pixel % $horizontal_total_length;

//error_log("Vertical position: " . $current_line);
//error_log("Horizontal position: " . $current_horizontal_position);

// Sync code

if ($current_pixel <= $total_pixel_count)
{
	if ($current_line < $vertical_visible_area)
	{
		// Vertical visible area
		$vvisible = true;
	}
	else
	{
		// Vertical blank
		$vblank = true; 
		if ($current_line > $vertical_visible_area + $vertical_front_porch &&
			$current_line <= $vertical_visible_area + $vertical_front_porch + $vertical_sync_pulse )
		{
			$vsync = true; 
		}
	}

	if ($current_horizontal_position < $horizontal_visible_area)
	{
		// Horizontal visible area
		$hvisible = true;
	}
	else
	{
		$hblank = true; 
		if ($current_horizontal_position > $horizontal_visible_area + $horizontal_front_porch &&
			$current_horizontal_position <= $horizontal_visible_area + $horizontal_front_porch + $horizontal_sync_pulse )
		{
			$hsync = true; 
		}	
	}
}
else
{
	// Finished frame
	$finished = true;
}

// Display content
if ($hvisible && $vvisible)
{
	// Don't load the image on each evaluation cycle
	global $gdimage; 
	if (!isset($gdimage))
	{
		error_log("Loading image...");
		$gdimage = imagecreatefrompng("examples/vga_indexed221_2.png");
	}

	$rgb = imagecolorat($gdimage, $current_horizontal_position, $current_line);
	$colors = imagecolorsforindex($gdimage, $rgb);

	$red = $colors['red'];
	$green = $colors['green'];
	$blue = $colors['blue'];
}

$output = [
	!!($red & 0x01),
	!!($red & 0x02),
	!!($green & 0x01),
	!!($green & 0x02),
	!!($blue & 0x02),
	$finished,
	$hsync,
	$vsync
];