<?php
namespace Plumber\Deployer;

use Plumber\Server\ServerInterface;

abstract class AbstractDeployer implements DeployerInterface
{
    /**
     * {@inheritDoc}
     */
    public function deploy(ServerInterface $server, array $options = array())
    {
        $options = array_merge($options, $server->getOptions());

        $dryRun = false;
        if (isset($options['dry_run']) && $options['dry_run']) {
            $dryRun = true;
        }

        return $this->doDeploy($server, $options, $dryRun);
    }

    /**
     * Do a deploy
     *
     * @param ServerInterface $server  The server
     * @param array           $options The options
     * @param Boolean         $dryRun  Dry run mode
     */
    public abstract function doDeploy(ServerInterface $server, array $options, $dryRun);
}