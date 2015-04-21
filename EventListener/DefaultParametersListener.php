<?php

/*
 * (c) Philipp Wahala <philipp.wahala@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace hacfi\AwsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Yaml\Yaml;

use Guzzle\Common\Event;


class DefaultParametersListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $parametersFile;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return ['command.before_prepare' => ['onCommandBeforePrepare', 16]];
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

        if (!is_array($defaultParameters = $this->getDefaultParameters())) {
            return;
        }

        if (!isset($defaultParameters[$fullCommandName])) {
            return;
        }

        $commandParameters = $defaultParameters[$fullCommandName];

        foreach (array_diff_key($commandParameters, $command->getAll()) as $key => $value) {
            $command[$key] = $value;
        }
    }

    /**
     * @return array|bool
     */
    public function getDefaultParameters()
    {
        if ($this->parameters === null) {
            if (!$this->getParametersFile()) {
                $this->parameters = false;

                return $this->parameters;
            }

            $this->parameters = Yaml::parse(file_get_contents($this->getParametersFile()));
        }

        return $this->parameters;
    }

    /**
     * @param string $parametersFile
     * @return $this self Object
     */
    public function setParametersFile($parametersFile)
    {
        $this->parametersFile = $parametersFile;

        return $this;
    }

    /**
     * @return string
     */
    public function getParametersFile()
    {
        return $this->parametersFile;
    }




}
