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
}

?>
