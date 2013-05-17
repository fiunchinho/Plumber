<?php
namespace Plumber\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;

class PlumberCommand extends Command
{
    protected function getPlumber( $output )
    {
    	$this->config = Yaml::parse( __DIR__ . '/../../../config.yml' );

    	$log = new \Monolog\Logger( 'plumber' );
        $log->pushHandler( new \Plumber\Handler\ConsoleHandler( $output ) );

        $plumber = new \Plumber\Plumber(
            new \Plumber\Deployer\RsyncDeployer,
            new \Symfony\Component\EventDispatcher\EventDispatcher,
            array(
                new \Plumber\ReleaseManager( new \Plumber\Server\SshConnection ),
                new \Plumber\Server\SshCommandExecuter( new \Plumber\Server\SshConnection )
            ),
            $log
        );

        foreach ( $this->config['servers'] as $server_name => $server_config ) {
        	$plumber->addServer( $server_name, $this->buildServer( $server_config ) );
        }

        return $plumber;
    }

    protected function buildServer( $config )
    {
        $server = new \Plumber\Server\Server( $config['host'], $config['user'], $config['to_deploy'] );
        $server->setReleasesFolder( $config['releases_folder'] );
        $server->setPublicKey( $config['pub_key_path'] );
        $server->setPrivateKey( $config['priv_key_path'] );

        return $server;
    }
}