<?php
namespace Plumber\Deployer;

use Plumber\Server\ServerInterface;

class RsyncDeployer implements DeployerInterface
{
    /**
     * Do a deploy
     *
     * @param ServerInterface $server  The server
     * @param array           $options The options
     */
    public function deploy( ServerInterface $server, array $options = array() )
    {
        $cache_folder = '/var/www/plumber/cache/';
        if ( !is_dir( $cache_folder ) ){
            $this->executeCommand( 'mkdir -p ' . $cache_folder );
            $this->executeCommand( 'git clone https://github.com/fiunchinho/Plumber.git ' . $cache_folder );
        }else{
            $this->executeCommand( 'cd ' . $cache_folder . ' && git pull' );
        }

        $command = 'rsync ';
        $command .= isset( $options['rsync_options'] ) ? $options['rsync_options'] : '-azC --force --delete --progress';

        if ( 22 !== $server->getPort() ){
            $command .= ' ' . sprintf( '-e "ssh -p%d"', $server->getPort() );
        }

        $command .= ' ' . $cache_folder . ' ' . sprintf( '%s@%s:%s', $server->getUser(), $server->getHost(), $server->getReleasesFolder() . $options['timestamp_folder'] );

        if ( isset( $options['rsync_exclude'] ) ){
            $command .= ' ' . sprintf( '--exclude-from \'%s\'', $this->getExcludeFile( $options['rsync_exclude'] ) );
        }

        if ( array_key_exists( 'dry_run', $options ) && ( true === $options['dry_run'] ) ){
            $command .= ' --dry-run';
        }

        return $this->executeCommand( $command );
    }

    
    protected function getExcludeFile( $exclude_file )
    {
        if ( false === file_exists( $exclude_file ) ) {
            throw new \InvalidArgumentException( sprintf( 'The exclude file "%s" does not exist.', $exclude_file ) );
        }

        return realpath( $exclude_file );
    }

    /**
     * @return string The deployer identifier
     */
    public function getName()
    {
        return 'rsync';
    }

    /**
     * Method to execute a command in the system. We need it to execute rsycn in the operating system.
     */
    public function executeCommand( $command )
    {
        return system($command);
    }
}