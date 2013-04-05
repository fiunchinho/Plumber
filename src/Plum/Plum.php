<?php

/*
 * This file is part of the Plum package.
 *
 * (c) 2010-2012 Julien Brochet <mewt@madalynn.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plum;

use Plum\Deployer\DeployerInterface;
use Plum\Server\ServerInterface;

class Plum
{
    /**
     * @var array
     */
    protected $servers = array();

    /**
     * Interface to run SSH commands
     * @var object
     */
    protected $ssh;

    /**
     * The tool to deploy the code
     * @var DeployerInterface
     */
    protected $deployer = array();

    public function __construct( DeployerInterface $deployer, $ssh_commands )
    {
        $this->deployer = $deployer;
        $this->ssh      = $ssh_commands;
    }

    /**
     * Adds a server to the list
     *
     * @param string $name
     * @param \Plum\Server\ServerInterface $server
     *
     * @return \Plum\Plum
     */
    public function addServer($name, ServerInterface $server)
    {
        $this->servers[$name] = $server;
    }

    /**
     * Deploys to the server using the deployer
     *
     * @param string $server_name   The name of the server
     */
    public function deploy( $server_name )
    {
        if ( !array_key_exists( $server_name, $this->servers ) ){
            throw new \InvalidArgumentException( 'Unknown server: '. $server_name );
        }

        return
            $this->deployer->deploy( $this->servers[$server_name], array() )
                && $this->ssh->executeCommands( $this->servers[$server_name], array() );
    }
}