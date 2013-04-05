<?php

/*
 * This file is part of the Plum package.
 *
 * (c) 2010-2012 Julien Brochet <mewt@madalynn.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plum\Deployer;

use Plum\Server\ServerInterface;

class SshDeployer
{
    /**
     * The SSH connection
     *
     * @var ressource
     */
    protected $con;

    public function __construct( \Plum\SshConnection $ssh )
    {
        $this->ssh = $ssh;
    }

    /**
     * {@inheritDoc}
     */
    public function executeCommands( ServerInterface $server, array $options, $dry_run = true )
    {
        $this->server = $server;
        $commands = isset($options['commands']) ? $options['commands'] : array();
        if (0 === count($commands)) {
            return false; // The SSH deployer is useless if the user has no command
        }

        $this->ssh->connect( $server->getHost(), $server->getPort() );
        $this->ssh->authenticate( $server->getUser(), $server->getPublicKey(), $server->getPrivateKey() );

        $this->ssh->execute( 'cd ' . $server->getDir() );
        foreach ( $commands as $command ) {
            $this->ssh->execute( $command );
        }

        $this->ssh->disconnect();
        return true;
    }
}