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
namespace SWP\MultiTenancyBundle\Doctrine\PHPCR;

use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilderInterface;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\PrefixCandidates;

/**
 * CMF Candidates Configurator allows to use TenantAwarePathBuilder to configure
 * Symfony CMF PrefixCandidates service after its instantiation.
 */
class CmfCandidatesConfigurator
{
    /**
     * @var TenantAwarePathBuilderInterface
     */
    private $pathBuilder;

    /**
     * @var array
     */
    private $routeBasePaths;

    /**
     * Construct.
     *
     * @param TenantAwarePathBuilderInterface $pathBuilder    Tenant aware path builder
     * @param array                           $routeBasePaths A set of paths where routes are located in the PHPCR tree
     */
    public function __construct(TenantAwarePathBuilderInterface $pathBuilder, array $routeBasePaths)
    {
        $this->pathBuilder = $pathBuilder;
        $this->routeBasePaths = $routeBasePaths;
    }

    /**
     * Configures CMF PrefixCandidates service
     * by setting its prefixes to tenant aware paths.
     *
     * @param PrefixCandidates $candidates PrefixCandidates object
     */
    public function configure(PrefixCandidates $candidates)
    {
        $candidates->setPrefixes((array) $this->pathBuilder->build($this->routeBasePaths));
    }
}
