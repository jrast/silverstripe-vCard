<?php

namespace jrast\vcard;

/**
 * Canonical number string for a telephone number.
 * 
 * Examples:
 *   TEL;PREF;WORK;MSG;FAX:+1-800-555-1234
 *   TEL;WORK;HOME;VOICE;FAX:+1-800-555-1234
 * 
 */
class TelProperty extends VCardProperty {
    protected static $allowed_attributes = array('type');
    
    protected static $allowed_attribute_values = array(
        'type'  => array('pref', 'work', 'home', 'voice', 'fax', 'msg', 'cell', 'pager', 'bbs', 'modem', 'car', 'isdn', 'video')
    );
}

?>
