<?php

namespace jrast\vcard;


class vCardProperty extends \ViewableData {

    protected $rawData = null;
    protected $key = null;
    protected $value = null;
    protected $attributes = array();

    function __construct($key = null, $value = null, $attributes = null) {
        if ($key)
            $this->setKey($key);

        if ($value)
            $this->setValue($value);

        if ($attributes)
            $this->setAttributes($attributes);

        parent::__construct();
    }

    public function setRawData($data) {
        $this->rawData = $data;
        $this->processRawData();
        return $this;
    }

    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function setAttributes($attributes) {
        $this->attributes = $attributes;
        return $this;
    }

    
    private function processRawData() {
        // Now we split every line in two parts: Key + additional parameter and value
        list($key, $value) = explode(':', $this->rawData, 2);

        // Trim, Lowercase and remove escaing sequences
        $key = strtolower(trim($this->unescape($key)));

        // If the property is AGENT, assign the value to a new vCard and return
        if ((strpos($key, 'agent') === 0) && (stripos($value, 'begin:vcard') !== false)) {
            $this->key = $key;
            $this->value = new vCard(str_replace('-wrap-', "\n", $value));
            return;            
        }       
        
        $this->parseKey($key);
        $this->parseValue($value);
    }

    private function unescape($text) {
        return str_replace(array('\:', '\;', '\,', "\n"), array(':', ';', ',', ''), $text);
    }
    
    private function parseKey($key) {
        $keyParts = explode(';', $key);
        $key = $keyParts[0];
        
        if(strpos($key, 'item') === 0) {
            $tmpKey = explode('.', $key, 2);
            $key = $tmpKey[1];
        }
        $this->key = $key;
        
        if(count($keyParts) > 1) {
            foreach(array_slice($keyParts, 1) as $item) {
                $this->attributes = array_merge($this->attributes, $this->parseAttribute($item));                
            }
        }
    }
    
    
    private function parseValue($value) {
        $value = trim($this->unescape(str_replace('-wrap-', '', $value)));
        $this->value = $value;
    }
        
    private function parseAttribute($item) {
        $parts = explode('=', $item, 2);
        $key = $parts[0];
        if(key_exists(1, $parts) && strpos($parts[1], ',')) {
            $value = array();
            $subParts = explode(',', $parts[1], 2);
            if(strpos($subParts[1], '=')) {
                $attrib = $this->parseAttribute($subParts[1]);
                if(!key_exists($key, $attrib)) {
                    throw new Exception('vCardPropert: invalid Property found! (' . $this->rawData . ')');
                }
                $value = array_merge(array($attrib[$key]), array($subParts[0]));
            } else {
               $value = $subParts;
            }
        } elseif(key_exists(1, $parts)) {
            $value = $parts[1];
        } else {
            $value = true;
        }
        return array($key => $value);
    }
}

