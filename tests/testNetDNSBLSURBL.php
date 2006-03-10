<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Net_RBL                                                        |
// +------------------------------------------------------------------------+
// | Copyright (c) 2004 Sebastian Nohn <sebastian@nohn.net>                 |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        | 
// | that is available at http://www.php.net/license/3_0.txt.               | 
// | If you did not receive a copy of the PHP license and are unable to     | 
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 | 
// +------------------------------------------------------------------------+
//
// $Id$
//

require_once "../DNSBL/SURBL.php";
require_once "PHPUnit2/Framework/TestCase.php";

class testNetDNSBLSURBL extends PHPUnit2_Framework_TestCase {
    private $surbl;
    
    protected function setUp() {
        $this->surbl = new Net_DNSBL_SURBL;
    }
    
    public function testSpamUrlsAlwaysGetReportedAsSpam() {
        $this->assertEquals(true,  $this->surbl->isListed("http://surbl-org-permanent-test-point.com/justatest"));
        $this->assertEquals(true,  $this->surbl->isListed("http://wasdavor.surbl-org-permanent-test-point.com/justatest"));
        $this->assertEquals(true,  $this->surbl->isListed("http://127.0.0.2/"));
        $this->assertEquals(true,  $this->surbl->isListed("http://127.0.0.2/justatest"));
    }

    public function testNoSpamUrlsNeverGetReportedAsSpam() {
        $this->assertEquals(false, $this->surbl->isListed("http://www.nohn.net"));
        $this->assertEquals(false, $this->surbl->isListed("http://www.php.net/"));
        $this->assertEquals(false, $this->surbl->isListed("http://www.heise.de/24234234?url=lala"));
        $this->assertEquals(false, $this->surbl->isListed("http://www.nohn.net/blog/"));
        $this->assertEquals(false, $this->surbl->isListed("http://213.147.6.150/justatest"));
        $this->assertEquals(false, $this->surbl->isListed("http://www.google.co.uk/search?hl=en&q=test&btnG=Google+Search&meta="));
    }

    public function testMixedSpamAndNospamUrlsWorkAsExpected() {
        $this->assertEquals(false, $this->surbl->isListed("http://www.nohn.net"));
        $this->assertEquals(true,  $this->surbl->isListed("http://surbl-org-permanent-test-point.com"));
        $this->assertEquals(true,  $this->surbl->isListed("http://wasdavor.surbl-org-permanent-test-point.com/justatest"));
        $this->assertEquals(true,  $this->surbl->isListed("http://127.0.0.2/justatest"));
        $this->assertEquals(false, $this->surbl->isListed("http://213.147.6.150/justatest"));
        $this->assertEquals(true,  $this->surbl->isListed("http://surbl-org-permanent-test-point.com/justatest"));
        $this->assertEquals(false, $this->surbl->isListed("http://www.php.net"));
        $this->assertEquals(false, $this->surbl->isListed("http://www.google.com"));
        $this->assertEquals(false, $this->surbl->isListed("http://www.google.co.uk/search?hl=en&q=test&btnG=Google+Search&meta="));
    }
}
?>
