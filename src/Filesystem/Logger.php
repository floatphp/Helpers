<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.1.0
 * @copyright  : (c) 2018 - 2024 Jihad Sinnaour <mail@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

declare(strict_types=1);

namespace FloatPHP\Helpers\Filesystem;

use FloatPHP\Kernel\TraitConfiguration;
use FloatPHP\Classes\Filesystem\Logger as MainLogger;
use FloatPHP\Helpers\Framework\inc\{
	TraitFormattable,
	TraitIO
};

/**
 * Built-in Logger factory class.
 */
final class Logger extends MainLogger
{
    use TraitConfiguration;
    use TraitFormattable;
    use TraitIO;

    /**
     * @access private
     * @var mixed $parserIgnore, array|string
     * @var string $parserRegex
     * @var string $levelRegex
     */
    private $parserIgnore = ["\n\r", "\n", "\r"];
    private $parserRegex = '/\[[0-9]{1,4}-[0-9]{1,4}-[0-9]{1,4}\s[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}\]/';
    private $levelRegex = '/:\s\[([a-z]{1,})]\s-/';

    /**
     * @inheritdoc
     * @uses initConfig()
     * @uses resetConfig()
     */
    public function __construct(?string $path = null, string $file = 'debug', string $ext = 'log')
    {
        // Init configuration
        $this->initConfig();

        // Override
        $path = "{$this->getLoggerPath()}/{$path}";
        $path = $this->formatPath($path, true);
        parent::__construct($path, $file, $ext);

        // Reset configuration
        $this->resetConfig();
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
     * @return array
     * @uses {[date]} : {level} - {content}.
     */
    public function parse() : array
    {
        $wrapper = [];
        foreach ($this->scanDir($this->path) as $file) {
            if ( ($content = $this->readFile("{$this->path}/{$file}")) ) {
                $strings = $this->splitString($content, [
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
                    $temp = $this->replaceString($this->parserIgnore, '', $temp);
                    if ( ($level = $this->matchString($this->levelRegex, $temp, -1)) ) {
                        $temp = $this->replaceString($level[0], '', $temp);
                        $level = $level[1];
                    }
                    $date = substr($content, $start, $offset);
                    if ( ($date = $this->matchString($this->parserRegex, $date)) ) {
                        $date = $this->replaceString(['[', ']'], '', $date);
                    }

                    $level = !$this->isType('array', $level) ? trim($level) : 'unknown';
                    $date = !$this->isType('bool', $date) ? trim($date) : 'undefined';
                    $temp = trim($temp);

                    $wrapper[] = [
                        'id'      => $key + 1,
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
