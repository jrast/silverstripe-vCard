<?php

namespace jrast\vcard;

/**
 * Property for a complete Adress
 * 
 * Example:
 *   ADR;DOM;HOME:P.O. Box 101;Suite 101;123 Main Street;Any Town;CA;91921-1234;
 */
class AdrProperty extends VCardProperty {
    protected static $allowed_attributes = array('type');
    
    protected static $allowed_attribute_values = array(
        'type'  => array('dom', 'intl', 'postal', 'parcel', 'home', 'work')
    );
}
