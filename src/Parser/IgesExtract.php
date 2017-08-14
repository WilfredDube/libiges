<?php

namespace Iges\Parser;

class IgesExtract {

  public function multiexplode($delimeters, $string) {
    $ready = str_replace ( $delimeters, $delimeters [0], $string );
    $launch = explode ( $delimeters [0], $ready );

    return $launch;
  }

  public function getDimensions($gsection) {
    $unit = trim ( $gsection [13] );
    $dim = null;

    switch ($unit) {
      case 1 :
      $dim = "inches";
      break;
      case 2 :
      $dim = "mm";
      break;
      case 3 :
      $dim = "special";
      break;
      case 4 :
      $dim = "ft";
      break;
      case 5 :
      $dim = "miles";
      break;
      case 6 :
      $dim = "metres";
      break;
      case 7 :
      $dim = "Km";
      break;
      case 8 :
      $dim = "mils";
      break;
      case 9 :
      $dim = "microns";
      break;
      case 10 :
      $dim = "cm";
      break;
      case 11 :
      $dim = "minchs";
      break;
      default :
      break;
    }

    return $dim;
  }

  public function removeD($arr) {
    $kn = trim ( $arr );

    $pos = strpos ( $kn, "D" );

    if ($pos) {
      $kn [$pos] = "E";
    }

    return $kn;
  }
}
