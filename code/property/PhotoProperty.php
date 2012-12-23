<?php

namespace jrast\vcard;

class PhotoProperty extends VCardProperty {
    protected static $allowed_attributes = array('type');
    
    static protected $allowed_attribute_values = array(
        'type'  => array('gif', 'cgm', 'wmf', 'bmp', 'met', 'pmb', 'dib','pict', 
                         'tiff', 'ps', 'pdf', 'jpeg', 'mpeg', 'mpeg2', 'avi', 'qtime')
    );
}

?>
