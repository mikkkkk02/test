<?php

namespace App\Helper;

class Helpers
{
    public static function renderTextColor($color) {

        $bg = Helpers::HTMLToRGB($color);
        $white = 'FFFFFF';
        $black = '000000';

        $wFont = Helpers::HTMLToRGB($white);
        $bFont = Helpers::HTMLToRGB($black);

        $wResult = Helpers::detectColor($bg, $wFont);
        $bResult = Helpers::detectColor($bg, $bFont);

        if($wResult > $bResult)
            return $white;

        return $black;
    }

	public static function HTMLToRGB($htmlCode) {
        /* Remove hashtag if exists */
        if($htmlCode[0] == '#')
            $htmlCode = substr($htmlCode, 1);

        if(strlen($htmlCode) == 3) {
          $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
        }

        $r = hexdec($htmlCode[0] . $htmlCode[1]);
        $g = hexdec($htmlCode[2] . $htmlCode[3]);
        $b = hexdec($htmlCode[4] . $htmlCode[5]);

        return [
            'r' => $r,
            'g' => $g,
            'b' => $b,
        ];
    }

    public static function detectColor($color1, $color2) {
        $l1 = 0.2126 * pow($color1['r'] / 255, 2.2) +
              0.7152 * pow($color1['g'] / 255, 2.2) +
              0.0722 * pow($color1['b'] / 255, 2.2);
     
        $l2 = 0.2126 * pow($color2['r'] / 255, 2.2) +
              0.7152 * pow($color2['g'] / 255, 2.2) +
              0.0722 * pow($color2['b'] / 255, 2.2);
     
        if($l1 > $l2) {
            return ($l1 + 0.05) / ($l2 + 0.05);
        } else {
            return ($l2 + 0.05) / ($l1 + 0.05);
        }        
    }
}