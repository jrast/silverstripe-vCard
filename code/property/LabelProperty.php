<?php

namespace jrast\vcard;

/**
 * Adressing label for physical delivery to the person/object assosiated with
 * the vCard.
 */
class LabelProperty extends VCardProperty {
    protected static $allowed_attributes = array('type');
    
    protected static $allowed_attribute_values = array(
        'type'  => array('dom', 'intl', 'postal', 'parcel', 'home', 'work')
    );
}

?>
