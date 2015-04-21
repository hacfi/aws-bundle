<?php

/*
 * (c) Philipp Wahala <philipp.wahala@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace hacfi\AwsBundle\Client;

use Aws\Common\Client\AbstractClient;

use Guzzle\Service\Resource\Model;
use Guzzle\Service\Resource\ResourceIteratorInterface;

use Psr\Log\LoggerInterface;


class AwsClient
{
    /**
     * @var AbstractClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    public function __construct(AbstractClient $client, $serviceName, LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->serviceName = $serviceName;
        $this->logger = $logger;
    }

    /**
     * Proxy function to enable logging
     *
     * @param string $name      A command name
     * @param array  $arguments An array of command arguments
     *
     * @return Model|ResourceIteratorInterface
     */
    public function __call($name, array $arguments)
    {
        $startTime = microtime(true);
        $result = call_user_func_array([$this->client, $name], $arguments);
        $duration = (microtime(true) - $startTime) * 1000;

        if (null !== $this->logger) {
            $this->logger->info(sprintf('AWS SDK Request: Service %s - Command %s - %s ms', $this->serviceName, $name, $duration));
        }

        return $result;
    }

    /**
     * @return AbstractClient
     */
    public function getClient()
    {
        return $this->client;
    }
}
