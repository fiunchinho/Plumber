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

    public function __construct( DeployerInterface $deployer, EventDispatcherInterface $dispatcher, array $subscribers = array(), $log )
    {
        $this->deployer     = $deployer;
        $this->dispatcher   = $dispatcher;
        $this->log          = $log;

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
        $options['timestamp_folder']    = ( array_key_exists( 'timestamp_folder', $options ) ) ? $options['timestamp_folder'] : date( 'YmdHis' );

        $this->dispatcher->dispatch(
            'deploy:pre',
            new \Plumber\Event\DeployEvent( $this->servers[$server_name], $options, $this->log )
        );
        try {
            $this->deployer->deploy( $this->servers[$server_name], $options );
            $this->dispatcher->dispatch(
                'deploy:post',
                new \Plumber\Event\DeployEvent( $this->servers[$server_name], $options, $this->log )
            );

            return true;
        } catch ( \Exception $e ) {
            $this->rollback( $server_name );
            $this->log->addError( 'Something went wrong. Rolling back...', array( 'server' => $this->servers[$server_name]->getHost() ) );
            throw $e;
        }
    }

    public function rollback( $server_name, $versions_back = 1 )
    {
        if ( !array_key_exists( $server_name, $this->servers ) ){
            throw new \InvalidArgumentException( 'Unknown server: '. $server_name );
        }

        $server                 = $this->servers[$server_name];
        $releases_names         = $this->getReleasesInServer( $server );
        $current_release_folder = array_pop( $releases_names ); // Remove current version
        $rollback_folder = $this->checkIfReleaseExists( $releases_names, $versions_back );
        $this->changeSymLinkTo( $server, $rollback_folder );
        $this->removeOldRelease( $server, $current_release_folder );

        return $rollback_folder;
    }

    private function getReleasesInServer( $server )
    {
        $ls_command = $server->executeCommand( 'ls ' . $server->getReleasesFolder() );
        $releases_names = explode( "\n", $ls_command );
        array_pop( $releases_names ); // Remove garbage from ls command
        return $releases_names;
    }

    private function checkIfReleaseExists( $releases_names, $versions_back )
    {
        if ( isset( $releases_names[ count($releases_names) - $versions_back ] ) ){
            return $releases_names[ count($releases_names) - $versions_back ];
        }else{
            throw new \InvalidArgumentException( 'You don\'t have that many releases in the server' );
        }
    }

    private function changeSymLinkTo( $server, $rollback_folder )
    {
        $command = sprintf( 'ln -snf %s %s', $server->getReleasesFolder() . $rollback_folder, rtrim( $server->getDir(), '/' ) );
        $server->executeCommand( $command );
    }

    private function removeOldRelease( $server, $current_release_folder )
    {
        $server->executeCommand( 'rm -rf ' . $server->getReleasesFolder() . $current_release_folder );
    }
}