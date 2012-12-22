<?php

namespace jrast\vcard;

/**
 * This property specifies the name and optional the unit(s) of the organization
 * associated with this vCard object.
 * 
 * Example:
 *  ORG:ABC, Inc.;North American Division;Marketing 
 */
class OrgProperty extends VCardProperty {
    protected $OrgName = null;
    protected $OrgUnit = null;
    protected $OrgAdditions = null;
}

