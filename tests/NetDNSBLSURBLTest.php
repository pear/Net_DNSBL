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
 * @copyright 2004-2009 Sebastian Nohn <sebastian@nohn.net>
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Net_DNSBL Package Home
 * @see       Net_DNS
 * @since     File available since Release 1.0.0
 */

require_once 'Net/DNSBL/SURBL.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * TestNetDNSBLSURBL
 *
 * This class tests all public Net_DNSBL_SURBL methods
 *
 * @category Net
 * @package  Net_DNSBL
 * @author   Sebastian Nohn <sebastian@nohn.net>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/net_dnsbl Package Home
 */

class NetDNSBLSURBLTest extends PHPUnit_Framework_TestCase
{
    private $_surbl;

    /**
     * Set up Testcase for Net_DNSBL_SURBL
     *
     * @return boolean true on success, false on failure
     */
    protected function setUp()
    {
        $this->_surbl = new Net_DNSBL_SURBL;
    }

    /**
     * Tests if a URL is always correctly identified as such when
     * using DBL
     *
     * @return boolean true on success, false on failure
     */
    public function testDblAlsoWorksForSURBL()
    {
        $this->_surbl->setBlacklists(array('dbl.spamhaus.org'));
        $this->assertTrue(
            $this->_surbl->isListed(
                'http://dbltest.com/demo'
            )
        );
        $this->assertEquals(array(0 => 'http://www.spamhaus.org/query/dbl?domain=dbltest.com'), $this->_surbl->getTxt('http://dbltest.com/demo'));
        $this->assertFalse(
            $this->_surbl->isListed(
                'http://example.com/demo'
            )
        );
        $this->assertEquals(false, $this->_surbl->getTxt('http://example.com/demo'));
    }
    
    /**
     * Tests if a test spam URL is always correctly identified as such. 
     *
     * @return boolean true on success, false on failure
     */
    public function testSpamUrlsAlwaysGetReportedAsSpam()
    {
        $this->assertTrue(
            $this->_surbl->isListed(
                'http://surbl-org-permanent'.
                '-test-point.com/justatest'
            )
        );
        $this->assertEquals(
            array(0 => 'multi.surbl.org permanent test point'), 
            $this->_surbl->getTxt(
                'http://surbl-org-permanent-test-point.com/'.
                'justatest'
            )
        );
        $this->assertTrue(
            $this->_surbl->isListed(
                'http://wasdavor.surbl-org-'.
                'permanent-test-point.com/justatest'
            )
        );
        $this->assertTrue($this->_surbl->isListed('http://127.0.0.2/'));
        $this->assertTrue($this->_surbl->isListed('http://127.0.0.2/justatest'));
    }

    /**
     * Tests if an URL that should not be spam is always correctly identified as 
     * such. 
     *
     * @return boolean true on success, false on failure
     */
    public function testNoSpamUrlsNeverGetReportedAsSpam()
    {
        $this->assertFalse($this->_surbl->isListed('http://www.nohn.net'));
        $this->assertFalse($this->_surbl->isListed('http://www.php.net/'));
        $this->assertFalse(
            $this->_surbl->isListed(
                'http://www.heise.de/'.
                '24234234?url=lala'
            )
        );
        $this->assertFalse($this->_surbl->isListed('http://www.nohn.net/blog/'));
        $this->assertFalse($this->_surbl->isListed('http://213.147.6.150/atest'));
        $this->assertFalse(
            $this->_surbl->isListed(
                'http://www.google.co.uk/search'.
                '?hl=en&q=test&btnG=Google+Search&meta='
            )
        );
    }

    /**
     * Tests if a set of spam and no-spam URLs is always correctly identified as 
     * such. 
     *
     * @return boolean true on success, false on failure
     */
    public function testMixedSpamAndNospamUrlsWorkAsExpected()
    {
        $this->assertFalse($this->_surbl->isListed('http://www.nohn.net'));
        $this->assertTrue(
            $this->_surbl->isListed(
                'http://surbl-org-permanent'.
                '-test-point.com'
            )
        );
        $this->assertTrue(
            $this->_surbl->isListed(
                'http://surbl-org-permanent'.
                '-test-point.com/justatest'
            )
        );
        $this->assertTrue($this->_surbl->isListed('http://127.0.0.2/justatest'));
        $this->assertFalse($this->_surbl->isListed('http://213.147.6.150/atest'));
        $this->assertFalse($this->_surbl->isListed('http://www.php.net'));
        $this->assertFalse($this->_surbl->isListed('http://www.google.com'));
        $this->assertFalse(
            $this->_surbl->isListed(
                'http://www.google.co.uk/search'.
                '?hl=en&q=test&btnG=Google+Search&meta='
            )
        );
    }

    /**
     * Tests if invalid arguments always return false.
     *
     * @return boolean true on success, false on failure
     */
    public function testInvalidArguments()
    {
        $this->assertFalse($this->_surbl->isListed('hurgahurga'));
        $this->assertFalse($this->_surbl->isListed(null));
        $this->assertFalse($this->_surbl->isListed(false));
        $this->assertFalse($this->_surbl->isListed(true));
    }

    /**
     * Test encoded URLs are looked up correctly
     *
     * @return boolean true on success, false on failure
     */
    public function testEncodedUrls()
    {
        $this->assertTrue(
            $this->_surbl->isListed(
                'http://%73urbl-org-permanent'.
                '-test-point.com/justatest'
            )
        );
    }
}
?>
