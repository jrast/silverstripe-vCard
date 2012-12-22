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
class TelProperty {
    protected static $allowed_parameters = array('type');
}

?>
