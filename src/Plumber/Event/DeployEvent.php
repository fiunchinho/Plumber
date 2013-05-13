<?php
namespace Plumber\Event;

use Symfony\Component\EventDispatcher\Event;

class DeployEvent extends Event
{
    protected $server;
    protected $options;

    public function __construct( \Plumber\Server\Server $server, array $options )
    {
        $this->server   = $server;
        $this->options  = $options;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getOptions()
    {
        return $this->options;
    }
}