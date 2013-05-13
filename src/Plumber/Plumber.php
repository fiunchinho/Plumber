<?php
namespace Plumber;

use Plumber\Deployer\DeployerInterface;
use Plumber\Server\ServerInterface;
use Plumber\Server\SshCommandExecuter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Plumber
{
    /**
     * Default value to use for the rsync dry_run parameter
     * @var boolean
     */
    const DEFAULT_DRY_RUN = false;

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

    public function __construct( DeployerInterface $deployer, EventDispatcherInterface $dispatcher, array $subscribers = array() )
    {
        $this->deployer     = $deployer;
        $this->dispatcher   = $dispatcher;

        foreach ( $subscribers as $subscriber ) {
            $this->dispatcher->addSubscriber( $subscriber );
        }
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

        $options['commands']            = ( array_key_exists( 'commands', $options ) ) ? $options['commands'] : array();
        $options['dry_run']             = ( array_key_exists( 'dry_run', $options ) ) ? $options['dry_run'] : self::DEFAULT_DRY_RUN;
        $options['timestamp_folder']    = ( array_key_exists( 'timestamp_folder', $options ) ) ? $options['timestamp_folder'] : date( 'YmdHis' );
        // $options['rollback_folder'] = readlink( $this->servers[$server_name]->getDir() );

        $this->dispatcher->dispatch(
            'deploy:pre',
            new \Plumber\Event\DeployEvent( $this->servers[$server_name], $options )
        );
        $this->deployer->deploy( $this->servers[$server_name], $options );
        $this->dispatcher->dispatch(
            'deploy:post',
            new \Plumber\Event\DeployEvent( $this->servers[$server_name], $options )
        );

        return true;
    }
}