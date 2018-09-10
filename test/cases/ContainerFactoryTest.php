<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Source\Constant;
use Inpsyde\Config\Source\Environment;
use Inpsyde\Config\Source\Source;
use Inpsyde\Config\Source\Variable;
use Inpsyde\Config\Source\WpOption;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class ContainerFactoryTest extends BrainMonkeyWpTestCase
{

    /**
     * @dataProvider buildSourcesListData
     * @group integration
     */
    public function testBuildSourcesList(
        array $definition,
        array $variabelConfig,
        array $keysBySource,
        array $expectations
    ) {
        $schemaValidator = \Mockery::mock(SchemaValidation::class);
        $schema = \Mockery::mock(Schema::class);

        $schema->shouldReceive('getKeys')
            ->andReturnUsing(
                function ($source) use ($keysBySource): array {
                    return array_key_exists($source, $keysBySource)
                        ? $keysBySource[$source]
                        : [];
                }
            );

        $schemaValidator->shouldReceive('validateSchema')
            ->with($definition)
            ->andReturn($schema);

        $testee = new ContainerFactory($schemaValidator);
        $sourceList = $testee->buildSourcesList($definition, $variabelConfig);

        self::assertEquals(
            array_keys($expectations),
            array_keys($sourceList),
            "Sources list does not match expected keys",
            0.0,
            10,
            true // order of array elements is not relevant
        );

        foreach ($expectations as $key => $sourceType) {
            self::assertInstanceOf(
                $sourceType,
                $sourceList[$key],
                "Test failed for key {$key}"
            );
        }
    }

    /**
     * @see testBuildSourcesList
     */
    public function buildSourcesListData(): array
    {
        return [
            [
                'definition' => [
                    'config.env.one' => [
                        'source' => Source::SOURCE_ENV,
                    ],
                    'config.env.two' => [
                        'source' => Source::SOURCE_ENV,
                    ],
                    'config.env.three' => [
                        'source' => Source::SOURCE_ENV,
                    ],
                    'config.option.one' => [
                        'source' => Source::SOURCE_WP_OPTION,
                    ],
                    'config.option.two' => [
                        'source' => Source::SOURCE_WP_OPTION,
                    ],
                    'config.siteoption.one' => [
                        'source' => Source::SOURCE_WP_SITEOPTION,
                    ],
                    'config.constant.one' => [
                        'source' => Source::SOURCE_CONSTANT,
                    ],
                    'config.constant.two' => [
                        'source' => Source::SOURCE_CONSTANT,
                    ],
                    'config.variable.one' => [
                        'source' => Source::SOURCE_VARIABLE,
                    ],
                ],
                'variableConfig' => [
                    'config.variable.one' => true,
                ],
                'keysBySource' => [
                    Source::SOURCE_ENV => [
                        'config.env.one',
                        'config.env.two',
                        'config.env.three',
                    ],
                    Source::SOURCE_WP_OPTION => [
                        'config.option.one',
                        'config.option.two',
                    ],
                    Source::SOURCE_WP_SITEOPTION => [
                        'config.siteoption.one',
                    ],
                    Source::SOURCE_CONSTANT => [
                        'config.constant.one',
                        'config.constant.two',
                    ],
                    Source::SOURCE_VARIABLE => [
                        'config.variable.one',
                    ],
                ],
                'expectations' => [
                    'config.env.one' => Environment::class,
                    'config.env.two' => Environment::class,
                    'config.env.three' => Environment::class,
                    'config.option.one' => WpOption::class,
                    'config.option.two' => WpOption::class,
                    'config.siteoption.one' => WpOption::class,
                    'config.constant.one' => Constant::class,
                    'config.constant.two' => Constant::class,
                    'config.variable.one' => Variable::class,
                ],
            ],
        ];
    }
}
