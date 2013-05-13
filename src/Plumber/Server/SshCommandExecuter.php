<?php
namespace Plumber\Server;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SshCommandExecuter implements EventSubscriberInterface
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
    public function execute( \Plumber\Event\DeployEvent $event )
    {
        $server     = $event->getServer();
        $options    = $event->getOptions();
        $commands   = $options['commands'];
        if ( ( !empty( $options['dry_run'] ) ) || ( 0 === count( $commands ) ) ){
            return true;
        }

        $this->ssh->connect( $server->getHost(), $server->getPort() );
        $this->ssh->authenticate( $server->getUser(), $server->getPublicKey(), $server->getPrivateKey() );

        $this->executeCommands( $commands, $server->getDir() );
        $this->ssh->disconnect();

        return true;
    }

    /**
     * Execute the commands in the remote server via SSH.
     *
     * @param array $commands The commands to execute
     * @param string $path Path where to execute the commands
     */
    protected function executeCommands( array $commands, $path )
    {
        foreach ( $commands as $command ) {
            echo "\nExecuting... " . 'cd ' . $path . ' && ' . $command . "\n";
            $this->ssh->execute( 'cd ' . $path . ' && ' . $command );
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            'deploy:post' => 'execute'
        );
    }
}