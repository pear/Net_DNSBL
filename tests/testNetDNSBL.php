<?php
require_once "Net/DNSBL.php";
require_once "PHPUnit2/Framework/TestCase.php";

class testNetDNSBL extends PHPUnit2_Framework_TestCase {
    private $rbl;
    
    protected function setUp() {
        $this->rbl = new Net_DNSBL;
    }
    
    public function testHostsAlwaysAreListed() {
        $this->assertEquals(true,  $this->rbl->isListed("127.0.0.2"));
    }

    public function testTrustworthyHostsArentListed() {
        $this->assertEquals(false, $this->rbl->isListed("mail.nohn.net"));
        $this->assertEquals(false, $this->rbl->isListed("212.112.226.205"));
        $this->assertEquals(false, $this->rbl->isListed("smtp1.google.com"));
    }

    public function testSetters() {
        $this->rbl->setRBL(array('dun.dnsrbl.net'));
        $this->assertEquals(array('dun.dnsrbl.net'), $this->rbl->getRBL());
    }

    public function testSettersAndLookups() {
        $this->rbl->setRBL(array('dnsbl.sorbs.net'));
        $this->assertEquals(array('dnsbl.sorbs.net'), $this->rbl->getRBL());
        $this->assertEquals(false, $this->rbl->isListed("mail.nohn.net"));
        $this->assertEquals(true,  $this->rbl->isListed("p50927464.dip.t-dialin.net"));
    }
}
?>