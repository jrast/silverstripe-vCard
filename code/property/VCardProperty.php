<?php

namespace jrast\vcard;

use \Exception;


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
    
    static protected $allowed_attribute_values = array(
        'encoding'  => array('base64', 'quoted-printable', '8bit'),
        'charset'   => array('any'),
        'language'  => array('any'),
        'value'     => array('inline', 'cid', 'content-id', 'url', 'uri')
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
                (?P<attrib> [a-z0-9-,=;]+?)  # there are Attributes
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
    
    /**
     * 
     * @return array - all allowed attribute keys for this property
     * @see VCardProperty::$allowed_attributes
     */
    public function getAllowedAttributes() {
        return \Config::inst()->get($this->class, 'allowed_attributes');
    }
    
    public function getAllowedAttributeValues() {
        return \Config::inst()->get($this->class, 'allowed_attribute_values');
    }

    /**
     * Set some raw data for this vCard. The data is not processed at all.
     * @param string $data
     * @return \jrast\vcard\VCardProperty
     */
    public function setRawData($data) {
        $this->rawData = $data;
        return $this;
    }
    
    /**
     * Set the attributes as string.
     * The attributes are paresed and saved to the attributes arrays
     * 
     * @param string $attributes
     * @see VCardProperty::$attributes
     * @return \jrast\vcard\VCardProperty
     */
    public function setRawAttributes($attributes) {
        $regex_attributes = "/(?P<key>[a-z0-9-]+)=?(?P<value> [a-z0-9-]+)?,?/ix";
        $attrib = array();
        if($attributes != '') {
            $parts = array();
            $result = preg_match_all($regex_attributes, $attributes, $parts);
            if(!$result) {
                $this->attributes = $attrib;
                return;
            }
            $keys = $parts['key'];
            $values = $parts['value'];
            $allowed_keys = $this->getAllowedAttributes();
            $allowed_values = $this->getAllowedAttributeValues();
            foreach ($keys as $index => $key) {
                $key = strtolower($key);
                // The key is in the allowed_keys array
                if(in_array($key, $allowed_keys)) {
                    $value = strtolower($values[$index]);
                    if(array_key_exists($key, $allowed_values)) {
                        // For this key exists a allowed values array
                        if(in_array($value, $allowed_values[$key])) {
                            // Otherwise we check if the value is in the array
                            $attrib[$key][] = $value;   
                        } else {
                            // OK, we found a key wich is not allowed
                            $allowedValues = implode(', ', $allowed_values[$key]);
                            throw new Exception("$this->class: '$value' not allowed for attribute '$key'! Allowed values are: $allowedValues");
                        }                        
                    } else {
                        // For this key, no array with allowed values exist. we just save the key and asume any value is allowed
                        $attrib[$key][] = $value;
                        throw new Exception("$this->class: '$value' not allowed for attribute '$key'! Allowed values are: $allowedValues");
                    }
                } else {
                    $keyIsAllowed = false;
                    foreach($allowed_values as $otherKey => $otherValues) {
                        if(in_array($key, $otherValues)) {
                            $attrib[$otherKey][] = $key;
                            $keyIsAllowed = true;
                        }
                    }
                    if(!$keyIsAllowed) {
                        throw new Exception(sprintf("%s: '%s' not allowed as attribute!", $this->class, $key));
                    }
                }
            }
        }
        $this->setAttributes($attrib);
        return $this;
    }
    
    /**
     * Set the raw value
     * @param string $value
     * @return \jrast\vcard\VCardProperty
     */
    public function setRawValue($value) {
        $this->setValue($value);
        return $this;
    }
    
    /**
     * Set a group for the this property. make sure the group has a valid name!
     * @param type $group
     * @return \jrast\vcard\VCardProperty
     */
    public function setGroup($group) {
        $this->group = $group;
        return $this;
    }

    /**
     * Set the key for this property.
     * @param type $key
     * @throws \Exception
     * @return \jrast\vcard\VCardProperty
     */
    public function setKey($key) {
        $class = get_called_class();
        if($class != __CLASS__) {
            if(self::get_classname_from_key($key) != $class) {
                throw new Exception("VCardProperty: You can't set the key to '$key' on $class. Use a VCardProperty instead");
            }
        }
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
            throw new Exception("VCard: Line does not match vCard-property pattern: $data");
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
