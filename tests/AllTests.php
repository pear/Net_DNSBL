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

ini_set('display_errors', 'On');

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'testNetDNSBL.php';
require_once 'testNetDNSBLSURBL.php';

/**
 * AllTests
 *
 * This class integrates all unit tests.
 *
 * @category Net
 * @package  Net_DNSBL
 * @author   Sebastian Nohn <sebastian@nohn.net>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/net_dnsbl Package Home
 */

class AllTests
{
    
    /**
     * Execute tests
     *
     * @access public
     * @return boolean true on success, false on failure
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Set up testsuite
     *
     * @access public
     * @return boolean true on success, false on failure
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Net_DNSBL TestSuite');
        $suite->addTestSuite('TestNetDNSBL');
        $suite->addTestSuite('TestNetDNSBLSURBL');
        return $suite;
    }
}
?>
