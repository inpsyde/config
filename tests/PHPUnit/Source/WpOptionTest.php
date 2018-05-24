<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Brain\Monkey\Functions;
use Inpsyde\Config\Exception\MissingConfig;
use Inpsyde\Config\Filter;
use Inpsyde\Config\Schema;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class WpOptionTest extends BrainMonkeyWpTestCase
{

    /**
     * @dataProvider hasData
     */
    public function testHas(array $data, array $mocks, bool $expected)
    {
        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);

        $schema->shouldReceive('getDefinition')
            ->with($data['key'])
            ->andReturn($data['schema']);

        $filter->shouldReceive('validateValue')
            ->with($data['value'], $data['schema'])
            ->andReturn($mocks['filterValidates']);

        $optionLoader = function ($optionName, $default) use ($data) {
            self::assertSame(
                $optionName,
                $data['schema']['source_name']
            );
            if (array_key_exists('default_value', $data['schema'])) {
                self::assertSame(
                    $data['schema']['default_value'],
                    $default
                );
            } else {
                self::assertFalse($default);
            }

            return $data['value'];
        };

        $testee = new WpOption($optionLoader, $schema, $filter);
        self::assertSame(
            $expected,
            $testee->has($data['key'])
        );
    }

    /**
     * @see testHas
     */
    public function hasData(): array
    {
        return [
            'existing valid value' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',
                        'filter' => FILTER_VALIDATE_INT,
                        'default_value' => 10,
                    ],
                    'value' => '10',
                ],
                'mocks' => [
                    'filterValidates' => true,
                ],
                'expected' => true,
            ],
            'existing valid value without default and filter' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',
                    ],
                    'value' => '10',
                ],
                'mocks' => [
                    'filterValidates' => true,
                ],
                'expected' => true,
            ],
            'invalid value does not fall back to default value' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',
                        'default_value' => 10,
                        'filter' => FILTER_VALIDATE_INT,
                    ],
                    'value' => 'NAN',
                ],
                'mocks' => [
                    'filterValidates' => false,
                ],
                'expected' => false,
            ],
            'not existing value falls back to default value' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',
                        'default_value' => 10,
                        'filter' => FILTER_VALIDATE_INT,
                    ],
                    'value' => null,
                ],
                'mocks' => [
                    'filterValidates' => true,
                ],
                'expected' => true,
            ],
            'not existing value' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',

                    ],
                    'value' => null,
                ],
                'mocks' => [
                    'filterValidates' => false,
                ],
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider getData
     */
    public function testGet(array $data, array $mocks, $expected)
    {
        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);

        $schema->shouldReceive('getDefinition')
            ->with($data['key'])
            ->andReturn($data['schema']);

        if (null !== $mocks['filterValidates']) {
            $filter->shouldReceive('validateValue')
                ->with($data['value'], $data['schema'])
                ->andReturn($mocks['filterValidates']);
        }

        if (null !== $mocks['filteredValue']) {
            $filter->shouldReceive('filterValue')
                ->with($data['value'], $data['schema'])
                ->andReturn($mocks['filteredValue']);
        }

        $optionLoader = function ($optionName, $default = false) use ($data) {
            self::assertSame(
                $optionName,
                $data['schema']['source_name']
            );
            if (false !== $default && array_key_exists('default_value', $data['schema'])) {
                self::assertSame(
                    $data['schema']['default_value'],
                    $default
                );
            }

            return $data['value'] ?? $default;
        };

        $testee = new WpOption($optionLoader, $schema, $filter);
        self::assertSame(
            $expected,
            $testee->get($data['key'])
        );
    }

    /**
     * @see testGet
     */
    public function getData(): array
    {
        return [
            'existing valid value' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',
                        'filter' => FILTER_VALIDATE_INT,
                        'default_value' => 10,
                    ],
                    'value' => '10',
                ],
                'mocks' => [
                    'filterValidates' => true,
                    'filteredValue' => 10,
                ],
                'expected' => 10,
            ],
            'existing valid value without default and filter' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',
                    ],
                    'value' => '10',
                ],
                'mocks' => [
                    'filterValidates' => true,
                    'filteredValue' => 10,
                ],
                'expected' => 10,
            ],
            'not existing value falls back to default value' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',
                        'default_value' => 10.0,
                        'filter' => FILTER_VALIDATE_FLOAT,
                    ],
                    'value' => null,
                ],
                'mocks' => [
                    'filterValidates' => null,
                    'filteredValue' => null,
                ],
                'expected' => 10.0,
            ],
        ];
    }

    public function testGetThrowsMissingConfigException()
    {
        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);
        $key = 'not.a.config';

        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn([]);

        self::expectException(MissingConfig::class);

        (new WpOption(
            function () {
            }, $schema, $filter
        ))
            ->get($key);
    }

    public function testAsWpOption()
    {
        $key = 'site.home_url';
        $sourceName = 'home';
        $defaultValue = 'http://localhost';
        $expected = 'http://my.site';
        $definition = [
            'source' => Source::SOURCE_WP_OPTION,
            'source_name' => 'home',
            'default_value' => $defaultValue,
        ];

        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);

        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definition);

        $filter->shouldReceive(
            [
                'validateValue' => true,
                'filterValue' => $expected,
            ]
        );

        Functions\expect('get_option')
            ->once()
            ->with($sourceName, $defaultValue)
            ->andReturn($expected);

        self::assertSame(
            $expected,
            (WpOption::asWpOption($schema, $filter))->get($key)
        );
    }

    public function testAsWpSiteOption()
    {
        $key = 'site.home_url';
        $sourceName = 'home';
        $defaultValue = 'http://localhost';
        $expected = 'http://my.site';
        $definition = [
            'source' => Source::SOURCE_WP_OPTION,
            'source_name' => 'home',
            'default_value' => $defaultValue,
        ];

        $schema = \Mockery::mock(Schema::class);
        $filter = \Mockery::mock(Filter::class);

        $schema->shouldReceive('getDefinition')
            ->with($key)
            ->andReturn($definition);

        $filter->shouldReceive(
            [
                'validateValue' => true,
                'filterValue' => $expected,
            ]
        );

        Functions\expect('get_site_option')
            ->once()
            ->with($sourceName, $defaultValue)
            ->andReturn($expected);

        self::assertSame(
            $expected,
            (WpOption::asWpSiteoption($schema, $filter))->get($key)
        );
    }
}
