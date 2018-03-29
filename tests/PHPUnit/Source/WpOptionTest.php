<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

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

        $schema->expects('getDefinition')
            ->times(5)
            ->with($data['key'])
            ->andReturn($data['schema']);

        $filter->expects('validateValue')
            ->once()
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

    public function testGet()
    {
        self::markTestSkipped();
    }

    /**
     * @see testHas
     */
    public function hasData()
    {
        return [
            'with default value' => [
                'data' => [
                    'key' => 'some.config.key',
                    'schema' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'source_name' => '_option_name',
                        'default_value' => 10,
                    ],
                    'value' => '10',
                ],
                'mocks' => [
                    'filterValidates' => true,
                ],
                'expected' => true,
            ],
            'no default value' => [
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
        ];
    }
}
