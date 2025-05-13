<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    
    /**
     * Override pour éviter le chargement de bundles de développement en production
     */
    public function registerBundles(): iterable
    {
        $bundlesPath = $this->getProjectDir() . '/config/bundles.php';
        $bundles = require $bundlesPath;
        
        // En production, filtrer les bundles qui ne sont que pour dev/test
        if ($this->environment === 'prod') {
            foreach ($bundles as $class => $envs) {
                if (isset($envs['all']) && $envs['all'] === true) {
                    yield new $class();
                } elseif (isset($envs['prod']) && $envs['prod'] === true) {
                    yield new $class();
                }
            }
        } else {
            foreach ($bundles as $class => $envs) {
                if (isset($envs['all']) 
                    || isset($envs[$this->environment]) && $envs[$this->environment] === true
                ) {
                    yield new $class();
                }
            }
        }
    }
}
