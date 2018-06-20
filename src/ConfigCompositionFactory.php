<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Source\Constant;
use Inpsyde\Config\Source\Environment;
use Inpsyde\Config\Source\Source;
use Inpsyde\Config\Source\WpOption;

class ConfigCompositionFactory
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

    public function buildConfig(array $schema): Config
    {
        return new ConfigComposition(
            $this->buildSourcesList($schema)
        );
    }

    /**
     * @return Config[] Assoc array [$key => $config]
     */
    public function buildSourcesList(array $definition): array
    {
        $sources = [];
        $schema = $this->validator->validateSchema($definition);
        $filter = new Filter();

        foreach ($this->sourceFactories() as $source => $factory) {
            $sourceConfig = $factory($schema, $filter);
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
            Source::SOURCE_ENV => function (Schema $schema, Filter $filter = null) {
                return new Environment($schema, $filter);
            },
            Source::SOURCE_WP_OPTION => function (Schema $schema, Filter $filter = null) {
                return WpOption::asWpOption($schema, $filter);
            },
            Source::SOURCE_WP_SITEOPTION => function (Schema $schema, Filter $filter = null) {
                return WpOption::asWpSiteoption($schema, $filter);
            },
            Source::SOURCE_CONSTANT => function (Schema $schema, Filter $filter = null) {
                return new Constant($schema, $filter);
            },
        ];
    }
}