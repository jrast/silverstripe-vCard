<?php

namespace jrast\vcard;

/**
 * Emailadress of the vCard person/object
 * 
 * Examples:
 *   EMAIL;INTERNET:john.public@abc.com
 */
class EmailProperty extends VCardProperty {
    protected static $allowed_attributes = array('type');
    
    static protected $allowed_attribute_values = array(
        'type'  => array('pref', 'home', 'work', 'internet')
    );
}

?>
