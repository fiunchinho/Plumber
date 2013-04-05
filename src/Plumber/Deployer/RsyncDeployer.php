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
        $command = 'rsync ';
        $command .= isset( $options['rsync_options'] ) ? $options['rsync_options'] : '-azC --force --delete --progress';

        if ( 22 !== $server->getPort() ){
            $command .= ' ' . sprintf( '-e "ssh -p%d"', $server->getPort() );
        }

        $command .= ' ./ ' . sprintf( '%s@%s:%s', $server->getUser(), $server->getHost(), $server->getDir() );

        if ( isset( $options['rsync_exclude'] ) ){
            $command .= ' ' . sprintf( '--exclude-from \'%s\'', $this->getExcludeFile( $options['rsync_exclude'] ) );
        }

        if ( array_key_exists( 'dry_run', $options ) && ( true === $options['dry_run'] ) ){
            $command .= ' --dryrun';
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
    public function executeCommand()
    {
        return system($command);
    }
}