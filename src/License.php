<?php
/**
 * @author    : JIHAD SINNAOUR
 * @package   : FloatPHP
 * @subpackage: Helpers Component
 * @version   : 1.0.0
 * @category  : PHP framework
 * @copyright : (c) 2017 - 2021 JIHAD SINNAOUR <mail@jihadsinnaour.com>
 * @link      : https://www.floatphp.com
 * @license   : MIT License
 *
 * This file if a part of FloatPHP Framework
 */

namespace FloatPHP\Helpers;

use FloatPHP\Classes\Filesystem\Arrayify;
use FloatPHP\Classes\Filesystem\TypeCheck;
use FloatPHP\Classes\Filesystem\Stringify;

class License
{
    /**
     * @access protected
     * @var array $keys
     * @var array $ids
     * @var bool $useMcrypt
     * @var bool $useTime
     * @var bool $useServer
     * @var string $algorithm
     * @var int $startDif
     * @var string $begin1
     * @var string $end1
     * @var string $pad
     * @var string $begin2
     * @var string $end2
     * @var int $wrapto
     * @var array $data
     * @var string $serv
     * @var string $mac
     * @var bool $allowLocalhost
     * @var array $serverInfo
     * @var array $serverVars
     * @var array $ip
     * @var int $requiredUrls
     * @var string $dateFormat
     * @var int $allowedServerDifs
     * @var int $allowedIpDifs
     */

    protected $keys = [];
    protected $ids = [];
	protected $useMcrypt = false;
    protected $useTime = false;
    protected $useServer = false;
    protected $algorithm = 'blowfish';
    protected $startDif = 129600;

	protected $begin1 = 'BEGIN LICENSE KEY';
	protected $end1 = 'END LICENSE KEY';
	protected $pad = "-";
	protected $begin2 = '_DATA{';
	protected $end2 = '}DATA_';

	protected $wrapto = 80;
	protected $data = [];
    protected $serv;
    protected $mac;
    protected $allowLocalhost;
    protected $serverInfo = [];
    protected $serverVars = [];
    protected $ip = [];
    protected $requiredUrls = 2;
    protected $dateFormat = 'M/d/Y H:i:s';
    protected $allowedServerDifs = 0;
    protected $allowedIpDifs = 0;

    /**
     * @param bool $useMcryptis
     * @param bool $useTime
     * @param bool $useServer
     * @param bool $allowLocalhost
     */
    public function __construct($useMcrypt = true, $useTime = true, $useServer = true, $allowLocalhost = false)
    {
        $this->init($useMcrypt,$useTime,$useServer,$allowLocalhost);
        $this->setKeys();
        $this->setIds();
    }

    /**
     * Init license
     *
     * @access public
     * @param bool $useMcrypt
     * @param bool $useTime
     * @param bool $useServer
     * @param bool $allowLocalhost
     * @return void
     */
    public function init($useMcrypt = true, $useTime = true, $useServer = true, $allowLocalhost = false)
    {
        $this->useMcrypt = $useMcrypt;
        if ( !Mcrypter::exist() ) {
            $this->useMcrypt = false;
        }
        $this->useTime = $useTime;
        $this->allowLocalhost = $allowLocalhost;
        $this->useServer = $useServer;
        if ( $this->useServer ) {
            $this->mac = $this->getMacAddress();
        }
    }

    /**
     * Set static keys
     *
     * @access public
     * @param array $keys
     * @return void
     */
    public function setKeys($keys = [])
    {
        $this->keys = Arrayify::merge($this->getDefaultKeys(),$keys);
    }

    /**
     * Get default static keys
     *
     * @access protected
     * @param void
     * @return array
     */
    protected function getDefaultKeys() : array
    {
        $keys = [];

        $keys['key']  = 'YmUzYWM2sNGU24NbA363zA7IDSDFGDFGB5aVi35B';
        $keys['key'] .= 'DFGQ3YNO36ycDFGAATq4sYmSFVDFGDFGps7XDYEz';
        $keys['key'] .= 'GDDw96OnMW3kjCFJ7M+UV2kHe1WTTEcM09UMHHTo';

        $keys['request']  = '80dSbqylf4Cu5e5OYdAoAVkzpRDWAt7J1Vp27sYD';
        $keys['request'] .= 'U52ZBJprdRL1KE0il8KQXuKCK3sdA51P9w8U60wo';
        $keys['request'] .= 'hX2gdmBu7uVhjxbS8g4y874Ht8L12W54Q6T4R4ap';

        $keys['server']  = 'ant9pbc3OK28Li36Mi4d3fsWJ4tQSN4a9Z2qa8W6';
        $keys['server'] .= '6qR7ctFbljsOc9J4wa2Bh6j8KB3vbEXB18i6gfbE';
        $keys['server'] .= '0yHS0ZXQCceIlG7jwzDmN7YT06mVwcM9z0vy62Ty';

        return $keys;
    }

    /**
     * Set static ids
     *
     * @access public
     * @param array $ids
     * @return void
     */
    public function setIds($ids = [])
    {
        $this->ids = Arrayify::merge($this->getDefaultIds(),$ids);
    }

    /**
     * Get default static Ids
     *
     * @access protected
     * @param void
     * @return array
     */
    protected function getDefaultIds() : array
    {
        $ids = [
            'key'     => 'nSpkAHRiFfM2hE588eBi',
            'request' => 'NWCy0s0JpGubCVKlkkKi',
            'server'  => 'G95ZP2uS782cFey9x5Ai'
        ];
        return $ids;
    }

    /**
     * Protect against spoofing
     *
     * @access public
     * @param void
     * @return void
     */
    public function setServerVars()
    {
        $this->serverVars = Server::get();
        $this->ip = $this->getIpAddress();
        $this->serverInfo = $this->getServerVars();
    }

    /**
     * Validate license
     *
     * @access public
     * @param string $license
     * @return array
     */
    public function validate($license) : array
    {
        return $this->doValidate($license);
    }

    /**
     * Validate license through remote server
     *
     * @param string $license
     * @param string $host
     * @param string $path
     * @param string $port
     * @return array
     */
    public function validateRemote($license, $host, $path, $port = '80') : array
    {
        return $this->doValidate($license,true,$host,$path,$port);
    }

    /**
     * Set date format
     *
     * @access public
     * @param string $dateFormat
     * @return void
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Write key
     *
     * @access public
     * @param string $key
     * @param string $path
     * @return bool
     */
    public function writeKey($key, $path) : bool
    {
        // Open the key file for writeing and truncate
        $h = fopen($path,'w');
        // Write fails return error
        if ( fwrite($h,$key) === false ) {
            return false;
        }
        // Close file
        fclose($h);
        return true;
    }

    /**
     * Register install with the home server
     *
     * @access public
     * @param string $domain
     * @param number $start
     * @param number $expireIn
     * @param array $data
     * @param string $host
     * @param string $path
     * @param number $port
     * @return mixed
     */
    public function registerInstall($domain, $start, $expireIn, $data, $host, $path, $port = '80')
    {
	    /**
	     * @todo Check if key is alread generated
	     */
        if ( @filesize($this->licensePath) > 4 ) {
            return ['RESULT' => 'KEY-EXISTS'];
        }

        $data = ['DATA' => $data];

        // Check server matching
        if ( $this->useServer ) {
            // Evaluate the supplied domain against the collected ips
            if ( !$this->compareDomainIp($domain, $this->ip) ) {
                return ['RESULT' => 'DOMAIN-IP-FAIL'];
            }
            // Check server urls
            if ( count($this->serverInfo) < $this->requiredUrls ) {
                return ['RESULT' => 'SERVER-FAIL'];
            }
            $data['SERVER']['MAC'] = $this->mac;
            $data['SERVER']['PATH'] = $this->serverInfo;
            $data['SERVER']['IP'] = $this->ip;
            $data['SERVER']['DOMAIN'] = $domain;
        }

        // Time restrictions
        if ( $this->useTime ) {
            $current = time();
            $start = ($current < $start) ? $start : $current + $start;
            // Set dates
            $data['DATE']['START'] = $start;
            if ( $expireIn === 'NEVER' ) {
                $data['DATE']['SPAN'] = '~';
                $data['DATE']['END'] = 'NEVER';
            } else {
                $data['DATE']['SPAN'] = $expireIn;
                $data['DATE']['END'] = $start + $expireIn;
            }
        }
        // Include id for request
        $data['ID'] = md5($this->ids['request']);
        // Post the data home
        $data = $this->sendData($data,$host,$path,$port);
        // Return result and key if approved
        return (empty($data['RESULT'])) ? ['RESULT' => 'SOCKET-FAILED'] : $data;
    }

    /**
     * Generates server key when the license class resides on the server
     *
     * @access public
     * @param string $domain
     * @param int $start
     * @param int $expireIn
     * @param array $args
     * @return string
     */
    public function generate($domain = '', $start = 0, $expireIn = 31449600, $args = []) : string
    {
        if ( $this->serverInfo !== false || !$this->useServer ) {

            // Set id
            $data['ID'] = md5($this->ids['key']);

            // Set server bind
            if ( $this->useServer ) {
                // Validate domain IP
                if ( !$this->compareDomainIp($domain,$this->ip) ) {
                    return 'DOMAIN-IP-FAIL';
                }
                // Set domain
                $data['SERVER']['DOMAIN'] = $domain;
                // Set mac
                $data['SERVER']['MAC'] = $this->mac;
                // Set server info
                $data['SERVER']['PATH'] = $this->serverInfo;
                // Set ip
                $data['SERVER']['IP'] = $this->ip;
            }

            // Set time bind
            if ( $this->useTime && !TypeCheck::isArray($start) ) {
                $current = time();
                $start = ($current < $start) ? $start : $current + $start;
                // Set dates
                $data['DATE']['START'] = $start;
                $data['DATE']['SPAN']  = $expireIn;
                if ( $expireIn === 'NEVER' ) {
                    $data['DATE']['END'] = 'NEVER';
                } else {
                    $data['DATE']['END'] = $start + $expireIn;
                }
            }

            // Convert args
            if ( TypeCheck::isArray($start) ) {
                $args = $start;
            }

            // Set the server os
            $args['--PHP-OS'] = PHP_OS;
            // Set the server os
            $args['--PHP-VERSION'] = PHP_VERSION;
            // Merge data with args
            $data['DATA'] = $args;
            // Encrypt the key
            $key = $this->wrapLicense($data);
            // return the key
            return $key;
        }
        // Generation failed
        return 'SERVER-FAIL';
    }

    /**
     * Validate license key and return data
     *
     * @access protected
     * @param string $license
     * @param bool $dial
     * @param string $host
     * @param string $path
     * @param string $port
     * @return array
     */
    protected function doValidate($license, $dial = false, $host = '', $path = '', $port = '80') : array
    {
        if ( strlen($license) > 0 ) {

            // Decrypt data
            $data = $this->unwrapLicense($license);
            if ( TypeCheck::isArray($data) ) {

                if ( $data['ID'] != md5($this->ids['key']) ) {
                    $data['RESULT'] = 'CORRUPTED';
                }
                if ( $this->useTime ) {
                    // License used before official start
                    if ( $data['DATE']['START'] > (time() + $this->startDif) ) {
                        $data['RESULT'] = 'TIME-MINUS';
                    }
                    // License expired
                    if ( ($data['DATE']['END'] - time()) < 0 && $data['DATE']['SPAN'] != 'NEVER' ) {
                        $data['RESULT'] = 'EXPIRED';
                    }
                    $data['DATE']['HUMAN']['START'] = date($this->dateFormat,$data['DATE']['START']);
                    $data['DATE']['HUMAN']['END'] = date($this->dateFormat,$data['DATE']['END']);
                }
                if ( $this->useServer ) {
                    $mac = $data['SERVER']['MAC'] === $this->mac;
                    $path = count(Arrayify::diff($this->serverInfo,$data['SERVER']['PATH'])) <= $this->allowedServerDifs;
                    $domain = $this->compareDomainIp($data['SERVER']['DOMAIN'],$this->ip);
                    $ip = count(Arrayify::diff($this->ip,$data['SERVER']['IP'])) <= $this->allowedIpDifs;

                    // Check server data
                    if ( !$mac || !$path || !$domain || !$ip ) {
                        $data['RESULT'] = 'ILLEGAL';
                    }

                    // Check localhost
                    if ( $this->isLocalhost($data) && !$this->allowLocalhost ) {
                    	$data['RESULT'] = 'ILLEGAL-LOCAL';
                    }
                }
                // passed all current test so license is ok
                if ( !isset($data['RESULT']) ) {
                    // Dial home server if required
                    if ( $dial ) {
                        // Create details to send to home server
                        $send = [];
                        $send['LICENSE-DATA'] = $data;
                        $send['LICENSE-DATA']['KEY'] = md5($license);
                        // Dial home
                        $data['RESULT'] = $this->requestServer($send,$host,$path,$port);
                    } else {
                        // License is legal
                        $data['RESULT'] = 'OK';
                    }
                }
                return $data;

            } else {
                return ['RESULT' => 'INVALID'];
            }
        }
        return ['RESULT' => 'EMPTY'];
    }

    /**
     * Posts data to home server
     *
     * @access protected
     * @param array  $data
     * @param string $host
     * @param string $path
     * @param int $port
     * @return mixed
     */
    protected function sendData($data = [], $host = '', $path = '', $port = 80)
    {
        // Generate the post query info
        $query  = 'POSTDATA=' . $this->encrypt($data,'HOMEKEY');
        $query .= '&MCRYPT=' . $this->useMcrypt;
        // Init return string
        $return = '';

        // Generate post headers
        $post  = "POST $path HTTP/1.1\r\n";
        $post .= "Host: $host\r\n";
        $post .= "Content-type: application/x-www-form-urlencoded\r\n";
        $post .= "Content-length: " . strlen($query) . "\r\n";
        $post .= "Connection: close\r\n";
        $post .= "\r\n";
        $post .= $query;

        // Open a socket
        $header = @fsockopen($host,$port);
        if ( !$header ) {
            // Socket fails return failed
            return array('RESULT' => 'SOCKET-FAILED');
        }
        @fputs($header, $post);
        // Read returned data
        while (!@feof($header)) {
            $return .= @fgets($header,1024);
        }
        fclose($header);

        // Seperate output
        $leftpos = strpos($return,$this->begin2) + strlen($this->begin2);
        $rightpos = strpos($return,$this->end2) - $leftpos;

        // decrypt and return the data
        return $this->decrypt(substr($return,$leftpos,$rightpos),'HOMEKEY');
    }

    /**
     * Compare domain ip
     *
     * @access protected
     * @param type $domain
     * @param array $ips
     * @return bool
     */
    protected function compareDomainIp($domain, $ips = false) : bool
    {
        if ( !$ips ) {
            $ips = $this->getIpAddress();
        }
        // Get domain ip list
        $domainIps = gethostbynamel($domain);
        // loop through the collected ip's searching for matches against the domain ips
        if ( TypeCheck::isArray($domainIps) && count($domainIps) > 0 ) {
            foreach ($domainIps as $ip) {
                if ( in_array($ip,$ips) ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Pad out begin and end seperators
     *
     * @access protected
     * @param string $str
     * @return string
     */
    protected function pad($str) : string
    {
        $strLen = strlen($str);
        $spaces = ($this->wrapto-$strLen)/2;
        $str1 = '';
        for ($i = 0; $i < $spaces; $i++) {
            $str1 = $str1 . $this->pad;
        }
        if ( $spaces/2 != round($spaces/2) ) {
            $str = substr($str1,0,strlen($str1)-1) . $str;
        } else {
            $str = $str1 . $str;
        }
        $str = $str . $str1;
        return $str;
    }

    /**
     * Get hash key for current encryption
     *
     * @access protected
     * @param string $type
     * @return string
     */
    protected function getKey($type = 'KEY') : string
    {
        switch ($type) {
            case 'KEY':
                return $this->keys['key'];
                break;

            case 'REQUESTKEY':
                return $this->keys['request'];
                break;

            case 'HOMEKEY':
                return $this->keys['server'];
                break;

            default:
            	return '';
        }
    }

    /**
     * Get begin seperator
     *
     * @access protected
     * @param string $type
     * @return string
     */
    protected function getBegin($type = 'KEY') : string
    {
        switch ($type) {
            case 'KEY':
                return $this->begin1;
                break;

            case 'REQUESTKEY':
                return $this->begin2;
                break;

            case 'HOMEKEY':
                return '';
                break;

            default:
            	return '';
        }
    }

    /**
     * Get end seperator
     *
     * @access protected
     * @param string $type
     * @return string
     */
    protected function getEnd($type = 'KEY') : string
    {
        switch ($type) {
            case 'KEY':
                return $this->end1;
                break;

            case 'REQUESTKEY':
                return $this->end2;
                break;

            case 'HOMEKEY':
                return '';
                break;

            default:
            	return '';
        }
    }

    /**
     * Get random string
     *
     * @access protected
     * @param number $length
     * @param string $seeds
     * @return string
     */
    protected function getRandomString($length = 10, $seeds = '') : string
    {
    	if ( empty($seeds) ) {
    		$seeds  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    		$seeds .= 'abcdefghijklmnopqrstuvwxyz';
    		$seeds .= '01234567890123456789';
    	}
        $str = '';
        $count = strlen($seeds);
        list($usec,$sec) = explode(' ',microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);
        for ($i = 0; $length > $i; $i++) {
            $str .= $seeds[mt_rand(0,$count - 1)];
        }
        return $str;
    }

    /**
     * Encrypt key
     *
     * @access protected
     * @param array $srcArray
     * @param string $type
     * @return string
     */
    protected function encrypt($srcArray, $type = 'KEY') : string
    {
        // Get random
        $random = $this->getRandomString(3);
        // Get key
        $key = $this->getKey($type);
        $key = "{$random}{$key}";
        $crypt = '';

        // Mycrypt
        if ( $this->useMcrypt ) {
            // Open mcrypt
            $td = mcrypt_module_open($this->algorithm,'','ecb','');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
            // Process key
            $key = substr($key,0,mcrypt_enc_getKey_size($td));
            // Init mcrypt
            mcrypt_generic_init($td,$key,$iv);
            // encrypt data
            $crypt = mcrypt_generic($td,Stringify::serialize($srcArray));
            // Shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        } else {
            // Regular encryption method
            $str = Stringify::serialize($srcArray);
            // loop through the str and encrypt it
            for ($i = 1; $i <= strlen($str); $i++) {
                $char = substr($str,$i-1,1);
                $keyChar = substr($key,($i % strlen($key))-1, 1);
                $char = chr(ord($char)+ord($keyChar));
                $crypt .= $char;
            }
        }
        // Return key
        $crypt = base64_encode(base64_encode(trim($crypt)));
        return $random . $crypt;
    }

    /**
     * Decrypt key
     *
     * @access public
     * @param string $str
     * @param string $keyType
     * @return array
     */
    protected function decrypt($str, $keyType = 'KEY')
    {
        $random = substr($str, 0, 3);
        $str = base64_decode(base64_decode(substr($str, 3)));
        // get the key
        $key = $random.$this->getKey($keyType);

        // check to see if mycrypt exists
        if ( $this->useMcrypt ) {
            // openup mcrypt
            $td = mcrypt_module_open($this->algorithm,'','ecb','');
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            // process the key
            $key = substr($key, 0, mcrypt_enc_getKey_size($td));
            // init mcrypt
            mcrypt_generic_init($td, $key, $iv);

            // decrypt the data and return
            $decrypt = @mdecrypt_generic($td, $str);

            // shutdown mcrypt
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        } else {
            // if mcrypt doesn't exist use regular decryption method
            // init the decrypt vars
            $decrypt = '';

            // loop through the text and decode the string
            for ($i = 1; $i <= strlen($str); $i++) {
                $char     = substr($str, $i-1, 1);
                $keychar  = substr($key, ($i % strlen($key))-1, 1);
                $char     = chr(ord($char)-ord($keychar));
                $decrypt  .= $char;
            }
        }
        // return the key
        return Stringify::unserialize($decrypt);
    }

    /**
     * Wrap license key
     *
     * @access protected
     * @param array $data
     * @param string $type
     * @return string
     */
    protected function wrapLicense($data, $type = 'KEY') : string
    {
        // Sort variables
        $begin = $this->pad($this->getBegin($type));
        $end = $this->pad($this->getEnd($type));
        // Encrypt data
        $str = $this->encrypt($data,$type);
        // Wrap data
        return $begin . PHP_EOL . wordwrap($str,$this->wrapto,PHP_EOL,1) . PHP_EOL . $end;
    }

    /**
     * Unwrap license
     *
     * @access public
     * @param string $license
     * @param string $type
     * @return array
     */
    protected function unwrapLicense($license, $type = 'KEY') : array
    {
        // Sort variables
        $begin = $this->pad($this->getBegin($type));
        $end = $this->pad($this->getEnd($type));
        // Format license
        $license = trim(Stringify::replace([$begin,$end,"\r","\n","\t"],'',$license));
        // Decrypt license
        return $this->decrypt($license,$type);
    }

    /**
     * getOsVar
     *
     * gets various vars depending on the os type
     *
     * @access public
     * @param type $varName The var name
     * @param type $os      The os name
     *
     * @return string various values
     **/
    protected function getOsVar($varName, $os)
    {
        $varName = strtolower($varName);
        // switch between the os's
        switch ($os) {
            // not sure if the string is correct for FreeBSD
            // not tested
            case 'freebsd':
            // not sure if the string is correct for NetBSD
            // not tested
            case 'netbsd':
            // not sure if the string is correct for Solaris
            // not tested
            case 'solaris':
            // not sure if the string is correct for SunOS
            // not tested
            case 'sunos':
            // darwin is mac os x
            // tested only on the client os
            case 'darwin':
                // switch the var name
                switch ($varName) {
                    case 'conf':
                        $var = '/sbin/ifconfig';
                        break;
                    case 'mac':
                        $var = 'ether';
                        break;
                    case 'ip':
                        $var = 'inet ';
                        break;
                }
                break;
            // linux variation
            // tested on server
            case 'linux':
                // switch the var name
                switch ($varName) {
                    case 'conf':
                        $var = '/sbin/ifconfig';
                        break;
                    case 'mac':
                        $var = 'HWaddr';
                        break;
                    case 'ip':
                        $var = 'inet addr:';
                        break;
                }
                break;
        }

        return $var;
    }

    /**
     * Get config
     *
     * gets the server config file and returns it. tested on Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @access protected
     * @return string config file data
     **/
    protected function getServerConfig()
    {
        if ( ini_get('safe_mode') ) {
            return 'SAFE-MODE';
        }
        // if anyone has any clues for windows environments
        // or other server types let me know
        $os = strtolower(PHP_OS);
        if (substr($os, 0, 3) === 'win') {
            // this windows version works on xp running apache
            // based server. it has not been tested with anything
            // else, however it should work with NT, and 2000 also

            // execute the ipconfig
            @exec('ipconfig/all', $lines);
            // count number of lines, if none returned return MAC_404
            // thanks go to Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>
            if (count($lines) === 0) {
                return 'ERROR-OPEN';
            }
            // $path the lines together
            $conf = implode(PHP_EOL, $lines);
        } else {
            // get the conf file name
            $osFile = $this->getOsVar('conf', $os);
            // open the ipconfig
            $fp = @popen($osFile, "rb");
            // returns invalid, cannot open ifconfig
            if (!$fp) {
                return 'ERROR-OPEN';
            }
            // read the config
            $conf = @fread($fp, 4096);
            @pclose($fp);
        }

        return $conf;
    }

    /**
     * Get ip address
     *
     * @access protected
     * @return mixed
     **/
    protected function getIpAddress()
    {
        $ips = [];
        // get the cofig file
        $conf = $this->getServerConfig();
        // if the conf has returned and error return it
        if ($conf != 'SAFE-MODE' && $conf != 'ERROR-OPEN') {
            // if anyone has any clues for windows environments
            // or other server types let me know
            $os = strtolower(PHP_OS);
            if (substr($os, 0, 3) !== 'win') {
                // explode the conf into seperate lines for searching
                $lines = explode(PHP_EOL, $conf);
                // get the ip delim
                $ipDelim = $this->getOsVar('ip', $os);

                // ip pregmatch
                $num = "(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])";
                // seperate the lines
                foreach ($lines as $key => $line) {
                    // check for the ip signature in the line
                    if (!preg_match("/^$num\\.$num\\.$num\\.$num$/", $line) && strpos($line, $ipDelim)) {
                        // seperate out the ip
                        $ip = substr($line, strpos($line, $ipDelim)+strlen($ipDelim));
                        $ip = trim(substr($ip, 0, strpos($ip, " ")));
                        // add the ip to the collection
                        if ( !isset($ips[$ip]) ) {
                            $ips[$ip] = $ip;
                        }
                    }
                }
            }
        }

        if ( isset($this->serverVars['SERVER_NAME']) ) {
            $ip = gethostbyname($this->serverVars['SERVER_NAME']);
            if ( !isset($ips[$ip]) ) {
                $ips[$ip] = $ip;
            }
        }
        if ( isset($this->serverVars['SERVER_ADDR']) ) {
            $name = gethostbyaddr($this->serverVars['SERVER_ADDR']);
            $ip = gethostbyname($name);
            if ( !isset($ips[$ip]) ) {
                $ips[$ip] = $ip;
            }
            if ( isset($addr) && $addr != $this->serverVars['SERVER_ADDR'] ) {
                if ( !isset($ips[$this->serverVars['SERVER_ADDR']]) ) {
                    $ips[$this->serverVars['SERVER_ADDR']] = $this->serverVars['SERVER_ADDR'];
                }
            }
        }

        if ( count($ips) > 0 ) {
            return $ips;
        }
        // failed to find an ip check for conf error or return 404
        if ($conf === 'SAFE-MODE' || $conf === 'ERROR-OPEN') {
            return $conf;
        }

        return 'IP-404';
    }

    /**
    * Get mac address
    *
    * @access private
    * @return string Mac address if found
    * @return string ERROR-OPEN means config can't be found and thus not opened
    * @return string MAC_404 means mac adress doesn't exist in the config file
    * @return string SAFE-MODE means server is in safe mode so config can't be read
    **/
    protected function getMacAddress()
    {
        // open the config file
        $conf = $this->getServerConfig();

        // if anyone has any clues for windows environments
        // or other server types let me know
        $os = strtolower(PHP_OS);
        if (substr($os, 0, 3) === 'win') {
            // explode the conf into lines to search for the mac
            $lines = explode(PHP_EOL, $conf);
            // seperate the lines for analysis
            foreach ($lines as $key => $line) {
                // check for the mac signature in the line
                // originally the check was checking for the existence of string 'physical address'
                // however Gert-Rainer Bitterlich pointed out this was for english language
                // based servers only. preg_match updated by Gert-Rainer Bitterlich. Thanks
                if (preg_match("/([0-9a-f][0-9a-f][-:]){5}([0-9a-f][0-9a-f])/i", $line)) {
                    $trimmedLine = trim($line);
                    // take of the mac addres and return
                    return trim(substr($trimmedLine, strrpos($trimmedLine, " ")));
                }
            }
        } else {
            // get the mac delim
            $macDelim = $this->getOsVar('mac', $os);

            // get the pos of the os_var to look for
            $pos = strpos($conf, $macDelim);
            if ($pos) {
                // seperate out the mac address
                $str1 = trim(substr($conf, ($pos+strlen($macDelim))));

                return trim(substr($str1, 0, strpos($str1, "\n")));
            }
        }
        // failed to find the mac address
        return 'MAC_404';
    }

    /**
     * Get server vars
     *
     * @access public
     * @param void
     * @return mixed
     */
    protected function getServerVars()
    {
        $vars = [];
        if ( empty($this->serverVars) ) {
            $this->setServerVars();
        }

        if ( isset($this->serverVars['SERVER_ADDR']) ) {
            if ( !strrpos($this->serverVars['SERVER_ADDR'],'127.0.0.1') || $this->allowLocalhost ) {
                $vars['SERVER_ADDR'] = $this->serverVars['SERVER_ADDR'];
            }
        }

        if ( isset($this->serverVars['HTTP_HOST']) ) {
            if ( !strrpos($this->serverVars['HTTP_HOST'], '127.0.0.1') || $this->allowLocalhost ) {
               $vars['HTTP_HOST'] = $this->serverVars['HTTP_HOST'];
            }
        }

        if ( isset($this->serverVars['SERVER_NAME']) ) {
            $vars['SERVER_NAME'] = $this->serverVars['SERVER_NAME'];
        }

        if ( isset($this->serverVars['PATH_TRANSLATED']) ) {
            $vars['PATH_TRANSLATED'] = substr($this->serverVars['PATH_TRANSLATED'],0,strrpos($this->serverVars['PATH_TRANSLATED'],'/'));

        } elseif ( isset($this->serverVars['SCRIPT_FILENAME']) ) {
            $vars['SCRIPT_FILENAME'] = substr($this->serverVars['SCRIPT_FILENAME'],0,strrpos($this->serverVars['SCRIPT_FILENAME'],'/'));
        }

        if ( isset($this->serverVars['SCRIPT_URI']) ) {
            $vars['SCRIPT_URI'] = substr($this->serverVars['SCRIPT_URI'],0,strrpos($this->serverVars['SCRIPT_URI'],'/'));
        }

        if ( count($vars) < $this->requiredUrls ) {
            return 'SERVER-FAILED';
        }

        return $vars;
    }

    /**
     * Request host server
     *
     * @access public
     * @param array $data
     * @param string $host
     * @param string $path
     * @param int $port
     * @return string
     */
    protected function requestServer($data, $host, $path, $port) : string
    {
        // Post data to host server
        $data = $this->sendData($data,$host,$path,$port);
        return !empty($data['RESULT']) ? $data['RESULT'] : 'SOCKET-FAILED';
    }

    /**
     * Check local server
     *
     * @access private
     * @param array $data
     * @return bool
     */
    private function isLocalhost($data = []) : bool
    {
		if ( isset($data['SERVER']['IP']) ) {
			if ( Stringify::contains(['127.0.0.1','::1'],$data['SERVER']['IP']) ) {
				return true;
			}

		} elseif ( isset($data['PATH']['SERVER_ADDR'] )) {
			if ( $data['PATH']['SERVER_ADDR'] === '127.0.0.1' || $data['PATH']['SERVER_ADDR'] === '::1' ) {
				return true;
			}

		} elseif ( isset($data['PATH']['HTTP_HOST'] )) {
			if ( $data['PATH']['HTTP_HOST'] === '127.0.0.1' || $data['PATH']['HTTP_HOST'] === '::1' ) {
				return true;
			}
		}
		return false;
    }
}
