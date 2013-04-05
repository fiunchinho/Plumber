<?php
namespace Plumber\Server;

use Plumber\Exception\SshException;

class SshConnection
{
	public function connect( $host, $port )
	{
		if ( false === $this->connection = ssh2_connect( $host, $port ) ) {
            throw new SshException( sprintf( 'Cannot connect to server "%s"', $host ) );
        }
	}

	public function authenticate( $user, $public_key, $private_key )
	{
		if ( false === ssh2_auth_pubkey_file( $this->connection, $user, $public_key, $private_key ) ) {
            throw new SshException( sprintf( 'Authorization failed for user "%s"', $user ) );
        }
	}

	public function disconnect()
	{
		return ssh2_exec( $this->connection, 'exit' );
	}

	/**
     * Execute a SSH command
     *
     * @param string $cmd The SSH command
     *
     * @return string The output
     */
    public function execute( $cmd )
    {
        if ( false === $stream = ssh2_exec( $this->connection, $cmd ) ) {
            throw new SshException( sprintf( '"%s" : SSH command failed', $cmd ) );
        }

        stream_set_blocking( $stream, true );

        $data = '';
        while ( $buf = fread( $stream, 4096 ) ) {
            $data .= $buf;
        }

        fclose($stream);

        return $data;
    }
}