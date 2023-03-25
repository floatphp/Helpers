<?php
/**
 * @author     : JIHAD SINNAOUR
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.0.2
 * @category   : PHP framework
 * @copyright  : (c) 2017 - 2023 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://www.floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Kernel\TraitConfiguration;
use FloatPHP\Classes\Filesystem\{
    File, TypeCheck, Stringify, Logger as ParentLogger
};

/**
 * Logger helper class,
 * @see {[date]} : {level} - {content}.
 */
final class Logger extends ParentLogger
{
    use TraitConfiguration;

    /**
     * @access private
     * @var mixed $parserIgnore, array|string
     * @var string $parserRegex
     * @var string $levelRegex
     */
    private $parserIgnore = ["\n\r","\n","\r"];
    private $parserRegex = '/\[[0-9]{1,4}-[0-9]{1,4}-[0-9]{1,4}\s[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}\]/';
    private $levelRegex = '/:\s\[([a-z]{1,})]\s-/';

    /**
     * @param string $path
     * @param string $filename
     * @param string $extension
     */
    public function __construct($path = '', $filename = 'debug', $extension = 'log')
    {
        // Init configuration
        $this->initConfig();

        // Override
        $path = "{$this->getLoggerPath()}/{$path}";
        $this->setPath(Stringify::formatPath($path,true));
        $this->setFilename($filename);
        $this->setExtension($extension);

        // Reset configuration
        $this->resetConfig();
    }

    /**
     * Get logger path.
     * 
     * @access public
     * @param void
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set parser ignore strings.
     * 
     * @access public
     * @param mixed $strings
     * @return void
     */
    public function setParserIgnore($strings)
    {
        $this->parserIgnore = $strings;
    }

    /**
     * Set parser regex (date).
     * 
     * @access public
     * @param string $regex
     * @return void
     */
    public function setParserRegex(string $regex)
    {
        $this->parserRegex = $regex;
    }

    /**
     * Set level regex.
     * 
     * @access public
     * @param string $regex
     * @return void
     */
    public function setLevelRegex(string $regex)
    {
        $this->levelRegex = $regex;
    }

    /**
     * Parse logs.
     * 
     * @access public
     * @param void
     * @return array
     */
    public function parse() : array
    {
        $wrapper = [];
        foreach (File::scanDir($this->path) as $file) {
            if ( ($content = File::r("{$this->path}/{$file}")) ) {
                $strings = Stringify::split($content, [
                    'regex' => $this->parserRegex,
                    'flags' => 1|4
                ]);
                $start = 0;
                foreach ($strings as $key => $string) {
                    $temp = $string[0] ?? '';
                    $offset = $string[1] ?? 0;
                    if ( $key > 0 ){
                        $start = $strings[$key-1][1] ?? 0;
                    }
                    $temp = Stringify::replace($this->parserIgnore, '', $temp);
                    if ( ($level = Stringify::match($this->levelRegex, $temp, -1)) ) {
                        $temp = Stringify::replace($level[0], '', $temp);
                        $level = $level[1];
                    }
                    $date = substr($content, $start, $offset);
                    if ( ($date = Stringify::match($this->parserRegex, $date)) ) {
                        $date = Stringify::replace(['[', ']'], '', $date);
                    }

                    $level = !TypeCheck::isArray($level) ? trim($level) : 'unknown';
                    $date = !TypeCheck::isBool($date) ? trim($date) : 'undefined';
                    $temp = trim($temp);

                    $wrapper[] = [
                        'id'      => $key+1,
                        'file'    => $file,
                        'level'   => $level,
                        'date'    => $date,
                        'content' => $temp
                    ];
                }
            }
        }
        return $wrapper;
    }
}
