<?php

namespace jrast\vcard\test;

use jrast\vcard;
use \SapphireTest;
use \Exception;

class VCardTest extends SapphireTest {
    
    public function getExampleData($version) {
        $data =file_get_contents(\Director::getAbsFile('vcard/tests/Example'. $version . '.vcf'));
		return preg_replace('{(\n+)}', "\n", str_replace("\r", "\n", $data));        
    }


    public function testCreateFromRawData() { 
        $data = $this->getExampleData('2.1');
        $vCard = vcard\VCard::create($data);
        
        // Get some values from the card:
        $this->assertEquals("Gump;Forrest", $vCard->Property('N')->Value);
    }
    
}

?>
