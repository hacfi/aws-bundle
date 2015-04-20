<?php

namespace hacfi\AwsBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use hacfi\AwsBundle\DependencyInjection\hacfiAwsExtension;


class hacfiAwsBundle extends Bundle
{
    private $configurationAlias;

    public function __construct($alias = 'hacfi_aws')
    {
        $this->configurationAlias = $alias;
    }

    public function getContainerExtension()
    {
        return new hacfiAwsExtension($this->configurationAlias);
    }

    public function registerCommands(Application $application)
    {
        return;
    }
}
