<?php

/*
 * (c) Philipp Wahala <philipp.wahala@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hacfi\AwsBundle\S3;

use Aws\S3\StreamWrapper;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class StreamWrapperRegistry implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $services;

    /**
     * @var array
     */
    private $registeredProtocols = [];

    /**
     * @param array $services
     */
    public function __construct(array $services = [])
    {
        $this->services = $services;
    }


    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function registerAll()
    {
        foreach (array_keys($this->services) as $protocol) {
            $this->register($protocol);
        }
    }

    /**
     * @param string $protocol
     */
    public function register($protocol)
    {
        if (isset($this->registeredProtocols[$protocol])) {
            return;
        }

        $service = $this->services[$protocol];

        StreamWrapper::register(
            $this->getS3Service($service),
            $protocol
        );

        $this->registeredProtocols[$protocol] = true;
    }

    /**
     * @param string $service
     *
     * @return \Aws\S3\S3Client
     */
    private function getS3Service($service)
    {
        return $this->container->get($service);
    }
}
