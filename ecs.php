<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

if (class_exists(ContainerConfigurator::class)) {
    return static function (ContainerConfigurator $containerConfigurator): void {
        $containerConfigurator->import('vendor/sylius-labs/coding-standard/ecs.php');
        $containerConfigurator->parameters()->set(Option::PATHS, [
            'src', 'tests'
        ]);
    };
}

return static function (ECSConfig $containerConfigurator): void {
    $containerConfigurator->import('vendor/sylius-labs/coding-standard/ecs.php');
    $containerConfigurator->paths(['src', 'tests']);
};
