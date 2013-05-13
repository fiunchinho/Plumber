<?php
namespace Plumber\Tests;

use Plumber\Server\Server;
use Plumber\Plumber;

class PlumberTest extends \PHPUnit_Framework_TestCase
{
	public static $server;

	public function testDeployProdServerWhenOnlyProdIsPresent()
	{
		$server 	= new Server( 'host', 'root', '/var/www', 22 );
		$options 	= array( 'commands' => array(), 'dry_run' => false, 'timestamp_folder' => date( 'YmdHis' ) );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $server, $options )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $this->getMock( '\Symfony\Component\EventDispatcher\EventDispatcher' ) );
		$plum->addServer( 'prod', $server );
		$this->assertTrue( $plum->deploy( 'prod', $options ), 'Deploy must be successfull' );
	}

	public function testDeployProdServerWhenThereAreMoreServers()
	{
		$prod_server 	= new Server( 'prod', 'root', '/var/www', 22 );
		$dev_server 	= new Server( 'dev', 'root', '/var/www', 22 );
		$options 		= array( 'commands' => array(), 'dry_run' => false, 'timestamp_folder' => date( 'YmdHis' ) );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $prod_server, $options )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $this->getMock( '\Symfony\Component\EventDispatcher\EventDispatcher' ) );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'prod', array() ), 'Deploy must be successfull' );
	}

	public function testDeployDevServerWhenThereAreMoreServers()
	{
		$prod_server 	= new Server( 'prod', 'root', '/var/www', 22 );
		$dev_server 	= new Server( 'dev', 'root', '/var/www', 22 );
		$options 		= array( 'commands' => array(), 'dry_run' => false, 'timestamp_folder' => date( 'YmdHis' ) );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $dev_server, $options )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $this->getMock( '\Symfony\Component\EventDispatcher\EventDispatcher' ) );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'dev', array() ), 'Deploy must be successfull' );
	}

	public function testDeployServerNotPresent()
	{
		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->never() )->method( 'deploy' );

		$plum = new Plumber( $deployer, $this->getMock( '\Symfony\Component\EventDispatcher\EventDispatcher' ) );
		$plum->addServer( 'prod', new Server( 'host', 'root', '/var/www', 22 ) );
		$this->setExpectedException( '\InvalidArgumentException' );
		$this->assertTrue( $plum->deploy( 'dev', array() ), 'Deploy must be successfull' );
	}

	public function testEventsAreDispatched()
	{
		$deployer 	= $this->getMock( '\Plumber\Deployer\NoRsyncDeployer' );

		$dispatcher = $this->getMock( '\Symfony\Component\EventDispatcher\EventDispatcher' );
		$dispatcher->expects( $this->exactly(2) )->method( 'dispatch' );

		$plum = new Plumber( $deployer, $dispatcher );
		$plum->addServer( 'prod', new Server( 'host', 'root', '/var/www', 22 ) );
		$plum->deploy( 'prod' );
	}

	public function testDeployWithSubscribers()
	{
		$server 	= new Server( '91.121.5.9', 'root', '/var/www/plumbertest', 22 );
		$server->setReleasesFolder( '/var/www/releases' );

		$deployer 	= $this->getMock( '\Plumber\Deployer\NoRsyncDeployer' );
		$dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher;
		$ssh_con 	= $this->getMock( '\Plumber\Server\SshConnection' );
		$ssh_con->expects( $this->once() )->method( 'execute' )->with( sprintf( 'ln -sf %s %s', $server->getReleasesFolder() . date( 'YmdHis' ) . '/', $server->getDir() ) );

		$plum = new Plumber( $deployer, $dispatcher, array( new \Plumber\ReleaseManager( $ssh_con ) ) );
		$plum->addServer( 'prod', $server );
		$plum->deploy( 'prod', array( 'dry_run' => false ) );
	}
}