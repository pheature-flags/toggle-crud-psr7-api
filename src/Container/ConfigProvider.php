<?php

declare(strict_types=1);

namespace Pheature\Crud\Psr7\Toggle\Container;

use Pheature\Crud\Psr7\Toggle\DeleteFeatureFactory;
use Pheature\Crud\Psr7\Toggle\GetFeatureFactory;
use Pheature\Crud\Psr7\Toggle\GetFeaturesFactory;
use Pheature\Crud\Psr7\Toggle\PatchFeatureFactory;
use Pheature\Crud\Psr7\Toggle\PostFeatureFactory;
use Pheature\Crud\Psr7\Toggle\DeleteFeature;
use Pheature\Crud\Psr7\Toggle\GetFeature;
use Pheature\Crud\Psr7\Toggle\GetFeatures;
use Pheature\Crud\Psr7\Toggle\PatchFeature;
use Pheature\Crud\Psr7\Toggle\PostFeature;

/**
 * @psalm-type CrudPsr7Config array{dependencies: array{factories: array<string, string>}}
 */
final class ConfigProvider
{
    /** @return CrudPsr7Config */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    GetFeatures::class => GetFeaturesFactory::class,
                    GetFeature::class => GetFeatureFactory::class,
                    PostFeature::class => PostFeatureFactory::class,
                    PatchFeature::class => PatchFeatureFactory::class,
                    DeleteFeature::class => DeleteFeatureFactory::class,
                ],
            ],
        ];
    }
}
