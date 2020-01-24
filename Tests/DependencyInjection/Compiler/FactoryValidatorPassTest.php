<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\DependencyInjection\Compiler;

use Bazinga\GeocoderBundle\DependencyInjection\Compiler\FactoryValidatorPass;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class FactoryValidatorPassTest extends TestCase
{
    use SetUpTearDownTrait;

    /**
     * @var FactoryValidatorPass
     */
    private $compilerPass;

    /**
     * @var string
     */
    private $factoryId;

    protected function doSetUp()
    {
        $this->compilerPass = new FactoryValidatorPass();
        $this->factoryId = 'dummy_factory_id';
        $this->compilerPass::addFactoryServiceId($this->factoryId);
    }

    protected function doTearDown()
    {
        $reflection = new \ReflectionObject($this->compilerPass);
        $prop = $reflection->getProperty('factoryServiceIds');
        $prop->setAccessible(true);
        $prop->setValue([]);
    }

    public function testProcessThrows()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage("Factory with ID \"$this->factoryId\" could not be found");

        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasAlias')
            ->with($this->factoryId)
            ->willReturn(false);
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->factoryId)
            ->willReturn(false);

        $this->compilerPass->process($container);
    }

    public function testProcessDoesntThrowIfAliasExists()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasAlias')
            ->with($this->factoryId)
            ->willReturn(true);
        $container->expects($this->never())
            ->method('hasDefinition')
            ->with($this->factoryId)
            ->willReturn(false);

        $this->compilerPass->process($container);
    }

    public function testProcessDoesntThrowIfDefinitionExists()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasAlias')
            ->with($this->factoryId)
            ->willReturn(false);
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->factoryId)
            ->willReturn(true);

        $this->compilerPass->process($container);
    }
}
