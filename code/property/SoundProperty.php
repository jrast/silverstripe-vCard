<?php

namespace jrast\vcard;

/**
 * Examples:
 *   SOUND:JON Q PUBLIK
 *   SOUND;VALUE=URL:file///multimed/audio/jqpublic.wav
 *   
 */
class SoundProperty extends VCardProperty {
    protected static $allowed_parameters = array('type');
}

?>
