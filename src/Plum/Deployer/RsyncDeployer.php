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

class RsyncDeployer extends AbstractDeployer
{
    /**
     * {@inheritDoc}
     */
    public function doDeploy(ServerInterface $server, array $options, $dryRun)
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

        if ( true === $dryRun ){
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
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'rsync';
    }

    public function executeCommand()
    {
        return system($command);
    }
}