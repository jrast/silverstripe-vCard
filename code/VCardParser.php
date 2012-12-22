<?php

namespace jrast\vcard;

class VCardParser extends \Object implements \Countable, \IteratorAggregate  {
    protected $filename;
    protected $rawData;
    protected $fileHandle;
    
    protected $cards = null;

    /**
     * The Key of the current card in the cards array
     * @var int|null
     */
    protected $currentCard = null;
    
    /**
     * Open a vcf fiel for parsing
     * @param string $filename
     */
    public function __construct($filename = null) {
        if($filename) {
            $this->setFile($filename);
        }
        
        parent::__construct();
    }
    
    public function setFile($filename) {
        $filename = \Director::getAbsFile($filename);
        $this->filename = $filename;
        $this->prepareRawData();
    }

    private function prepareRawData() {
        if(!is_readable($this->filename)) {
            throw new Exception('VCardParser: Filepath is not accessible (' .$this->filename. ')');
        }
        $this->rawData = file_get_contents($this->filename);
        
        $matches = array(); // Used for PHP < 5.4
        $beginCount = preg_match_all('{^BEGIN\:VCARD}miS', $this->rawData, $matches);
		$endCount = preg_match_all('{^END\:VCARD}miS', $this->rawData, $matches);

        if(($beginCount != $endCount) || !$beginCount) {
            throw new Exception('VCardParser: invalid vCard');
        }
        
        $this->rawData = str_replace("\r", "\n", $this->rawData);
		$this->rawData = preg_replace('{(\n+)}', "\n", $this->rawData);
        
        $splitedCards = preg_split('/(BEGIN:VCARD.+?END:VCARD)\n/s', $this->rawData, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        $this->cards = array();
        $this->currentCard = 0;
        foreach($splitedCards as $card) {
            $this->cards[] = new VCard($card);
        }
    }

    public function count() {
        return count($this->cards);        
    }

    public function getIterator() {
        return new \ArrayIterator($this->cards);
    }
}