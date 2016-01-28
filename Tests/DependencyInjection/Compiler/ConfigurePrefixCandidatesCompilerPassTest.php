<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\MultiTenancyBundle\Tests\DependencyInjection\Compiler;

use SWP\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass;
use Symfony\Component\DependencyInjection\Reference;

class ConfigurePrefixCandidatesCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $definition;
    private $pass;

    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $this->pass = new ConfigurePrefixCandidatesCompilerPass();
    }

    /**
     * @covers SWP\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass::process
     */
    public function testProcess()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', true],
                ['cmf_routing.backend_type_phpcr', true],
            ]));

        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('kernel.bundles')
            ->will($this->returnValue(array(
                'CmfRoutingBundle' => true,
            )));

        $this->container->expects($this->once())
            ->method('getDefinition')
            ->with('cmf_routing.phpcr_candidates_prefix')
            ->will($this->returnValue($this->definition));

        $this->definition->expects($this->once())
            ->method('setConfigurator')
            ->with([
                new Reference('swp_multi_tenancy.candidates_configurator'),
                'configure',
            ]);

        $this->pass->process($this->container);
    }

    /**
     * @covers SWP\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass::process
     */
    public function testProcessPHPCRBackendDisabled()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', false],
                ['cmf_routing.backend_type_phpcr', true],
            ]));

        $this->assertNull($this->pass->process($this->container));
    }

    /**
     * @covers SWP\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass::process
     */
    public function testProcessCMFBackendDisabled()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', true],
                ['cmf_routing.backend_type_phpcr', false],
            ]));

        $this->assertNull($this->pass->process($this->container));
    }

    /**
     * @covers SWP\MultiTenancyBundle\DependencyInjection\Compiler\ConfigurePrefixCandidatesCompilerPass::process
     */
    public function testProcessWithoutConfig()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', false],
                ['cmf_routing.backend_type_phpcr', false],
            ]));

        $this->assertNull($this->pass->process($this->container));
    }

    /**
     * @expectedException Symfony\Component\DependencyInjection\Exception\RuntimeException
     */
    public function testNoBundle()
    {
        $this->container->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['swp_multi_tenancy.backend_type_phpcr', true],
                ['cmf_routing.backend_type_phpcr', true],
            ]));

        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('kernel.bundles')
            ->will($this->returnValue(array()));

        $this->pass->process($this->container);
    }
}
