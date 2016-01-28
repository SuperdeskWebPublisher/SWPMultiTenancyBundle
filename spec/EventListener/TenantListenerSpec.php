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
namespace spec\SWP\MultiTenancyBundle\EventListener;

use PhpSpec\ObjectBehavior;
use SWP\Component\MultiTenancy\Model\Tenant;
use SWP\Component\MultiTenancy\Resolver\TenantResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class TenantListenerSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        TenantContextInterface $tenantContext,
        TenantResolverInterface $tenantResolver
    ) {
        $this->beConstructedWith($entityManager, $tenantContext, $tenantResolver);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('SWP\MultiTenancyBundle\EventListener\TenantListener');
    }

    public function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    public function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            KernelEvents::REQUEST => 'onKernelRequest',
        ));
    }

    public function it_skips_tenantable_filter_on_kernel_request(
        GetResponseEvent $event,
        $tenantContext,
        Request $request,
        TenantResolverInterface $tenantResolver,
        $entityManager
    ) {
        $tenant = new Tenant();
        $tenant->setSubdomain('default');
        $tenant->setName('Default');

        $tenantContext->getTenant()->shouldBeCalled()->willReturn(new Tenant());
        $entityManager->getFilters()->shouldNotBeCalled();
        $request->getHost()->willReturn('example.com');
        $event->getRequest()->willReturn($request);
        $tenantResolver->resolve('example.com')->willReturn($tenant);
        $tenantContext->setTenant($tenant)->shouldBeCalled();

        $this->onKernelRequest($event)->shouldBeNull();
    }
}
