<?php

declare(strict_types=1);

namespace Pheature\Crud\Psr7\Toggle;

use Pheature\Crud\Toggle\Handler\RemoveFeature;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class DeleteFeatureFactory
{
    public function __invoke(ContainerInterface $container): DeleteFeature
    {
        /** @var RemoveFeature $removeFeature */
        $removeFeature = $container->get(RemoveFeature::class);
        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        return self::create($removeFeature, $responseFactory);
    }

    public static function create(
        RemoveFeature $removeFeature,
        ResponseFactoryInterface $responseFactory
    ): DeleteFeature {
        return new DeleteFeature($removeFeature, $responseFactory);
    }
}
