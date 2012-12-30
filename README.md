# Silverstripe vCard Module

## Usage
### Loading data
To load a vCard file create a new `VCardParser` and set the path to the file:
```php
$vCards = new VCardParser('vcard/tests/Example3.0.vcf');
```
Allways use the VCardParser class to load the data from a file. Do not load data directly to a VCard Object as the data has to be escapped before processing.

Once loaded the data, you can access the cards within a foreach loop:
```php
foreach($vCards as $vCard) {
  $vCard->Property('N')->Value; // returns the value of the N Property
  $vCard->Property('TEL'); // Returns an array with all TEL Properties of the current vCard
  $vCard->Property('TEL', 'work'); // Returns an array with TEL Properties containing 'WORK' attribute
  $vCard->Property('A:TEL', 'home'); // returns an array with TEL Properties in the group 'A' and containing the 'WORK' attribute  
}
```

Watch out: the key and the attributes are not case sensitive. The following lines will return the same:
```php
$vCard->Property('TEL', 'work');
$vCard->Property('tel', 'WORK');
```

The Group **IS** case sensitive! So be sure your filter matches the group exactly!


## See also
* vCard 2.1 and vCard 3.0 specifications: http://www.imc.org/pdi/pdiproddev.html
* vCard 4.0 [RFC6350]: http://www.rfc-editor.org/rfc/pdfrfc/rfc6350.txt.pdf
* vCard Extensions for Instant Messaging (IM): http://tools.ietf.org/html/rfc4770
* vCard MIME Directory Profile: http://tools.ietf.org/html/rfc2426
* A MIME Content-Type for Directory Information: http://tools.ietf.org/html/rfc2425
