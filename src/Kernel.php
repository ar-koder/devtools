<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    public function boot(): void
    {
        if (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'on') &&
            (empty($_SERVER['IS_DOCKERIZED']) || ! $_SERVER['IS_DOCKERIZED']) &&
            in_array($this->getEnvironment(), ['prod', 'staging'])
        ) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
            exit;
        }

        parent::boot();
    }

    public function process(ContainerBuilder $container): void
    {
        $container
            ->getDefinition('doctrine.dbal.bin_connection')
            ->addMethodCall('setBucketDir', [
                new Parameter('buckets_dirs'),
            ])
        ;
    }
}
