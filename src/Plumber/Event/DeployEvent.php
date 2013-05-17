<?php
namespace Plumber\Event;

use Symfony\Component\EventDispatcher\Event;

class DeployEvent extends Event
{
    protected $server;
    protected $options;
    protected $log;

    public function __construct( \Plumber\Server\Server $server, array $options, $log = null )
    {
        $this->server   = $server;
        $this->options  = $options;
        $this->log      = $log;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getLogger()
    {
        return $this->log;
    }
}