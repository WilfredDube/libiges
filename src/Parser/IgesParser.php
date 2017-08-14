<?php

namespace Iges\Parser;

// use Iges\Entity\IgesEntity;

class IgesParser {
  private $_file;
  private $entity;
  private $psection;
  private $gsection;
  public $entity_sum = 0;
  public $emark = 0;
  public $xt;

  function __construct($filename) {
    if (isset($filename))
      $this->_file = fopen ( $filename, "r" );

    $this->entity [] = new IgesEntity();
    $this->psection = new IgesPsection();
    $this->gsection = array ();
    $this->xt = new IgesExtract ();
  }

  public function getPsection() {
    return $this->psection;
  }

  public function getGsection() {
    return $this->gsection;
  }

  // *****************************************Process The D section***********************************************//
  public function get_line() {
    $line = fgets ( $this->_file );
    // do same stuff with the $line

    // echo $line;
    if (strpos ( $line, 'P' ))
    return null;

    return $line;
  }

  public function count_dline() {
    $count = 0;

    $flines = $this->get_line ();

    $lines = $this->jump_to_dsection ( $flines );

    while ( strpos ( $lines, 'D' ) == true ) {
      $lines = $this->get_line ();

      if (strpos ( $lines, 'P' )) {
        // echo "DONE";
        break;
      }

      ++ $count;
    }

    return $count;
  }

  public function read_iges_line() { /* read filerow from iges */
    $i = 0;
    $c = array ();
    $s = "";

    for($i = 0; $i < 80; ++ $i) {
      $c [$i] = fgetc ( $this->_file );

      echo $c [$i];
      if ($i == 0) { /* eat CR LF in line beginning and reset "i" to zero */
        switch ($c [$i]) {
          case '\r' :
          $i --;
          break;
          case '\n' :
          $i --;
          break;
        }
      }
    }

    $s = implode ( "", $c );

    return $s;
  }

  public function get_back() {
    rewind ( $this->_file );
  }

  private function copy_field($from, $poz, $x) {
    // $i=poz;
    $j = 0;
    $to = "";

    $to = substr ( $from, $poz, $x );

    return $to;
  }
  public function jump_to_dsection($s) {
    if ($s == null) {
      return null;
    }

    $i = 72;

    $ch = substr ( $s, 72, 1 );

    while ( $ch != 'D' ) {
      $s = $this->get_line ();
      $ch = substr ( $s, 72, 1 );

      // echo $ch;
      if ($s == null)
      break;

      if ($s == null) {
        break;
      }
    }

    return $s;
  }
  public function print_entity() {
    for($i = 0; $i < count ( $this->entity ); ++ $i)
    echo $this->entity [$i]->EntityType . " Pointer=>" . $this->entity [$i]->PointerData . "<br/>";
  }

  public function parse_d_entry($line1, $line2) {
    /* Parse single entry (pair of lines) in D section. */
    $fld = ""; /* for reading the value of array */
    // static $emark = 0;
    // echo $emark;
    $entity_sum = $this->entity_sum;

    // echo $line1;

    /*
    * Read:
    * entity type (A), $form (B), pointer to PD line (C), PD count (D), layer (E)
    * and if the entity is recognised (406-3,106-2,116,126,128) load it into entity[][]
    */
    // System.out.print($line1);

    $fld = $this->copy_field ( $line1, 73, 8 );
    $linenum = trim ( $fld ); /* Subscript NUmber */
    $emark = $linenum;

    $fld = $this->copy_field ( $line1, 0, 8 );
    $etype = trim ( $fld ); /* Entity Type */

    // echo $etype."dsdsw";

    switch ($etype) {
      // Shell Entity
      case 514:                       /* only layers (514)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // Face Entity
      case 510:                       /* only layers (510)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // Loop IgesEntity
      case 508:                       /* only layers (508)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // Edge Entity
      case 504:                       /* only layers (504)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // Vertex Entity
      case 502:                       /* only layers (502)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // Multi-purpose Entity
      case 406:                       /* only layers (406)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // Color Entity
      case 314:                       /* only layers (314)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // RBS Surface Entity
      case 128:                       /* only layers (128)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 8, 8 );
      $lineweight = trim ( $fld ); /* Line Weight */
      $this->entity [$emark]->LineWeight = $lineweight;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // RBS Curve Entity
      case 126:                       /* only layers (126)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 8, 8 );
      $lineweight = trim ( $fld ); /* Line Weight */
      $this->entity [$emark]->LineWeight = $lineweight;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // Solid B-Rep Object Entity
      case 186:                       /* only layers (186)*/
      $this->entity [$emark] = new IgesEntity ();
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      // $this->entity[$emark]->EntityLabel = elabel;
      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      case 116:                       /* only layers (116)*/
      $this->entity [$emark] = new IgesEntity ();
      // System.out.println($etype);
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 32, 8 );
      $layer = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Layer_or_level = $layer;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 8, 8 );
      $lineweight = trim ( $fld ); /* Line Weight */
      $this->entity [$emark]->LineWeight = $lineweight;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
      // Line Entity
      case 110:                       /* only layers (110)*/
      $this->entity [$emark] = new IgesEntity ();
      // System.out.println($etype);
      $this->entity [$emark]->EntityType = $etype; /* Entity Type */

      $fld = $this->copy_field ( $line1, 8, 8 );
      $pd_ptr = trim ( $fld ); /* PD Pointer */
      $this->entity [$emark]->PointerData = $pd_ptr;

      $fld = $this->copy_field ( $line1, 32, 8 );
      $layer = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Layer_or_level = $layer;

      $fld = $this->copy_field ( $line1, 64, 8 );
      $status = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->Status = $status;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $seqnum = trim ( $fld ); /* $status Number */
      $this->entity [$emark]->SectionCode = $seqnum;

      $fld = $this->copy_field ( $line2, 8, 8 );
      $lineweight = trim ( $fld ); /* Line Weight */
      $this->entity [$emark]->LineWeight = $lineweight;

      $fld = $this->copy_field ( $line2, 24, 8 );
      $pd_count = trim ( $fld ); /* Line Count */
      $this->entity [$emark]->LineCount = $pd_count;

      $fld = $this->copy_field ( $line2, 32, 8 );
      $form = trim ( $fld ); /* $form */
      $this->entity [$emark]->form = $form;

      $fld = $this->copy_field ( $line2, 64, 8 );
      $subscriptnum = trim ( $fld ); /* Subscript Number */
      $this->entity [$emark]->subscriptnumber = $subscriptnum;

      $fld = $this->copy_field ( $line1, 73, 8 );
      $linenum = trim ( $fld ); /* Subscript NUmber */
      $this->entity [$emark]->LineNumber = $linenum;

      $entity_sum ++;
      // $emark++;
      // echo $emark;
      break;
    }
  }

  public function getDsection() {
    $_SESSION ['dsection'] = $this->entity;
    return $this->entity;
  }

  // *****************************************Process The P section***********************************************//
  public function param_section() {
    $tempstring = "";
    $code = "";
    $i = 1;
    $set = false;
    static $x = 0;
    // return to the beginning of the file
    $this->get_back ();

    $total = $this->count_pline ();
    // echo $total;

    $this->get_back ();
    $line = $this->get_pline ();
    $line = $this->jump_to_psection ( $line );

    // echo $line;

    while ( strpos ( $line, 'P' ) == true ) {
      // echo "in";
      if (strpos ( $line, ';' )) {

        if (strpos ( $line, ';' ) && $tempstring == "") {
          $fld = $this->copy_field ( $line, 73, 8 );
          $code = trim ( $fld );
          $this->psection->key = $code;
          // echo $code."<br/>";
        }

        $line = $this->copy_field ( $line, 0, 64 );
        $tempstring .= $line;
        $this->psection->psection [$code] = $tempstring;

        // echo $tempstring."<br/><br/>";
        $tempstring = "";
        $set = false;
        $i = 1;
        // ++$x;
      } else {

        if ($i == 1) {
          $fld = $this->copy_field ( $line, 73, 8 );
          $code = trim ( $fld );
          $this->psection->key = $code;
          // echo $code."<br/>";
        }

        $line = $this->copy_field ( $line, 0, 64 );
        $tempstring .= $line;
        // echo $tempstring."<br/><br/>";

        ++ $i;
      }

      $line = $this->get_pline ();
    }

    $_SESSION ['psection'] = $this->psection->psection;

    return $this->psection->psection;
  }

  public function get_pline() {
    $line = fgets ( $this->_file );
    // do same stuff with the $line

    // echo $line;
    if (strpos ( $line, 'T' ) == true)
    return null;

    return $line;
  }

  public function count_pline() {
    $count = 0;

    $flines = $this->get_pline ();

    $lines = $this->jump_to_psection ( $flines );

    while ( strpos ( $lines, 'P' ) == true ) {
      $lines = $this->get_pline ();

      if (strpos ( $lines, 'T' )) {
        echo "DONE";
        break;
      }

      ++ $count;
    }

    return $count;
  }

  public function jump_to_psection($s) {
    if ($s == null) {
      return null;
    }

    $i = 72;

    $ch = substr ( $s, 72, 1 );

    while ( $ch != 'P' ) {
      $s = $this->get_pline ();
      $ch = substr ( $s, 72, 1 );

      // echo $ch;
      if ($s == null)
      break;

      if ($s == null) {
        break;
      }
    }

    return $s;
  }

  /**
  * ***********************************************************************************************************
  */
  public function get_gline() {
    $line = fgets ( $this->_file );
    // do same stuff with the $line

    // echo $line;
    if (strpos ( $line, 'D' ) == true)
    return null;

    return $line;
  }

  private function multiexplode($delimeters, $string) {
    $ready = str_replace ( $delimeters, $delimeters [0], $string );
    $launch = explode ( $delimeters [0], $ready );

    return $launch;
  }

  public function global_section() {
    $tempstring = "";
    $code = "";
    $i = 1;
    $set = false;
    static $x = 0;

    $this->get_back ();
    $line = $this->get_line ();
    // $line = $this->jump_to_gsection($line);

    while ( ($line = $this->get_gline ()) != null ) {
      // echo $line."<br/>";
      $line = $this->copy_field ( $line, 0, 72 );
      $tempstring = $tempstring . "" . $line;
    }

    $array = $this->multiexplode ( array (
      ",",
      ";"
    ), $tempstring );
    $this->gsection = $array;

    $_SESSION ['gsection'] = array ();
		$_SESSION ['gsection'] = $this->gsection;

    return $this->gsection;
  }


  public function end() {
		$_SESSION ['dsection'] = $this->entity;
	}
}
