<?php

namespace Iges\Entity\Bspline;

use Iges\Parser\IgesExtract;

class IgesBSplineCurve {

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

  function __construct() {
    $this->Knot_Sequence = array();
    $this->Weights = array();
  }

  public function rbsplineCurveTract($dsection = null, $psection = null) {
    $xtract = new IgesExtract();
    $counter = 0;

    // if ($dsection != null)
    foreach ( $dsection as $value ) {

      if ($value->EntityType == 126)
      {
        $id = $value->PointerData;
        $pentry = $psection [$id];

        $arr = $xtract->multiexplode ( array (
          ",",
          ";"
        ), $pentry );

        $j = 1;
        {
          $edt = new IgesBSplineCurve();
          if (($j + 1) >= count ( $arr ) && ($j + 2) >= count ( $arr )) {
            // echo "out";
            break;
          }
          ++ $counter;
          $K = trim ( $arr [$j] );
          $M = trim ( $arr [$j + 1] );

          $N = 1 + $K - $M;
          $A = $N + 2 * $M;

          $edt->K = $K;
          $edt->Degree = $M;

          $edt->PROP1 = $arr [$j + 2];
          $edt->PROP2 = $arr [$j + 3];
          $edt->PROP3 = $arr [$j + 4];
          $edt->PROP4 = $arr [$j + 5];

          $edt->Knot_Sequence = array ();
          $edt->Weights = array ();
          $edt->Control_Points = array ();

          $knotstart = $j + 6;
          $knotend = $knotstart + $A;

          $weightstart = $knotend + 1;
          $weightend = $weightstart + $K;

          $controlpstart = $weightend + 1;
          $controlpend = 9 + $A + (4 * $K) + 2;

          for($x = 0, $i = $knotstart; $i <= ($knotend); $i ++, $x ++) {
            $kn = ($arr [$i]);

            $pos = strpos ( $kn, "D" );

            if ($pos) {
              $kn [$pos] = "E";
            }

            $edt->Knot_Sequence [$x] = $kn;
          }

          for($x = 0, $i = $weightstart; $i <= ($weightend); $i ++, $x ++) {
            $kn = ($arr [$i]);

            $pos = strpos ( $kn, "D" );

            if ($pos) {
              $kn [$pos] = "E";
            }

            $edt->Weights [$x] = $kn;
          }

          for($x = 0, $i = $controlpstart; $i <= ($controlpend); $i ++, $x ++) {
            $kn = ($arr [$i]);

            $pos = strpos ( $kn, "D" );

            if ($pos) {
              $kn [$pos] = "E";
            }

            $edt->Control_Points [$x] = $kn;
          }

          $edt->Control_Pend = count ( $edt->Control_Points );

          // var_dump($edt->Control_Pend);
          // if ($counter == 2)
          // break;

          $edgetype [$id] = $edt;
        }
      }
    }

    // var_dump($edgetype);

    return ($edgetype);
  }
}
