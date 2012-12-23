<?php

namespace jrast\vcard;

class VCard extends \ViewableData {
    protected $rawData = null;


    /**
     * Contains the data of a single VCard
     * @var array
     */
    protected $data = null;
    
    public function __construct($data = null) {
        if($data) {
            $this->setData($data);
        }
        parent::__construct();
    }
    
    public function setData($data) {
        $this->rawData = $data;
        $this->prepareRawData();
        $this->extractData();
    }
    
    private function prepareRawData() {
        // Protect the BASE64 final = sign (detected by the line beginning with whitespace), otherwise the next replace will get rid of it
		$this->rawData = preg_replace('{(\n\s.+)=(\n)}', '$1-base64=-$2', $this->rawData);

		// Joining multiple lines that are split with a hard wrap and indicated by an equals sign at the end of line
		// (quoted-printable-encoded values in v2.1 vCards)
		$this->rawData = str_replace("=\n", '', $this->rawData);

		// Joining multiple lines that are split with a soft wrap (space or tab on the beginning of the next line
		$this->rawData = str_replace(array("\n ", "\n\t"), '-wrap-', $this->rawData);

		// Restoring the BASE64 final equals sign (see a few lines above)
		$this->rawData = str_replace("-base64=-\n", "=\n", $this->rawData);
    }
    
    
    private function extractData() {
        $lines = explode("\n", $this->rawData);
        $this->data = array();        
        foreach($lines as $line) {
            $property = VCardProperty::create_from_raw_data($line);
            $this->data[] = $property;         
        }
        
        \Debug::dump($this->data);
    }
    
}