<?php
namespace Plumber\Server;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SshCommandExecuter implements EventSubscriberInterface
{

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
        $log        = $event->getLogger();

        $commands   = $options['commands'];
        if ( !empty( $options['dry_run'] ) ){
            return true;
        }

        foreach ( $commands as $command ) {
            $log->addNotice(
                "\nExecuting the following command:\n$command\n" . $server->executeCommand( $command ),
                array( 'server' => $server->getHost() )
            );
        }

        return true;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'deploy:post'   => array( array( 'execute', 15 ) )
        );
    }
}