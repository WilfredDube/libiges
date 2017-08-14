<?php

namespace Iges\Entity\Bspline;

use Iges\Parser\IgesExtract;

class IgesBSplineSurface {

  public $K;
  public $Degree;
  public $PROP1;
  public $PROP2;
  public $PROP3;
  public $PROP4;
  public $Knot_Sequence; // ARRAY
  public $Weights; // ARRAY
  public $Control_Points; //
  public $Control_Pend;

  public function RBSplineSurfaceTract($surface) {
    $xtract = new IgesExtract();

    $counter = 1;

    $pentry = $surface;

    $arr = $xtract->multiexplode ( array (
      ",",
      ";"
    ), $pentry );

    $j = 1;

    if (($j + 1) >= count ( $arr ) && ($j + 2) >= count ( $arr )) {
      break;
    }

    $K1 = trim ( $arr [$j] );
    $K2 = trim ( $arr [$j + 1] );

    $M1 = trim ( $arr [$j + 2] );
    $M2 = trim ( $arr [$j + 3] );

    $N1 = 1 + $K1 - $M1;
    $N2 = 1 + $K2 - $M2;
    $A = $N1 + 2 * $M1;
    $B = $N2 + 2 * $M2;
    $C = (1 + $K1) * (1 + $K2);

    $this->K1 = $K1;
    $this->K2 = $K2;

    $this->Degree1 = $M1;
    $this->Degree2 = $M2;

    $this->PROP1 = $arr [$j + 4];
    $this->PROP2 = $arr [$j + 5];
    $this->PROP3 = $arr [$j + 6];
    $this->PROP4 = $arr [$j + 7];
    $this->PROP5 = $arr [$j + 8];

    $this->Knot_Sequence1 = array ();
    $this->Knot_Sequence2 = array ();
    $this->Weights = array ();
    $this->Control_Points = array ();

    $knot1start = $j + 9;
    $knot1end = $knot1start + $A;

    $knot2start = $j + 10 + $A;
    $knot2end = $knot2start + $B;

    $weight1start = $knot2end + 1;
    $weight1end = $knot2end + $C;

    $controlpstart = $weight1end + 1;
    $controlpend = 9 + $A + $B + (4 * $C) + 2;

    for($x = 0, $i = $knot1start; $i <= ($knot1end); $i ++, $x ++) {
      if ($arr [$i] == 0.)
      $arr [$i] = 0.0;
      else if ($arr [$i] == 1.)
      $arr [$i] = 1.0;
      $kn = ($arr [$i]);

      $pos = strpos ( $kn, "D" );

      if ($pos) {
        $kn [$pos] = "E";
      }

      $this->Knot_Sequence1 [$x] = $kn;
    }

    for($x = 0, $i = $knot2start; $i <= ($knot2end); $i ++, $x ++) {
      if ($arr [$i] == 0.)
      $arr [$i] = 0.0;
      else if ($arr [$i] == 1.)
      $arr [$i] = 1.0;
      $kn = ($arr [$i]);

      $pos = strpos ( $kn, "D" );

      if ($pos) {
        $kn [$pos] = "E";
      }

      $this->Knot_Sequence2 [$x] = $kn;
    }
    for($x = 0, $i = $weight1start; $i <= ($weight1end); $i ++, $x ++) {
      if ($arr [$i] == 0.)
      $arr [$i] = 0.0;
      else if ($arr [$i] == 1.)
      $arr [$i] = 1.0;
      $kn = ($arr [$i]);
      // echo $kn."dasasasa";
      $pos = strpos ( $kn, "D" );

      if ($pos) {
        $kn [$pos] = "E";
      }

      $this->Weights [$x] = $kn;
    }

    for($x = 0, $i = $controlpstart; $i <= ($controlpend); $i ++, $x ++) {

      if ($arr [$i] == 0.)
      $arr [$i] = 0.0;
      else if ($arr [$i] == 1.)
      $arr [$i] = 1.0;

      $kn = ($arr [$i]);
      $pos = strpos ( $kn, "D" );

      if ($pos) {
        $kn [$pos] = "E";
      }

      $this->Control_Points [$x] = $kn;
    }
    return $this;
  }
}
