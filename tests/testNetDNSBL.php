<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PEAR::Net_DNSBL
 *
 * This class acts as interface to generic Realtime Blocking Lists
 * (RBL)
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * Net_DNSBL looks up an supplied host if it's listed in 1-n supplied
 * Blacklists
 *
 * @category  Net
 * @package   Net_DNSBL
 * @author    Sebastian Nohn <sebastian@nohn.net>
 * @copyright 2004-2007 Sebastian Nohn <sebastian@nohn.net>
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Net_DNSBL Package Home
 * @see       Net_DNS
 * @since     File available since Release 1.0.0
 */

require_once "Net/DNSBL.php";
require_once "PHPUnit/Framework/TestCase.php";

/**
 * TestNetDNSBL
 *
 * This class tests all public Net_DNSBL methods
 *
 * @category Net
 * @package  Net_DNSBL
 * @author   Sebastian Nohn <sebastian@nohn.net>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/net_dnsbl Package Home
 */
class TestNetDNSBL extends PHPUnit_Framework_TestCase
{
    private $rbl;
    
    /**
     * Set up Testcase for Net_DNSBL
     *
     * @return boolean true on success, false on failure
     */
    protected function setUp()
    {
        $this->rbl = new Net_DNSBL;
    }
    
    /**
     * Test if known spam hosts are always identified correctly as such.
     *
     * @return boolean true on success, false on failure
     */
    public function testHostsAlwaysAreListed()
    {
        $this->assertTrue($this->rbl->isListed("127.0.0.2"));
        $this->assertContains("http://www.spamhaus.org/query/bl?ip=127.0.0.2", $this->rbl->getTxt('127.0.0.2'));
        $this->assertContains("http://www.spamhaus.org/SBL/sbl.lasso?query=SBL233", $this->rbl->getTxt('127.0.0.2'));
    }

    /**
     * Test if hosts that should not be know as spam hostsare always
     * identified correctly as such.
     *
     * @return boolean true on success, false on failure
     */
    public function testTrustworthyHostsArentListed()
    {
        $this->rbl->setBlacklists(array('dun.dnsrbl.net'));
        $this->assertFalse($this->rbl->isListed("mail.nohn.net"));
        $this->assertFalse($this->rbl->isListed("212.112.226.205"));
        $this->assertFalse($this->rbl->isListed("smtp1.google.com"));
    }

    /**
     * Test public setters
     *
     * @return boolean true on success, false on failure
     */
    public function testSetters()
    {
        $this->assertTrue($this->rbl->setBlacklists(array('dun.dnsrbl.net')));
        $this->assertEquals(array('dun.dnsrbl.net'), $this->rbl->getBlacklists());
        $this->assertFalse($this->rbl->setBlacklists('dnsbl.sorbs.net'));
    }

    /**
     * Test public setters and include some lookups.
     *
     * @return boolean true on success, false on failure
     */
    public function testSettersAndLookups()
    {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertEquals(array('dnsbl.sorbs.net'), $this->rbl->getBlacklists());
        $this->assertFalse($this->rbl->isListed("mail.nohn.net"));
        $this->assertTrue($this->rbl->isListed("p50927464.dip.t-dialin.net"));
    }

    /**
     * Test getDetails()
     *
     * @return boolean true on success, false on failure
     */
    public function testGetDetails()
    {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertTrue($this->rbl->isListed("p50927464.dip.t-dialin.net"));
        $this->assertEquals(array(
                                  "dnsbl" => "dnsbl.sorbs.net", 
                                  "record" => "127.0.0.10", 
                                  "txt" => array(
                                                 0 => "Dynamic IP Addresses See: http://www.sorbs.net/lookup.shtml?80.146.116.100"
                                                 )
                                  ), $this->rbl->getDetails("p50927464.dip.t-dialin.net"));
        $this->assertFalse($this->rbl->getDetails("mail.nohn.net"));
        $this->assertFalse($this->rbl->getDetails("somehost.we.never.queried"));
    }

    /**
     * Test getListingBl()
     *
     * @return boolean true on success, false on failure
     */
    public function testGetListingBl()
    {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertTrue($this->rbl->isListed("p50927464.dip.t-dialin.net"));
        $this->assertEquals("dnsbl.sorbs.net", $this->rbl->getListingBl("p50927464.dip.t-dialin.net"));
        $this->assertFalse($this->rbl->getListingBl("www.google.de"));
    }

    /**
     * Test getListingRecord()
     *
     * @return boolean true on success, false on failure
     */
    public function testGetListingRecord()
    {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertTrue($this->rbl->isListed("p50927464.dip.t-dialin.net"));
        $this->assertEquals("127.0.0.10", $this->rbl->getListingRecord("p50927464.dip.t-dialin.net"));
        $this->assertFalse($this->rbl->getListingRecord("www.google.de"));
    }

    /**
     * Test getTxt()
     *
     * @return boolean true on success, false on failure
     */
    public function testGetTxt()
    {
        $this->rbl->setBlacklists(array('dnsbl.sorbs.net'));
        $this->assertTrue($this->rbl->isListed("p50927464.dip.t-dialin.net"));
        $this->assertEquals("127.0.0.10", $this->rbl->getListingRecord("p50927464.dip.t-dialin.net"));
        $this->assertEquals(array(0 => "Dynamic IP Addresses See: http://www.sorbs.net/lookup.shtml?80.146.116.100"), $this->rbl->getTxt("p50927464.dip.t-dialin.net"));
        $this->assertFalse($this->rbl->getTxt("www.google.de"));
    }

    /**
     * Test results with multiple blacklists (host not listed)
     *
     * @return boolean true on success, false on failure
     */
    public function testMultipleBlacklists()
    {
        $this->rbl->setBlackLists(array(
                                        'sbl-xbl.spamhaus.org',
                                        'bl.spamcop.net'
                                        ));
        $this->assertFalse($this->rbl->isListed('212.112.226.205'));
        $this->assertFalse($this->rbl->getListingBl('212.112.226.205'));
    }

    /**
     * Test results with multiple blacklists (listed test host)
     *
     * @return boolean true on success, false on failure
     */
    public function testIsListedMulti()
    {
        $this->rbl->setBlackLists(array(
                                        'sbl-xbl.spamhaus.org',
                                        'bl.spamcop.net'
                                        ));
        $this->assertTrue($this->rbl->isListed('127.0.0.2', true));
    }

    /**
     * Test getBlacklists() with multiple blacklists (listed test host)
     *
     * @return boolean true on success, false on failure
     */
    public function testGetListingBls()
    {
        $this->rbl->setBlackLists(array(
                                        'sbl-xbl.spamhaus.org',
                                        'bl.spamcop.net'
                                        ));
        $this->assertTrue($this->rbl->isListed('127.0.0.2', true));
        $this->assertEquals(array(
                                  'sbl-xbl.spamhaus.org',
                                  'bl.spamcop.net'
                                  ), $this->rbl->getListingBls('127.0.0.2'));
        $this->assertFalse($this->rbl->isListed('smtp1.google.com', true));
        $this->assertEquals(false, $this->rbl->getListingBls('smtp1.google.com'));
        $result = $this->rbl->getDetails('127.0.0.2');
        $this->assertContains('127.0.0.2', $result['sbl-xbl.spamhaus.org']['record']);
        $this->assertContains('http://www.spamhaus.org/SBL/sbl.lasso?query=SBL233', $result['sbl-xbl.spamhaus.org']['txt']);
        $this->assertContains('http://www.spamhaus.org/query/bl?ip=127.0.0.2', $result['sbl-xbl.spamhaus.org']['txt']);
        $this->assertContains('127.0.0.2', $result['bl.spamcop.net']['record']);
        $this->assertContains('Blocked - see http://www.spamcop.net/bl.shtml?127.0.0.2', $result['bl.spamcop.net']['txt']);
        $this->assertFalse($this->rbl->getDetails('smtp1.google.com'));
    }

    /**
     * Test without caching.
     *
     * @return boolean true on success, false on failure
     */
    public function testCacheNoCache()
    {
        for ($i=1; $i<=10; $i++) {
            $this->assertFalse($this->rbl->isListed($i.'.nohn.net'));
            $this->assertFalse($this->rbl->isListed(md5(rand()).'.nohn.net'));
        }
    }
}
?>