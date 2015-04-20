<?php

/*
 * (c) Philipp Wahala <philipp.wahala@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hacfi\AwsBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Guzzle\Common\Event;


class ResolveParametersListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return ['command.before_prepare' => ['onCommandBeforePrepare']];
    }

    /**
     * @param Event $event Event emitted
     */
    public function onCommandBeforePrepare(Event $event)
    {
        /** @var \Guzzle\Service\Command\AbstractCommand $command */
        $command = $event['command'];

        /** @var \Aws\Common\Client\AbstractClient $client */
        $client = $command->getClient();

        $config = $client->getConfig();
        $service = $config->get('service');

        $fullCommandName = sprintf('%s.%s', $service, $command->getName());

        $parameters = $command->filter(function ($key, $value) {
            return strpos($key, 'command.') !== 0;
        }, false);

        foreach ($parameters->getKeys() as $key) {
            $command[$key] = $this->getContainerParameterBag()->resolveValue($command[$key]);
        }
    }

    /**
     * @return FrozenParameterBag
     */
    protected function getContainerParameterBag()
    {
        return $this->container->getParameterBag();
    }

}
