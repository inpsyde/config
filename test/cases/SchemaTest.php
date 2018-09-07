<?php
declare(strict_types = 1);

namespace Inpsyde\Config;

use Inpsyde\Config\Source\Source;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class SchemaTest extends BrainMonkeyWpTestCase
{

    /**
     * @dataProvider definitionData
     * @group unit
     */
    public function testGetKeys(array $definition, $unnused, $expectedKeys)
    {

        $testee = new Schema($definition);
        self::assertSame(
            $expectedKeys,
            $testee->getKeys()
        );
    }

    /**
     * @dataProvider definitionData
     * @group unit
     */
    public function testGetKeysForSource(array $definition, array $expectedSource)
    {

        $testee = new Schema($definition);
        foreach ($expectedSource as $soruce => $keys) {
            self::assertSame(
                $keys,
                $testee->getKeys($soruce),
                "Test failed for source {$soruce}"
            );
        }
    }

    /**
     * @dataProvider definitionData
     * @group unit
     */
    public function testGetDefinition(array $definitions)
    {

        $testee = new Schema($definitions);

        foreach ($definitions as $key => $definition) {
            self::assertSame(
                $definition,
                $testee->getDefinition($key),
                "Test failed for key {$key}"
            );
        }
    }

    public function definitionData(): array
    {

        return [
            'complete definition' => [
                'definiton' => [
                    'key.constant.one' => [
                        'source' => Source::SOURCE_CONSTANT,
                        'definition' => ['constant.one'],
                    ],
                    'key.constant.two' => [
                        'source' => Source::SOURCE_CONSTANT,
                        'definition' => ['constant.two'],
                    ],
                    'key.env.one' => [
                        'source' => Source::SOURCE_ENV,
                        'definition' => ['env.one'],
                    ],
                    'key.env.two' => [
                        'source' => Source::SOURCE_ENV,
                        'definition' => ['env.two'],
                    ],
                    'key.wpoption.one' => [
                        'source' => Source::SOURCE_WP_OPTION,
                        'definition' => ['wpoption.one'],
                    ],

                ],
                'expected keys for source' => [
                    Source::SOURCE_CONSTANT => [
                        'key.constant.one',
                        'key.constant.two',
                    ],
                    Source::SOURCE_ENV => [
                        'key.env.one',
                        'key.env.two',
                    ],
                    Source::SOURCE_WP_OPTION => [
                        'key.wpoption.one',
                    ],
                    Source::SOURCE_WP_SITEOPTION => [],
                ],
                'expected keys' => [
                    'key.constant.one',
                    'key.constant.two',
                    'key.env.one',
                    'key.env.two',
                    'key.wpoption.one',
                ],
            ],
        ];
    }
}
