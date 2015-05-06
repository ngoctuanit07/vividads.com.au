<?php
/**
 * Class Aptoplex_EasyUploader_Helper_Utility
 *
 * @author Aptoplex
 * @copyright 2015 Aptoplex
 */
class Aptoplex_EasyUploader_Helper_Utility extends Mage_Core_Helper_Abstract {

    static public function postInstallSetup() {

        /**
         * Refresh the compiler then return it to its original state
         *
         * DISABLED - can occasionally cause the server to timeout on some installations
         * (General error: 2006 MySQL server has gone away...)
         */
        //$compiler = Mage::getSingleton('compiler/process');
        //$compilerEnabled = defined('COMPILER_INCLUDE_PATH');
        //Mage::getSingleton('compiler/process')->run();
        //if (!$compilerEnabled) {
        //    $compiler->registerIncludePath(false);
        //}
    }

    /**
     * Gets the calling method/function name
     *
     * @param bool $completeTrace
     * @param bool $ignoreThisMethod
     * @return string
     */
    public function getCallingFunctionName($completeTrace = true, $ignoreThisMethod = true) {

        $trace = debug_backtrace();

        if ($completeTrace) {
            $str = '';
            foreach($trace as $caller) {
                $str .= "\n-- {$caller['function']}() --";
                if (isset($caller['class'])) {
                    $str .= " called from Class: -- {$caller['class']}";
                }

                if ($ignoreThisMethod && $caller['function'] == 'getCallingFunctionName') {
                    $str = '';
                }
            }
        }
        else {
            $caller = $trace[2]; // TODO: look into this...
            $str = "\n-- {$caller['function']}() --";
            if (isset($caller['class'])) {
                $str .= " called from Class: -- {$caller['class']}";
            }

            if ($ignoreThisMethod && $caller['function'] == 'getCallingFunctionName') {
                $str = '';
            }
        }

        return $str;
    }

    /**
     * Returns a random hexadecimal string
     *
     * @param int $length
     * @param bool $upperCase
     * @return string
     */
    public function getRandomHexString($length = 8, $upperCase = false) {
        $md5_hash_length = 32;
        $iterations = abs(ceil($length / $md5_hash_length));
        $string = '';
        for ($i = 0; $i < $iterations; ++$i) {
            $string .= md5(microtime());
        }
        $string = substr($string, 0, max(array(1, $length)));
        if ($upperCase == true) $string = strtoupper($string);

        return $string;
    }
}