<?php

namespace jrast\vcard;

/**
 * This property specifies information about another person who will act on
 * behalf of the vCard object.
 * 
 * Example:
 *   AGENT:
 *    BEGIN:VCARD
 *    VERSION:2.1
 *    N:Friday;Fred
 *    TEL;WORK;VOICE:+1-213-555-1234
 *    TEL;WORK;FAX:+1-213-555-5678
 *    END:VCARD
 * 
 */
class AgentProperty extends VCardProperty {
    
    public function setRawValue($data) {
        $data = str_replace('-wrap-', "\n", $data);
        if(strpos($data, "BEGIN:VCARD") === 0) {
            $this->value = new VCard($data);
        } else {
            parent::setRawValue($data);
        }
        return $this;
    }
}

?>
