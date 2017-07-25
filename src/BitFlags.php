<?php
  namespace mdeschermeier\bitflags;

  class BitFlags{

    // ATTRIBUTES //============================================================
    private $flagsInt;
    private $flagsArr;
    private $shiftCount;
    private $maxArraySize;
    private $maxIntSize;

    // CONSTRUCTOR //===========================================================
    /**
     * CONSTRUCTOR
     * Initializes attributes and initializes flags, if specified in params.
     * Throws exception when array is too large for system to handle.
     *
     * @param $initFlagArray Associative Array
     */
    public function __construct($initFlagArray = []){
      $this->flagsInt = 0;
      $this->shiftCount = 0;
      $this->flagsArr = [];
      $this->setMaxArraySize();

      if (!empty($initFlagArray)){
        $this->setFlags($initFlagArray);
      }

      return $this->flagsInt;
    }

    // PUBLIC //================================================================

    /**
     * getCompressedFlags()
     * Getter Function for $flags
     *
     * @return int
     * @codeCoverageIgnore
     */
    public function getCompressedFlags(){
      return $this->flagsInt;
    }

    /**
     * getFlagPosition($flag)
     * Returns bit position of specified $flag parameter.
     *
     * @param $flag string
     * @return mixed
     */
    public function getFlagPosition($flag){
      if (isset($this->flagsArr[$flag])){

        // @codeCoverageIgnoreStart
        return $this->flagsArr[$flag];
         // @codeCoverageIgnoreEnd
      }
      return false;
    }

    /**
     * setFlags($flagArray)
     * Assigns Flags specified in Flag Array to a bit and flips bit to initial
     * value, determined by $flagArray. Will throw Exception if flags attempting
     * to be set exceeds $this->maxArraySize's value.
     *
     * @param $flagArray Associative Array
     * @return void
     */
    public function setFlags($flagArray){
      if (count($flagArray) + count($this->flagsArr) > $this->maxArraySize){
        throw new \Exception("Max size of flags array on this system is ".$this->maxArraySize." elements.", 1);
      }

      foreach ($flagArray as $flag => $bool){
        $pos = 1 << $this->shiftCount;
        $this->flagsArr[$flag] = $pos;
        if ($bool){
          $this->toggleFlag($flag);
        }
        $this->shiftCount++;
      }
    }

    /**
     * toggleFlag($flag)
     * Flips the bit associated with the specified Flag. Returns true on success,
     * false if flag does not exist.
     *
     * @param $flag string
     * @return boolean
     */
    public function toggleFlag($flag){
      if (isset($this->flagsArr[$flag])){
        $this->flagsInt = $this->flagsInt ^ $this->flagsArr[$flag];
        return true;
      }
      return false;
    }

    /**
     * enableAllFlags()
     * Enables all set flags.
     *
     * @return void
     */
    public function enableAllFlags(){
      $allOn = 0;
      foreach ($this->flagsArr as $flagPos){
        $allOn = $allOn | $flagPos;
      }
      $this->flagsInt = $allOn;
    }

    /**
     * disableAllFlags()
     * Disables all set flags
     *
     * @return void
     */
    public function disableAllFlags(){
      $this->flagsInt = 0;
    }

    // PRIVATE //===============================================================

    /**
     * setMaxArraySize()
     * Sets maximum array size for flags, based on system architecture.
     * @codeCoverageIgnore
     */
    private function setMaxArraySize(){
      $this->maxArraySize = (PHP_INT_SIZE === 8) ? 63 : 31;
    }



  }

 ?>
