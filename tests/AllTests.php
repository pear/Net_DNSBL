<?php
ini_set('display_errors', 'On');

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'testNetDNSBL.php';
require_once 'testNetDNSBLSURBL.php';

class AllTests {
    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Net_DNSBL TestSuite');
        $suite->addTestSuite('testNetDNSBL');
        $suite->addTestSuite('testNetDNSBLSURBL');
        return $suite;
    }
}
?>
