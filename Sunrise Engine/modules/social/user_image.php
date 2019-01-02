<?php

$size = 300;
$scale = 20;

$ip = rand(0, 255).".".rand(0, 255).".".rand(0, 255).".".rand(0, 255);
if(isset($_REQUEST['ip']))$ip = $_REQUEST['ip'];

header("Content-Type: image/png");
$im = @imagecreate($size, $size)
    or die("Fail");
	
//

$background_color = imagecolorallocate($im, 0, 0, 0);

$ip_array = explode(".", $ip);

if(sizeof($ip_array) == 4)
{
	$big_ip_array = $ip_array;
	for($i = 0; $i < sizeof($ip_array); $i++)
	{
		$big_ip_array[sizeof($big_ip_array)] = $ip_array[sizeof($ip_array) - $i - 1];
	}
	$ip_array = $big_ip_array;
	
	for($i = 0; $i < sizeof($ip_array); $i++)
	{
		if(is_numeric($ip_array[$i]))
		{
			$bin = decbin($ip_array[$i]);
			$bin_array = str_split($bin);
			
			$color = imagecolorallocate($im, $ip_array[0], $ip_array[1], $ip_array[2]);
			
			$x = $i;
			for($y = 0; $y < sizeof($bin_array); $y++)
			{
				if($bin_array[$y] == 1)
				{
					$padding = ($size - ($scale * 8))/2;
					for($ix = 0; $ix < $scale; $ix++)
					{
						for($iy = 0; $iy < $scale; $iy++)
						{
							$set = imagesetpixel($im, $padding + $x * $scale + $ix, $padding + $y * $scale + $iy, $color);
						}
					}
				}
			}
		}
	}
}

//
imagepng($im);
imagedestroy($im);

?>