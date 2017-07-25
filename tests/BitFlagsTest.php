<?php
  require_once "src/BitFlags.php";

  use PHPUnit\Framework\TestCase;
  use mdeschermeier\bitflags\BitFlags;

  class BitFlagsTest extends TestCase{
    private $max_size;

    public function SetUp(){
      $this->max_size = (PHP_INT_SIZE === 8) ? 63 : 31;
    }

    // TESTS //=================================================================

    public function testConstructorWillNotSetFlagsForOversizedInitArrays(){
      $this->expectException(Exception::class);
      $break = new BitFlags($this->buildFlagArray($this->max_size+1));
    }

    public function testMaxLengthFlagArraysCompressCorrectly(){
      $BF = new BitFlags($this->buildFlagArray($this->max_size));

      $this->assertEquals(9223372036854775807, $BF->getCompressedFlags());
    }

    public function testSetFlagsCorrectlySetsAllFlags(){
      for ($i = 0; $i < 1000; $i++){
        $BF = new BitFlags();
        $tArray = $this->buildFlagArray(10, true);
        $BF->setFlags($tArray);
        $this->assertEquals($this->getIntVal($tArray), $BF->getCompressedFlags());
      }
    }

    public function testToggleFlagCorrectlyFlipsBitsWhenFlagsAreSet(){
      $tArray = $this->buildFlagArray(8, true);
      $BF = new BitFlags($tArray);
      for ($i = 0; $i < 1000; $i++){
        $flip = "FLAG_".(mt_rand() % 7);
        $tArray[$flip] = $tArray[$flip] ? false : true;
        $BF->toggleFlag($flip);
        $this->assertEquals($this->getIntVal($tArray), $BF->getCompressedFlags());
      }

    }

    public function testToggleFlagReturnsFalseWhenFlagNameDoesNotExist(){
      $exists = $this->buildFlagArray(10);
      $BF = new BitFlags($exists);

      $this->assertFalse($BF->toggleFlag("NOT_REAL"));
      $this->assertFalse($BF->toggleFlag("ALSO_NOT_SET"));
      $this->assertFalse($BF->toggleFlag("FLAG_10"));
    }

    public function testEnableAllFlagsEnablesAllFlags(){
      for ($i = 0; $i < 1000; $i++){
        $randFlags = $this->buildFlagArray((mt_rand() % $this->max_size), true);
        $BF = new BitFlags($randFlags);
        foreach($randFlags as $flag => $val){
          $randFlags[$flag] = true;
        }
        $BF->enableAllFlags();
        $this->assertEquals($this->getIntVal($randFlags), $BF->getCompressedFlags());
      }
    }

    public function testDisableAllFlagsDisablesAllFlags(){
      for ($i = 0; $i < 1000; $i++){
        $randFlags = $this->buildFlagArray((mt_rand() % $this->max_size), true);
        $BF = new BitFlags($randFlags);
        $BF->disableAllFlags();
        $this->assertEquals(0, $BF->getCompressedFlags());
      }
    }

    public function testGetFlagPositionReturnsFalseIfFlagDoesNotExist(){
      $exists = $this->buildFlagArray(5);
      $BF = new BitFlags($exists);

      $this->assertFalse($BF->getFlagPosition("NOT_A_FLAG"));
      $this->assertFalse($BF->getFlagPosition("THIS_DOES_NOT_EXIST"));
      $this->assertFalse($BF->getFlagPosition("BOOP"));
    }

    public function testSetCompressedFlagsReturnsFalseIfNumberTooLarge(){
      $num = pow(2,64)-5000;
      $BF = new BitFlags();
      $this->assertFalse($BF->setCompressedFlags($num));
    }

    public function testSetCompressedFlagsReturnsFalseIfNumberLessThanZero(){
      $num = -5000;
      $BF = new BitFlags();
      $this->assertFalse($BF->setCompressedFlags($num));
    }

    public function testSetCompressedFlagsReturnsTrueIfNumberValid(){
      $BF = new BitFlags();
      $this->assertTrue($BF->setCompressedFlags(21));
    }

    // PRIVATE //===============================================================
    private function getIntVal($flagArray){
      $counter = 0;
      $intVal = 0;
      foreach($flagArray as $flagVal){
        // Set Position of Flag's Bit
        $flagPos = 1 << $counter;
        // If flag set to true, flip bit in $intVal.
        if ($flagVal){
          $intVal = $intVal ^ $flagPos;
        }
        $counter++;
      }
      return $intVal;

    }

    private function buildFlagArray($numFlags, $randBits = false){
      $iArray = [];

      for ($i = 0; $i < $numFlags; $i++){
        //If $randBits, select random T/F - else assign true.
        $iArray['FLAG_'.$i] = (mt_rand() % 2 === 0 || !$randBits) ?  true : false;
      }
      return $iArray;
    }
  }

 ?>
