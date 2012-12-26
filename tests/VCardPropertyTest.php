<?php

namespace jrast\vcard\test;

use jrast\vcard;
use \SapphireTest;
use \Exception;

class VCardPropertyTest extends SapphireTest {
    protected $testValues = array(
            array(
                'raw'           => "BEGIN:VCARD",
                'class'         => 'VCardProperty',
                'key'           => 'begin',
                'group'         => null,
                'attributes'    => array() 
            ),
            array(
                'raw'           => "N:Gump;Forrest",
                'class'         => 'NProperty',
                'key'           => 'n',
                'group'         => null,
                'attributes'    => array()
            ),
            array(
                'raw'           => "A.TEL;HOME:+1-213-555-1234",
                'class'         => 'TelProperty',
                'key'           => 'tel',
                'group'         => 'A',
                'attributes'    => array('type' => array('home'))
            ),
            array(
                'raw'           => "TEL;HOME;VOICE:(404) 555-1212",
                'class'         => 'TelProperty',
                'key'           => 'tel',
                'group'         => null,
                'attributes'    => array(
                        'type'  => array('home', 'voice'))
            ),
            array(
                'raw'           => "VERSION:2.1",
                'class'         => 'VersionProperty',
                'key'           => 'version',
                'group'         => null,
                'attributes'    => array()
            ),
            array(
                'raw'           => "LABEL;WORK;ENCODING=QUOTED-PRINTABLE:100 Waters Edge=0D=0ABaytown, LA 30314=0D=0AUnited States of America",
                'class'         => 'LabelProperty',
                'key'           => 'label',
                'group'         => null,
                'attributes'    => array(
                        'type'      => array('work'),
                        'encoding'  => array('quoted-printable'))
            ),
            array(
                'raw'           => "PHOTO;VALUE=URL;TYPE=GIF:http://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Example_svg.svg/200px-Example_svg.svg.png",
                'class'         => 'PhotoProperty',
                'key'           => 'photo',
                'group'         => null,
                'attributes'    => array(
                        'value'     => array('url'),
                        'type'      => array('gif'))
            ),
            array(
                'raw'           => "N;LANGUAGE=DE-DE:Gump;Forrest",
                'class'         => 'NProperty',
                'key'           => 'n',
                'group'         => null,
                'attributes'    => array(
                        'language'  => array('de-de')
                )
            )
        );
    





    public function testCreateFromRawData() {        
        foreach ($this->testValues as $value) {
            $property = vcard\VCardProperty::create_from_raw_data($value['raw']);
            $this->assertEquals('jrast\\vcard\\' . $value['class'] ,$property->class);
            $this->assertEquals($value['key'] ,$property->Key);
            $this->assertEquals($value['group'], $property->Group);
            foreach($property->Attributes as $key => $attributeArray) {
                foreach($attributeArray as $attribute) {
                    $this->assertTrue(
                        in_array($attribute, $value['attributes'][$key]),
                        sprintf("Failed finding '%s' in attributes for the key '%s'! Object: %s", $attribute, $key, $property->class));
                }
            }
        }
    }
    
   
    public function testCreateFromInvalidRawData() {
        $invalidRawData = array(
            // Missing ":"
            'ORG Bubba Gump Shrimp Co.',
        );
        foreach($invalidRawData as $data) {
            try {
                vcard\VCardProperty::create_from_raw_data($data);
            } catch (Exception $e) {
                continue;
            }
            $this->fail('An expected exception has not been raised.');
        }
        // OK, if we got so far, we mark the test as complete.
        $this->assertTrue(true);
    }
    
    
    /**
     * Test the different cases of how invalid attribute strings are handled.
     */
    public function testSetInvalidRawAttributes() {
        $values = array(
            array('not_allowed_attribute'   => "this_should_raise_an_exception"),
            array('not_allowed_value'       => "VALUE=something")
        );
        
        foreach($values as $value) {
            reset($value);
            $expected = key($value);
            $attributes = $value[$expected];
            $property = vcard\VCardProperty::create();
            switch ($expected) {
                case 'not_allowed_attribute':
                    try {
                       $property->setRawAttributes($attributes); 
                    } catch (Exception $e) {
                        break;
                    }
                    $this->fail('An Exception should have been thrown!');
                    break;
                case 'not_allowed_value':
                    try {
                       $property->setRawAttributes($attributes);
                    } catch (Exception $e) {
                        break;                        
                    }
                    $this->fail('An Exception should have been thrown!');
                    break;
                default :
                    $this->markTestIncomplete("The handling for this exepted value ($expected) is not implemented");
            }            
        }
        // OK, if we got so far, we mark the test as complete.
        $this->assertTrue(true);
    }


    /**
     * If a new Property is created, the key shoud be set automaticly.
     */
    public function testKeySetAfterCreate() {
        // If a property of the baseclass is created, the key remains null
        $property = vcard\VCardProperty::create();
        $this->assertEquals(null, $property->Key);
        
        // If a specific property is created, the key is set
        // This list covers not all classes, but as all behave the same, this should be enough
        $property = vcard\AdrProperty::create();
        $this->assertEquals('adr', $property->Key);
        
        $property = vcard\AgentProperty::create();
        $this->assertEquals('agent', $property->Key);
        
        $property = vcard\BdayProperty::create();
        $this->assertEquals('bday', $property->Key);
        
        $property = vcard\EmailProperty::create();
        $this->assertEquals('email', $property->Key);
        
        $property = vcard\FnProperty::create();
        $this->assertEquals('fn', $property->Key);
        
        $property = vcard\LogoProperty::create();
        $this->assertEquals('logo', $property->Key);
        
        $property = vcard\MailerProperty::create();
        $this->assertEquals('mailer', $property->Key);
        
        $property = vcard\NProperty::create();
        $this->assertEquals('n', $property->Key);
        
        $property = vcard\NoteProperty::create();
        $this->assertEquals('note', $property->Key);
        
        $property = vcard\OrgProperty::create();
        $this->assertEquals('org', $property->Key);
    }
    
    
    public function testConstructorWithArguments() {
        $property = new vcard\VCardProperty('N');
        $this->assertEquals('n', $property->Key);
        
        $property = new vcard\VCardProperty('N', 'Max Muster');
        $this->assertEquals('n', $property->Key);
        $this->assertEquals('Max Muster', $property->Value);
    }
    
    public function testSetWrongKey() {        
        // We can't change the key on a existing, specific class:
        $property = vcard\TelProperty::create();
        $exception = false;
        try {
            $property->Key = 'WrongValue';
        } catch (Exception $exc) {
            $exception = true;
        }
        $this->assertTrue($exception, 'No exception thrown if the key is altered!');
        
        
        // If we change the key on a vCardProperty object, the specific class is returned:
        $property = vcard\VCardProperty::create();
        $property = $property->setKey('TEL');
        $this->assertEquals('jrast\\vcard\\TelProperty', $property->class);
    }
}
