<?php

namespace jrast\vcard;

/**
 * Structed representation of the name of the persons
 * This Property is mandatory for vCards!
 * 
 * Examples: 
 *   For a Person: N:Public;John;Quinlan;Mr.;Esq.
 *   For a Place:  N:Veni,  Vidi,  Vici;The Restaurant
 */
class NProperty extends VCardProperty {
    protected $familyName;
    protected $givenName;
    protected $additionalNames;
    protected $namePrefix;
    protected $nameSuffix;    
}

?>
