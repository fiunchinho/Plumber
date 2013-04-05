<?php

namespace Plum\Deployer;

use Plum\Server\ServerInterface;

interface DeployerInterface
{
    /**
     * Deploy to the specified server
     *
     * @param Server  The server
     * @param array   Options
     */
    function deploy(ServerInterface $server, array $options = array());

    /**
     * Returns the name of the Deployer
     *
     * @return string
     */
    function getName();
}