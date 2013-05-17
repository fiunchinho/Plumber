<?php
namespace Plumber\Server;

use Plumber\Exception\SshException;

class SshConnection
{
    protected $connection;
    protected $authenticated = false;

	public function connect( $host, $port )
	{
        if ( !$this->connection ){
            if ( false === $this->connection = ssh2_connect( $host, $port ) ) {
                throw new SshException( sprintf( 'Cannot connect to server "%s"', $host ) );
            }
        }
	}

	public function authenticate( $user, $public_key, $private_key )
	{
        if ( !$this->authenticated ){
            if ( false === $this->authenticated = ssh2_auth_pubkey_file( $this->connection, $user, $public_key, $private_key ) ) {
                throw new SshException( sprintf( 'Authorization failed for user "%s"', $user ) );
            }
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
        // if ( false === $stream = ssh2_exec( $this->connection, $cmd ) ) {
        //     throw new SshException( sprintf( '"%s" : SSH command failed', $cmd ) );
        // }

        $stdout = ssh2_exec( $this->connection, $cmd );
        $stderr = ssh2_fetch_stream( $stdout, SSH2_STREAM_STDERR );
        stream_set_blocking( $stderr, true );
        stream_set_blocking( $stdout, true );
        $error = stream_get_contents( $stderr );
        if ($error !== '') {
            throw new \RuntimeException( $error );
        }
        return stream_get_contents( $stdout );
    }
}