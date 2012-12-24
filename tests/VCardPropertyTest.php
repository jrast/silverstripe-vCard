<?php

use jrast\vcard;

class VCardPropertyTest extends SapphireTest {
    protected $testValues = array(
            array(
                'raw'   => "BEGIN:VCARD",
                'class' => 'VCardProperty',
                'key'   => 'begin',
                'group' => null
            ),
            array(
                'raw'   => "N:Gump;Forrest",
                'class' => 'NProperty',
                'key'   => 'n',
                'group' => null
            ),
            array(
                'raw'   => "A.TEL;HOME:+1-213-555-1234",
                'class' => 'TelProperty',
                'key'   => 'tel',
                'group' => 'A'
            ),
            array(
                'raw'   => "VERSION:2.1",
                'class' => 'VersionProperty',
                'key'   => 'version',
                'group' => null
            )
        );




    public function testCreateFromRawData() {        
        foreach ($this->testValues as $value) {
            $property = vcard\VCardProperty::create_from_raw_data($value['raw']);
            $this->assertEquals('jrast\\vcard\\' . $value['class'] ,$property->class);
            $this->assertEquals($value['key'] ,$property->Key);
            $this->assertEquals($value['group'], $property->Group);
        }
    }
    
}
