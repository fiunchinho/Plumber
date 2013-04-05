<?php
namespace Plumber;

use Plumber\Deployer\DeployerInterface;
use Plumber\Server\ServerInterface;
use Plumber\Server\SshCommandExecuter;

class Plumber
{
    /**
     * @var array
     */
    protected $servers = array();

    /**
     * Interface to run SSH commands
     * @var SshCommandExecuter
     */
    protected $ssh;

    /**
     * The tool to deploy the code
     * @var DeployerInterface
     */
    protected $deployer = array();

    public function __construct( DeployerInterface $deployer, SshCommandExecuter $ssh_commands )
    {
        $this->deployer = $deployer;
        $this->ssh      = $ssh_commands;
    }

    /**
     * Adds a server to the list
     *
     * @param string $name
     * @param \Plumber\Server\ServerInterface $server
     *
     */
    public function addServer( $name, ServerInterface $server )
    {
        $this->servers[$name] = $server;
    }

    /**
     * Deploys to the server using the deployer
     *
     * @param string $server_name   The name of the server
     */
    public function deploy( $server_name, array $options = array() )
    {
        if ( !array_key_exists( $server_name, $this->servers ) ){
            throw new \InvalidArgumentException( 'Unknown server: '. $server_name );
        }

        $commands = ( array_key_exists( 'commands', $options ) ) ? $options['commands'] : array();

        return
            $this->deployer->deploy( $this->servers[$server_name], array() )
                && $this->ssh->execute( $this->servers[$server_name], $commands );
    }
}