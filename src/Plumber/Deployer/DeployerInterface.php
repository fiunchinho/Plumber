<?php
namespace Plumber\Deployer;

use Plumber\Server\ServerInterface;

interface DeployerInterface
{
    /**
     * Deploy to the specified server
     *
     * @param Server  The server
     * @param array   Options
     */
    function deploy(ServerInterface $server, array $options = array());
}