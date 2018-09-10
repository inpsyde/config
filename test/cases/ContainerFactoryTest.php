<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use function Brain\Monkey\Functions\expect;
use Inpsyde\Config\Source\Constant;
use Inpsyde\Config\Source\Environment;
use Inpsyde\Config\Source\Source;
use Inpsyde\Config\Source\Variable;
use Inpsyde\Config\Source\WpOption;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class ContainerFactoryTest extends BrainMonkeyWpTestCase
{

    private $environment = [];

    /**
     * @dataProvider buildContainerIntegrationData
     * @group integration
     */
    public function testBuildContainerIntegration(array $definition, array $config, array $mockData, array $expected)
    {
        if (array_key_exists('env', $mockData)) {
            foreach ( $mockData['env'] as $envName => $envValue) {
                $this->putEnv($envName, $envValue);
            }
        }
        $mockWpOption = function($function = 'get_option') {
            return function($mock) use ($function) {
                expect($function)
                    ->with($mock['key'], $mock['default'])
                    ->andReturn($mock['return']);
            };
        };
        if (array_key_exists('wp_option',$mockData)) {
            array_walk($mockData['wp_option'], $mockWpOption());
        }
        if (array_key_exists('wp_siteoption',$mockData)) {
            array_walk($mockData['wp_siteoption'], $mockWpOption('get_site_option'));
        }

        $container = Loader::loadFromArray($definition,$config);

        foreach ( $expected as $key => $expected) {
            static::assertTrue(
                $container->has($key),
                "Test failed for key '{$key}'"
            );
            static::assertSame(
                $expected,
                $container->get($key),
                "Test failed for key '{$key}'"
            );
        }
    }

    public function buildContainerIntegrationData(): array
    {
        return require __DIR__.'/../data/data-ContainerFactoryTest::testBuildContainerIntegration.php';
    }

    /**
     * @group unit
     */
    public function testBuildContainer()
    {
        $definition = [
            'some.config.key' => [
                'source' => Source::SOURCE_VARIABLE,
            ],
        ];
        $config = [
            'some.config.key' => 3.1415,
        ];
        $schema = \Mockery::mock(Schema::class);
        $schema->shouldReceive('getKeys')
            ->andReturnUsing(
                function ($source): array {
                    return Source::SOURCE_VARIABLE === $source
                        ? ['some.config.key']
                        : [];
                }
            );
        $schema->shouldReceive('getDefinition')
            ->with('some.config.key')
            ->andReturn($definition['some.config.key']);

        $validator = \Mockery::mock(SchemaValidation::class);
        $validator->shouldReceive('validateSchema')
            ->with($definition)
            ->andReturn($schema);

        static::assertInstanceOf(
            Config::class,
            (new ContainerFactory($validator))
                ->buildContainer($definition, $config)
        );
    }

    /**
     * @dataProvider buildSourcesListData
     * @group unit
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
        return require __DIR__.'/../data/data-ContainerFactoryTest::testBuildSourcesList.php';
    }

    private function putEnv(string $name, string $value)
    {
        $this->environment[$name] = $value;
        putenv("{$name}={$value}");
    }

    protected function tearDown()
    {
        parent::tearDown();

        foreach (array_keys($this->environment) as $env) {
            putenv($env);
        }

        $this->environment = [];
    }
}
