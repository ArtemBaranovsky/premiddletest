<?php

namespace PHPUnit\Framework\TestCase;

use Classes\Cache;
use PHPUnit\Framework\TestCase;

/**
 * Class CacheTest
 * @package PHPUnit\Framework\TestCase
 */
class CacheTest extends TestCase
{
    /**
     * @param $key
     * @param $value
     * @dataProvider putAndGetProvider
     */
    public function testPutAndGet($key, $value)
    {
        Cache::put($key, $value, 1440);
        $cached = Cache::get($key);
        $this->assertSame($value, $cached);
    }

    public function putAndGetProvider()
    {
        return [
            [
                'key' => 'user',
                'value' => [
                    'name' => 'Linus Torvalds',
                    'rules' => 'superuser',
                ]
            ],
        ];
    }

    public function testPutAndGetScalar()
    {
        Cache::put('year', 2021, 1440);
        $cached = Cache::get('year');
        $this->assertSame(2021, $cached);

        Cache::put('gender', 'male', 1440);
        $cached = Cache::get('gender');
        $this->assertSame('male', $cached);
    }

}
