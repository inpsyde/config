<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use MonkeryTestCase\BrainMonkeyWpTestCase;

class LoaderTest extends BrainMonkeyWpTestCase
{

    private $environment = [];

    /**
     * @group unit
     */
    public function testLoadFromArray()
    {
        $config = require dirname(__DIR__).'/data/loader-test.php';
        self::assertInstanceOf(
            Config::class,
            Loader::loadFromArray($config)
        );
    }

    /**
     * @group unit
     */
    public function testLoadFromFile()
    {
        $file = dirname(__DIR__).'/data/loader-test.php';
        self::assertInstanceOf(
            Config::class,
            Loader::loadFromFile($file)
        );
    }

    /**
     * @group integration
     */
    public function testFunctionalLoadFromArray()
    {
        $configSchema = require dirname(__DIR__).'/data/loader-test.php';
        $value = 3.14;
        $this->putEnv('CONFIG_LOADER_TEST', (string) $value);

        $config = Loader::loadFromArray($configSchema);

        self::assertSame(
            $value,
            $config->get('inpsyde.config.loaderTest')
        );
    }

    /**
     * @group integration
     */
    public function testFunctionalLoadFromFile()
    {
        $file = dirname(__DIR__).'/data/loader-test.php';
        $value = 3.14159;
        $this->putEnv('CONFIG_LOADER_TEST', (string) $value);

        $config = Loader::loadFromFile($file);

        self::assertSame(
            $value,
            $config->get('inpsyde.config.loaderTest')
        );
    }

    protected function tearDown()
    {
        parent::tearDown();

        foreach (array_keys($this->environment) as $env) {
            putenv($env);
        }

        $this->environment = [];
    }

    private function putEnv(string $name, string $value)
    {
        $this->environment[$name] = $value;
        putenv("{$name}={$value}");
    }
}
