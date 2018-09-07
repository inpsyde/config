<?php
declare(strict_types=1);

namespace Inpsyde\Config;

use Inpsyde\Config\Exception\RuntimeException;
use MonkeryTestCase\BrainMonkeyWpTestCase;

class LoaderTest extends BrainMonkeyWpTestCase
{

    private $environment = [];

    /**
     * @group unit
     */
    public function testLoadFromArray()
    {
        $config = require __DIR__.'/../data/data-LoaderTest.php';
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
        $file = __DIR__ . '/../data/data-LoaderTest.php';
        self::assertInstanceOf(
            Config::class,
            Loader::loadFromFile($file)
        );
    }

    /**
     * @dataProvider loadFromFileThrowsExceptionData
     */
    public function testLoadFromFileThrowsException(string $file, string $expectedException)
    {
        self::expectException($expectedException);
        Loader::loadFromFile($file);
    }

    /**
     * @see testLoadFromFileThrowsException
     */
    public function loadFromFileThrowsExceptionData(): array {
        return [
            'file is not valid file' => [
                'file' => __DIR__,
                'expectedException' => RuntimeException::class,
            ],
            'file is not readable' => [
                'file' => '/root/.bash_history',
                'expectedException' => RuntimeException::class,
            ],
            'file does not contain array' => [
                'file' => __DIR__ .'/../data/data-LoaderTest::testLoadFromFileThrowsException.php',
                'expectedException' => RuntimeException::class,
            ]
        ];
    }

    /**
     * @group integration
     */
    public function testFunctionalLoadFromArray()
    {
        $configSchema = require __DIR__.'/../data/data-LoaderTest.php';
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
        $file = __DIR__.'/../data/data-LoaderTest.php';
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
