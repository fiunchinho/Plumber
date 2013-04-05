<?php
namespace Plumber\Server;

class SshCommandExecuter
{
    /**
     * The SSH connection
     *
     * @var ressource
     */
    protected $con;

    /**
     *
     * @param SshConnection $ssh The interface to talk SSH to a remote server
     */
    public function __construct( SshConnection $ssh )
    {
        $this->ssh = $ssh;
    }

    /**
     * Connect through SSH to a remote server and execute some commands.
     *
     * @param ServerInterface $server The server where to execute the commands
     * @param array $commands The commands to execute
     * @return boolean 
     */
    public function execute( ServerInterface $server, array $commands = array() )
    {
        if ( 0 === count( $commands ) ) {
            return true;
        }

        $this->ssh->connect( $server->getHost(), $server->getPort() );
        $this->ssh->authenticate( $server->getUser(), $server->getPublicKey(), $server->getPrivateKey() );

        $this->ssh->execute( 'cd ' . $server->getDir() ); // Move to the right path
        $this->executeCommands( $commands );
        $this->ssh->disconnect();

        return true;
    }

    /**
     * Execute the commands in the remote server via SSH.
     *
     * @param array $commands The commands to execute
     */
    protected function executeCommands( array $commands )
    {
        foreach ( $commands as $command ) {
            $this->ssh->execute( $command );
        }
    }
}