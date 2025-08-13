<?php
/**
 * @author     : Jakiboy
 * @package    : FloatPHP
 * @subpackage : Helpers Filesystem Component
 * @version    : 1.5.x
 * @copyright  : (c) 2018 - 2025 Jihad Sinnaour <me@jihadsinnaour.com>
 * @link       : https://floatphp.com
 * @license    : MIT
 *
 * This file if a part of FloatPHP Framework.
 */

use PHPUnit\Framework\TestCase;
use FloatPHP\Classes\Filesystem\{TypeCheck, File};
use FloatPHP\Helpers\Filesystem\Cache;
use FloatPHP\Helpers\Framework\Configuration;

final class CacheTest extends TestCase
{
    public function testInstance()
    {
        $cache = Cache::instance();
        $object = 'FloatPHP\Helpers\Filesystem\cache\FileCache';
        $this->assertTrue(
            TypeCheck::isObject($cache, $object)
        );

        if ( TypeCheck::isClass('Redis') ) {
            $cache = Cache::instance('Redis');
            $object = 'FloatPHP\Helpers\Filesystem\cache\RedisCache';
            $this->assertTrue(
                TypeCheck::isObject($cache, $object)
            );
        }

        $cache = Cache::instance('File');
        $object = 'FloatPHP\Helpers\Filesystem\cache\FileCache';
        $this->assertTrue(
            TypeCheck::isObject($cache, $object)
        );
    }

    public function testI()
    {
        $cache = new Cache();
        $object = 'FloatPHP\Helpers\Filesystem\cache\FileCache';
        $this->assertTrue(
            TypeCheck::isObject($cache, $object)
        );

        if ( TypeCheck::isClass('Redis') ) {
            $cache = Cache::i('Redis');
            $object = 'FloatPHP\Helpers\Filesystem\cache\RedisCache';
            $this->assertTrue(
                TypeCheck::isObject($cache, $object)
            );
        }

        $cache = Cache::i('File');
        $object = 'FloatPHP\Helpers\Filesystem\cache\FileCache';
        $this->assertTrue(
            TypeCheck::isObject($cache, $object)
        );
    }

    public function testGetInstance()
    {
        if ( TypeCheck::isClass('Redis') ) {
            Cache::i('Redis');
            $object = 'FloatPHP\Helpers\Filesystem\cache\RedisCache';
            $this->assertTrue(
                TypeCheck::isObject(Cache::getInstance(), $object)
            );
        }
        new Cache();
        $object = 'FloatPHP\Helpers\Filesystem\cache\FileCache';
        $this->assertTrue(
            TypeCheck::isObject(Cache::getInstance(), $object)
        );
    }

    public function testGet()
    {
        $cache = new Cache();
        $this->assertTrue(TypeCheck::isNull($cache->get('')));

        $cache->get('test');
        $cache->set(123, 'tag-1');
        $this->assertSame($cache->get('test'), 123);
    }

    public function testIsCached()
    {
        $cache = new Cache();
        $cache->get('test-2', $status);
        $this->assertFalse($status);
        $cache->set(123, 'tag-2');
        $this->assertTrue($status);
    }

    public function testSet()
    {
        $cache = new Cache();
        $cache->get('test-3');
        $this->assertTrue($cache->set(123));
    }

    public function testDelete()
    {
        $cache = new Cache();
        $this->assertTrue($cache->delete('test-3'));
        $this->assertFalse($cache->delete('test-4'));
    }

    public function testDeleteByTag()
    {
        $cache = new Cache();
        $this->assertTrue($cache->deleteByTag('tag-1'));
        $this->assertTrue($cache->deleteByTag(['tag-2'])); // Array
        $this->assertFalse($cache->deleteByTag('tag-3'));
    }

    public function testGetCache()
    {
        if ( TypeCheck::isClass('Redis') ) {
            $cache = Cache::i('Redis');
            $cache->get('test');
            $object = 'Phpfastcache\Drivers\Redis\Item';
            $this->assertTrue(
                TypeCheck::isObject($cache->getCache(), $object)
            );
        }

        $cache = new Cache();
        $cache->get('test');
        $object = 'Phpfastcache\Drivers\Files\Item';
        $this->assertTrue(
            TypeCheck::isObject($cache->getCache(), $object)
        );
    }

    public function testGetAdapter()
    {
        if ( TypeCheck::isClass('Redis') ) {
            $cache = Cache::i('Redis');
            $cache->get('test');
            $object = 'Phpfastcache\Drivers\Redis\Driver';
            $this->assertTrue(
                TypeCheck::isObject($cache->getAdapter(), $object)
            );
        }

        $cache = new Cache();
        $cache->get('test');
        $object = 'Phpfastcache\Drivers\Files\Driver';
        $this->assertTrue(
            TypeCheck::isObject($cache->getAdapter(), $object)
        );
    }

    public function testTTL()
    {
        $cache = new Cache();

        $cache->get('test');
        $cache->set(123, 'tag', 30);
        $this->assertSame($cache->getCache()->getTtl(), 30);

        $cache->delete('test');
        $cache->get('test');
        $cache->set(123, 'tag');
        $this->assertTrue(($cache->getCache()->getTtl() > 30));
    }

    public function testPurge()
    {
        $cache = new Cache();
        $this->assertTrue($cache->purge());
    }

    public function testPurgeView()
    {
        $config = new Configuration();
        $path = $config->getRoot($config->reflect()->path->cache);
        File::w("{$path}/view/cache.txt", 'test');
        $this->assertTrue(Cache::purgeView());
    }

    public function testPurgePath()
    {
        $config = new Configuration();
        $path = $config->getRoot($config->reflect()->path->cache);
        File::w("{$path}/cache.txt", 'test');
        $this->assertTrue(Cache::purgePath());
    }
}
