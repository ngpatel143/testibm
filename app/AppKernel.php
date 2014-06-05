<?php

use Symfony\Component\ClassLoader\DebugUniversalClassLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Sylius\Bundle\CoreBundle\Kernel\SyliusKernel;

class AppKernel extends SyliusKernel
{
    public function registerBundles()
    {
        $bundles = array(
           

            // CMF bundles.
            
            new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle(),
            new \Ibillmaker\Hub\CoreBundle\IbillmakerHubCoreBundle(),
        );

        return array_merge(parent::registerBundles(),$bundles);
    }
}
