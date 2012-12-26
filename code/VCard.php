<?php

namespace jrast\vcard;

use \ViewableData;
use \Debug;

class VCard extends ViewableData {
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
        // We don't need the raw data anymore
        $this->rawData = null;
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
    
    
    /**
     * Creates all Properties from the raw data.
     */
    private function extractData() {
        $lines = explode("\n", $this->rawData);
        $this->data = array();
        new \ArrayList();
        foreach($lines as $line) {
            $property = VCardProperty::create_from_raw_data($line);
            $this->data[] = $property;         
        }
    }
    
    public function Property(){
        if(func_num_args() == 0) {
            throw new \Exception('You must provide at leas one argument! (The key)');
        }        
        $arguments = func_get_args();
        //Debug::dump($arguments);
        $GroupKey = explode(':', $arguments[0]);
        if(count($GroupKey) > 2) {
            throw new \Exception("First Argument must be either in the form of 'Key' or 'Group:Key'!" );
        }
        if(count($GroupKey) == 1) {
            $key = $GroupKey[0];
            $group = null;
            array_shift($arguments);
        } else {
            $key = $GroupKey[1];
            $group = $GroupKey[0];
            array_shift($arguments);
            array_shift($arguments);
        }
        
        $properties = $this->filterKey($this->data, $key);
        if($group)
            $properties = $this->filterGroup($properties, $group);
        if(count($arguments) > 0)
            $properties = $this->filterAttributes($properties, $arguments);
        return reset($properties);
    }
    
    private function filterKey($data, $key) {
        $properties = array();
        foreach($data as $prop) {
            if($prop->Key == strtolower($key)) {
                $properties[] = $prop;
            }
        }
        return $properties;
    }
    
    private function filterGroup($data, $group) {
        $properties = array();
        foreach($data as $prop) {
            if($prop->Group == strtolower($group)) {
                $properties[] = $prop;
            }
        }
        return $properties;
    }
    
    private function filterAttributes($data, $attributes) {
        $properties = array();
        foreach($data as $prop) {
            $propAttributes = $prop->getFlatAttributes();
            foreach($propAttributes as $attrib) {
                if(in_array($attrib, $attributes)) {
                    $properties[] = $prop;
                    continue;
                }
            }
        }
        return $properties;
    }
}