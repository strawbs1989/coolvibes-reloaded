<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests;

use PhpMyAdmin\Application;
use PhpMyAdmin\Config;
use PhpMyAdmin\ErrorHandler;
use PhpMyAdmin\Exceptions\ConfigException;
use PhpMyAdmin\Http\Factory\ResponseFactory;
use PhpMyAdmin\Template;
use PHPUnit\Framework\Attributes\BackupStaticProperties;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(Application::class)]
final class ApplicationTest extends AbstractTestCase
{
    public function testInit(): void
    {
        $application = new Application(
            $this->createStub(ErrorHandler::class),
            $this->createStub(Config::class),
            $this->createStub(Template::class),
            new ResponseFactory($this->createStub(ResponseFactoryInterface::class)),
        );
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())->method('get')
            ->with($this->identicalTo(Application::class))->willReturn($application);
        $GLOBALS['containerBuilder'] = $container;
        $this->assertSame($application, Application::init());
    }

    #[BackupStaticProperties(true)]
    public function testRunWithConfigError(): void
    {
        $errorHandler = $this->createStub(ErrorHandler::class);

        $config = $this->createMock(Config::class);
        $config->expects($this->once())->method('loadAndCheck')
            ->willThrowException(new ConfigException('Failed to load phpMyAdmin configuration.'));

        $template = new Template($config);
        $expected = $template->render('error/generic', [
            'lang' => 'en',
            'dir' => 'ltr',
            'error_message' => 'Failed to load phpMyAdmin configuration.',
        ]);

        $application = new Application($errorHandler, $config, $template, ResponseFactory::create());
        $application->run();

        $output = $this->getActualOutputForAssertion();
        $this->assertSame($expected, $output);
    }
}
