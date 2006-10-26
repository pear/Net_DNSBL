<?php
require_once "Net/DNSBL.php";
require_once "PHPUnit/Framework/TestCase.php";

class testNetDNSBL extends PHPUnit_Framework_TestCase {
    private $rbl;
    
    protected function setUp() {
        $this->rbl = new Net_DNSBL;
    }
    
    public function testHostsAlwaysAreListed() {
        $this->assertTrue($this->rbl->isListed("127.0.0.2"));
        $this->assertTrue(in_array("http://www.spamhaus.org/query/bl?ip=127.0.0.2", $this->rbl->getTxt('127.0.0.2')));
        $this->assertTrue(in_array("http://www.spamhaus.org/SBL/sbl.lasso?query=SBL233", $this->rbl->getTxt('127.0.0.2')));
    }

    public function testTrustworthyHostsArentListed() {
        $this->rbl->setBlacklists(array('dun.dnsrbl.net'));
        $this->assertFalse($this->rbl->isListed("mail.nohn.net"));
        $this->assertFalse($this->rbl->isListed("212.112.226.205"));
        $this->assertFalse($this->rbl->isListed("smtp1.google.com"));
    }

    public function testSetters() {
        $this->assertTrue($this->rbl->setBlacklists(array('dun.dnsrbl.net')));
        $this->assertEquals(array('dun.dnsrbl.net'), $this->rbl->getBlacklists());
        $this->assertFalse($this->rbl->setBlacklists('dnsbl.sorbs.net'));
    }

    public function testSettersAndLookups() {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertEquals(array('dnsbl.sorbs.net'), $this->rbl->getBlacklists());
        $this->assertFalse($this->rbl->isListed("mail.nohn.net"));
        $this->assertTrue( $this->rbl->isListed("p50927464.dip.t-dialin.net"));
    }

    public function testGetDetails() {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertTrue( $this->rbl->isListed("p50927464.dip.t-dialin.net"));
        $this->assertEquals(array("dnsbl" => "dnsbl.sorbs.net", "record" => "127.0.0.10", "txt" => array(0 => "Dynamic IP Addresses See: http://www.sorbs.net/lookup.shtml?80.146.116.100")), $this->rbl->getDetails("p50927464.dip.t-dialin.net"));
        $this->assertFalse($this->rbl->getDetails("mail.nohn.net"));
        $this->assertFalse($this->rbl->getDetails("somehost.we.never.queried"));
    }

    public function testGetListingBl() {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertTrue( $this->rbl->isListed("p50927464.dip.t-dialin.net"));
        $this->assertEquals("dnsbl.sorbs.net",  $this->rbl->getListingBl("p50927464.dip.t-dialin.net"));
        $this->assertFalse($this->rbl->getListingBl("www.google.de"));
    }

    public function testGetListingRecord() {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertTrue( $this->rbl->isListed("p50927464.dip.t-dialin.net"));
        $this->assertEquals("127.0.0.10",  $this->rbl->getListingRecord("p50927464.dip.t-dialin.net"));
        $this->assertFalse($this->rbl->getListingRecord("www.google.de"));
    }

    public function testGetTxt() {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertTrue($this->rbl->isListed("p50927464.dip.t-dialin.net"));
        $this->assertEquals("127.0.0.10",  $this->rbl->getListingRecord("p50927464.dip.t-dialin.net"));
        $this->assertEquals(array(0 => "Dynamic IP Addresses See: http://www.sorbs.net/lookup.shtml?80.146.116.100"), $this->rbl->getTxt("p50927464.dip.t-dialin.net"));
        $this->assertFalse($this->rbl->getTxt("www.google.de"));
    }


    public function testMultipleBlacklists() {
        $this->rbl->setBlackLists(array(
                                        'sbl-xbl.spamhaus.org',
                                        'bl.spamcop.net'
                                        ));
        $this->assertFalse($this->rbl->isListed('212.112.226.205'));
        $this->assertFalse($this->rbl->getListingBl('212.112.226.205'));
    }
}
?>