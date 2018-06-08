<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use MonkeryTestCase\BrainMonkeyWpTestCase;

class ConfigCompositionTest extends BrainMonkeyWpTestCase
{

    /**
     * @dataProvider getData
     */
    public function testGet(array $components, string $key, $expected)
    {
        $components = $this->mockComponents($components, 'get');

        self::assertSame(
            $expected,
            (new ConfigComposition($components))->get($key)
        );
    }

    /**
     * @see testGet
     */
    public function getData(): array
    {
        return [
            'expect int' => [
                'components' => [
                    'some.config.key' => 42,
                ],
                'key' => 'some.config.key',
                'expected' => 42,
            ],
            'expect bool' => [
                'components' => [
                    'some.config.key' => false,
                ],
                'key' => 'some.config.key',
                'expected' => false,
            ],
            'expect null' => [
                'components' => [
                    'some.config.key' => null,
                ],
                'key' => 'some.config.key',
                'expected' => null,
            ],
        ];
    }

    /**
     * @dataProvider hasData
     */
    public function testHas(array $components, string $key, bool $expected)
    {
        $components = $this->mockComponents($components, 'has');

        self::assertSame(
            $expected,
            (new ConfigComposition($components))->has($key)
        );
    }

    /**
     * @see testHas
     */
    public function hasData(): array
    {
        return [
            'expect true' => [
                'components' => [
                    'some.config.key' => true,
                ],
                'key' => 'some.config.key',
                'expected' => true,
            ],
            'expect false' => [
                'components' => [
                    'some.config.key' => true,
                ],
                'key' => 'does.not.exist',
                'expected' => false,
            ],
            'expect pass through false' => [
                'components' => [
                    'some.config.key' => false,
                ],
                'key' => 'some.config.key',
                'expected' => false,
            ],
        ];
    }
    private function mockComponents(array $components, string $method): array
    {
        array_walk(
            $components,
            function (&$value, $key) use ($method) {
                $mock = \Mockery::mock(Config::class);
                $mock->shouldReceive($method)
                    ->with($key)
                    ->andReturn($value);

                $value = $mock;
            }
        );

        return $components;
    }
}
