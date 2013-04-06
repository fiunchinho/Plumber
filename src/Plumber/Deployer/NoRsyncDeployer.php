<?php
namespace Plumber\Deployer;

use Plumber\Server\ServerInterface;

class NoRsyncDeployer implements DeployerInterface
{
    /**
     * Dummy deployer that does nothing. Useful when using only SSH commands.
     *
     * @param ServerInterface $server  The server
     * @param array           $options The options
     */
    public function deploy( ServerInterface $server, array $options = array() )
    {
        return true;
    }

    /**
     * @return string The deployer identifier
     */
    public function getName()
    {
        return 'no rsync';
    }
}