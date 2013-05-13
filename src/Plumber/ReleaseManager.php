<?php
namespace Plumber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReleaseManager implements EventSubscriberInterface
{
	/**
     *
     * @param SshConnection $ssh The interface to talk SSH to a remote server
     */
    public function __construct( \Plumber\Server\SshConnection $ssh )
    {
        $this->ssh = $ssh;
    }

    public function release( \Plumber\Event\DeployEvent $event )
    {
    	$options 		    = $event->getOptions();
    	$timestamp_folder   = $options['timestamp_folder'];
    	$server 		    = $event->getServer();
    	$command 		    = sprintf( 'ln -s %s %s', $server->getReleasesFolder() . $timestamp_folder, rtrim( $server->getDir(), '/' ) );
    	if ( !empty( $options['dry_run'] ) ){
            return true;
        }
    	$this->ssh->connect( $server->getHost(), $server->getPort() );
        $this->ssh->authenticate( $server->getUser(), $server->getPublicKey(), $server->getPrivateKey() );

        $this->ssh->execute( 'rm -rf ' . rtrim( $server->getDir(), '/' ) );
        $this->ssh->execute( $command );
        $this->ssh->disconnect();

        return true;
    }

    public function prepareFolders( \Plumber\Event\DeployEvent $event )
    {
        $server     = $event->getServer();
        $options    = $event->getOptions();

        if ( $options['dry_run'] ){
            return true;
        }

        $this->ssh->connect( $server->getHost(), $server->getPort() );
        $this->ssh->authenticate( $server->getUser(), $server->getPublicKey(), $server->getPrivateKey() );

        // if( !is_dir( $server->getReleasesFolder() ) ){
        //     $this->ssh->execute( 'mkdir -p ' . $server->getReleasesFolder() );
        // }

        $this->ssh->execute( 'mkdir -p ' . $server->getReleasesFolder() . $options['timestamp_folder'] );

        $this->ssh->disconnect();

        return true;
    }

    public function cleanOldReleases( \Plumber\Event\DeployEvent $event )
    {
        $options    = $event->getOptions();
        $server     = $event->getServer();

        $this->ssh->connect( $server->getHost(), $server->getPort() );
        $this->ssh->authenticate( $server->getUser(), $server->getPublicKey(), $server->getPrivateKey() );
        $this->removeOldReleases( $server, $options['releases_to_keep'] );
        $this->ssh->disconnect();
    }

    protected function removeOldReleases( $server, $releases_to_keep )
    {
        $releases_names = $this->getReleasesInFolder( $server );
        for ( $i = 0; $i < count($releases_names) - $releases_to_keep; $i++ ) {
            $this->ssh->execute( 'rm -rf ' . $server->getReleasesFolder() . $releases_names[$i] );
        }

        return true;
    }

    protected function getReleasesInFolder( $server )
    {
        $ls_command = $this->ssh->execute( 'ls ' . $server->getReleasesFolder() );
        $releases_names = explode( "\n", $ls_command );
        array_pop( $releases_names );

        return $releases_names;
    }

	/**
	 * Methods to be executed.
	 * @return array Relation between events and methods
	 */
	public static function getSubscribedEvents()
    {
        return array(
            'deploy:pre'    => 'prepareFolders',
            'deploy:post'   => array( array( 'release', 10 ), array( 'cleanOldReleases', 5 ) )
        );
    }
}