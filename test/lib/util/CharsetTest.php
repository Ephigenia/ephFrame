public function testIsASCII() 
{
	$this->assertTrue(Charset::isASCII('abcdefghijklmop'));
	$this->assertFalse(Charset::isASCII(chr(123)));
}

public function testIsUTF8() 
{
	$this->assertTrue(Charset::isUTF8('bä'));
}

public function testToSingleBytes() 
{
	$this->assertEqual(Charset::toSingleBytes('Bär'), 'Baer');
	$this->assertEqual(Charset::toSingleBytes('Ègalité'), 'Egalite');
	$this->assertEqual(Charset::toSingleBytes('ÄÜÖß'), 'AEUEOEss');
}