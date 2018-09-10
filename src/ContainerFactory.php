<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Helper\SchemaReader;
use Inpsyde\Config\Source\Constant;
use Inpsyde\Config\Source\Environment;
use Inpsyde\Config\Source\Source;
use Inpsyde\Config\Source\Variable;
use Inpsyde\Config\Source\WpOption;

class ContainerFactory
{

    /**
     * @var SchemaValidation
     */
    private $validator;

    /**
     * @param SchemaValidation $validator
     */
    public function __construct(SchemaValidation $validator)
    {
        $this->validator = $validator;
    }

    public function buildContainer(array $definition, array $config = []): Config
    {
        return new Container(
            $this->buildSourcesList($definition, $config)
        );
    }

    /**
     * @return Config[] Assoc array [$key => $config]
     */
    public function buildSourcesList(array $definition, array $config = []): array
    {
        $sources = [];
        $schema = $this->validator->validateSchema($definition);
        $reader = new SchemaReader();
        $filter = new Filter();

        foreach ($this->sourceFactories() as $source => $factory) {
            $sourceConfig = $factory($schema, $filter, $reader, $config);
            $keys = $schema->getKeys($source);
            $sources += array_combine(
                $keys,
                array_fill(0, count($keys), $sourceConfig)
            );
        }

        return $sources;
    }

    private function sourceFactories(): array
    {
        return [
            Source::SOURCE_ENV => function (Schema $schema, Filter $filter = null, SchemaReader $reader = null) {
                return new Environment($schema, $filter, $reader);
            },
            Source::SOURCE_WP_OPTION => function (Schema $schema, Filter $filter = null, SchemaReader $reader = null) {
                return WpOption::asWpOption($schema, $filter, $reader);
            },
            Source::SOURCE_WP_SITEOPTION => function (
                Schema $schema,
                Filter $filter = null,
                SchemaReader $reader = null
            ) {
                return WpOption::asWpSiteOption($schema, $filter, $reader);
            },
            Source::SOURCE_CONSTANT => function (Schema $schema, Filter $filter = null, SchemaReader $reader = null) {
                return new Constant($schema, $filter, $reader);
            },
            Source::SOURCE_VARIABLE => function (
                Schema $schema,
                Filter $filter = null,
                SchemaReader $reader = null,
                array $config = []
            ) {
                return new Variable($schema, $config, $filter, $reader);
            },
        ];
    }
}
