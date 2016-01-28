<?php

/**
 * This file is part of the Superdesk Web Publisher MultiTenancy Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace spec\SWP\MultiTenancyBundle\Doctrine\PHPCR;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;

class CmfCandidatesConfiguratorSpec extends ObjectBehavior
{
    private $prefixes;

    public function let(TenantAwarePathBuilderInterface $pathBuilder)
    {
        $this->prefixes = ['routes', 'routes2'];
        $this->beConstructedWith($pathBuilder, $this->prefixes);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\MultiTenancyBundle\Doctrine\PHPCR\CmfCandidatesConfigurator');
    }

    public function it_should_test_configure($candidates, $pathBuilder)
    {
        $pathBuilder->build($this->prefixes)->willReturn(['/swp/default/routes', '/swp/default/routes2']);

        $candidates->beADoubleOf('Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\PrefixCandidates');
        $candidates->setPrefixes(['/swp/default/routes', '/swp/default/routes2'])->shouldBeCalled();

        $this->configure($candidates)->shouldReturn(null);
    }
}
