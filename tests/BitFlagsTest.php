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
      $FC = new BitFlags($this->buildFlagArray($this->max_size));

      $this->assertEquals(9223372036854775807, $FC->getCompressedFlags());
    }

    public function testSetFlagsCorrectlySetsAllFlags(){
      for ($i = 0; $i < 1000; $i++){
        $FC = new BitFlags();
        $tArray = $this->buildFlagArray(10, true);
        $FC->setFlags($tArray);
        $this->assertEquals($this->getIntVal($tArray), $FC->getCompressedFlags());
      }
    }

    public function testToggleFlagCorrectlyFlipsBitsWhenFlagsAreSet(){
      $tArray = $this->buildFlagArray(8, true);
      $FC = new BitFlags($tArray);
      for ($i = 0; $i < 1000; $i++){
        $flip = "FLAG_".(mt_rand() % 7);
        $tArray[$flip] = $tArray[$flip] ? false : true;
        $FC->toggleFlag($flip);
        $this->assertEquals($this->getIntVal($tArray), $FC->getCompressedFlags());
      }

    }

    public function testToggleFlagReturnsFalseWhenFlagNameDoesNotExist(){
      $exists = $this->buildFlagArray(10);
      $FC = new BitFlags($exists);

      $this->assertFalse($FC->toggleFlag("NOT_REAL"));
      $this->assertFalse($FC->toggleFlag("ALSO_NOT_SET"));
      $this->assertFalse($FC->toggleFlag("FLAG_10"));
    }

    public function testEnableAllFlagsEnablesAllFlags(){
      for ($i = 0; $i < 1000; $i++){
        $randFlags = $this->buildFlagArray((mt_rand() % $this->max_size), true);
        $FC = new BitFlags($randFlags);
        foreach($randFlags as $flag => $val){
          $randFlags[$flag] = true;
        }
        $FC->enableAllFlags();
        $this->assertEquals($this->getIntVal($randFlags), $FC->getCompressedFlags());
      }
    }

    public function testDisableAllFlagsDisablesAllFlags(){
      for ($i = 0; $i < 1000; $i++){
        $randFlags = $this->buildFlagArray((mt_rand() % $this->max_size), true);
        $FC = new BitFlags($randFlags);
        $FC->disableAllFlags();
        $this->assertEquals(0, $FC->getCompressedFlags());
      }
    }

    public function testGetFlagPositionReturnsFalseIfFlagDoesNotExist(){
      $exists = $this->buildFlagArray(5);
      $FC = new BitFlags($exists);

      $this->assertFalse($FC->getFlagPosition("NOT_A_FLAG"));
      $this->assertFalse($FC->getFlagPosition("THIS_DOES_NOT_EXIST"));
      $this->assertFalse($FC->getFlagPosition("BOOP"));
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
