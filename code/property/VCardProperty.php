<?php

namespace jrast\vcard;


class VCardProperty extends \ViewableData {
    /**
     * These attributes are allowed for all properties
     * Override this field in child classes which have more allowed parameters
     * 
     * @see VCardProperty::getAllowedAttributes()
     * @var array
     */
    static protected $allowed_attributes = array(
        'encoding', 'charset', 'language', 'value'
    );
    
    /**
     * With this Regex, a single line of a vCard can be split into the
     * different parts. the group and the key is extracted and ready for use,
     * attributes and the value must be processed individual.
     * @var string
     */
    static protected $property_regex =
            "~^
              (?: (?P<group>[a-z0-9]+?)\.)? # The line can start with a group name which is seperated with a dot from the key name
                (?P<key> [a-z0-9-]+?)       # Then, the key follows
              (?: ;                         # If there is a ; after the key,
                (?P<attrib> [a-z0-9,=;]+?)  # there are Attributes
              )?                            
              :                             # then we get to the seperator between the keypart and value
              (?P<value>.*)                 # we capture the value;
            $~ix";

    protected $rawData = null;
    protected $group = null;
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
    
    public function getAllowedAttributes() {
        return \Config::inst()->get($this->class, 'allowed_attributess');
    }

    public function setRawData($data) {
        $this->rawData = $data;
        return $this;
    }
    
    public function setRawAttributes($attributes) {
        $this->setAttributes($attributes);
        return $this;
    }
    
    public function setRawValue($value) {
        $this->setValue($value);
        return $this;
    }
    
    public function setGroup($group) {
        $this->group = $group;
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
    
    public function getValue() {
        return $this->value;
    }

    public function setAttributes($attributes) {
        $this->attributes = $attributes;
        return $this;
    }

/*
 * This are some old functions wich are no longer needed.
 * 


    private function unescape($text) {
        return str_replace(array('\:', '\;', '\,', "\n"), array(':', ';', ',', ''), $text);
    }
    
    
    private function parseValue($value) {
        $value = trim($this->unescape(str_replace('-wrap-', '', $value)));
        $this->value = $value;
    } 
 */
    
    
    /**
     * Creates a new VCardProperty-Object (or the correct Subclass according to
     * the key) for a single line in a vCard.s
     * 
     * @param string $data
     * @return VCardProperty a VCardProperty or a Subclass of VCardPropertys
     * @throws Exception
     */
    public static function create_from_raw_data($data) {
         $parts = array();            
         $result = preg_match(self::$property_regex, $data, $parts);
         if($result != 1) {
            throw new \Exception("VCard: Line does not match vCard-property pattern: $data");
         }
         $key = $parts['key'];
         $class = self::get_classname_from_key($key);
         if(class_exists($class)) {
             $property = $class::create();
         } else {
             $property = VCardProperty::create();
         }
         $property->setRawData($data);
         $property->setGroup($parts['group']);
         $property->setKey($parts['key']);
         $property->setRawAttributes($parts['attrib']);
         $property->setRawValue($parts['value']);
         return $property;   
    }
    
    
    /**
     * Get the correct classname for a property
     * @param string $key
     * @return string the classname for the property
     */
    protected static function get_classname_from_key($key) {
        return __NAMESPACE__ . '\\' . ucfirst(strtolower($key)) . 'Property';
    }
}
