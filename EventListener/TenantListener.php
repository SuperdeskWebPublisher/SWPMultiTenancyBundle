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
namespace SWP\MultiTenancyBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use SWP\Component\MultiTenancy\Context\TenantContextInterface;
use SWP\Component\MultiTenancy\Resolver\TenantResolverInterface;

/**
 * Tenant Listener class.
 *
 * It makes sure there is always tenant set in context and queries.
 * It resolves current tenant based on subdomain and sets the resolved
 * tenant in context so it can be available everywhere in the system.
 * Additionally it makes sure all SELECT queries are tenant aware.
 */
class TenantListener implements EventSubscriberInterface
{
    /**
     * @var TenantContextInterface
     */
    protected $tenantContext;

    /**
     * @var TenantResolverInterface
     */
    protected $tenantResolver;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Construct.
     *
     * @param EntityManagerInterface  $entityManager
     * @param TenantContextInterface  $tenantContext
     * @param TenantResolverInterface $tenantResolver
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TenantContextInterface $tenantContext,
        TenantResolverInterface $tenantResolver
    ) {
        $this->entityManager = $entityManager;
        $this->tenantContext = $tenantContext;
        $this->tenantResolver = $tenantResolver;
    }

    /**
     * Resolve and set tenant on kernel.request.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $tenant = $this->tenantContext->getTenant();
        $tenantId = $tenant->getId();
        if ($tenantId) {
            $this->entityManager
                ->getFilters()
                ->enable('tenantable')
                ->setParameter('tenantId', $tenantId);
        }

        if (null !== $tenantId) {
            return;
        }

        $request = $event->getRequest();
        $this->tenantContext->setTenant(
            $this->tenantResolver->resolve($request->getHost())
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
