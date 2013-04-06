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

		$commands 	= array( 'pwd' );
		$options 	= array( 'commands' => $commands );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $server, $options )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'execute' )->with( $server, $commands )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $server );
		$this->assertTrue( $plum->deploy( 'prod', $options ), 'Deploy must be successfull' );
	}

	public function testDeployProdServerWhenThereAreMoreServers()
	{
		$prod_server 	= new Server( 'prod', 'root', '/var/www', 22 );
		$dev_server 	= new Server( 'dev', 'root', '/var/www', 22 );

		$commands 		= array( 'pwd', 'ls', 'whoami' );
		$options 		= array( 'commands' => $commands );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $prod_server, $options )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'execute' )->with( $prod_server, $commands )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'prod', array( 'commands' => $commands ) ), 'Deploy must be successfull' );
	}

	public function testDeployDevServerWhenThereAreMoreServers()
	{
		$prod_server 	= new Server( 'prod', 'root', '/var/www', 22 );
		$dev_server 	= new Server( 'dev', 'root', '/var/www', 22 );

		$commands 		= array();
		$options 		= array( 'commands' => $commands );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $dev_server, $options )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'execute' )->with( $dev_server, $commands )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'dev', $options ), 'Deploy must be successfull' );
	}

	public function testDeployProdServerUsingDryRun()
	{
		$prod_server 	= new Server( 'prod', 'root', '/var/www', 22 );
		$dev_server 	= new Server( 'dev', 'root', '/var/www', 22 );

		$commands 		= array( 'pwd', 'ls', 'whoami' );
		$options 		= array( 'dry_run' => true, 'commands' => $commands );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $prod_server, $options )->will( $this->returnValue( false ) );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->never() )->method( 'execute' );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'prod', $options ), 'Deploy must be successfull' );
	}

	public function testDeployServerNotPresent()
	{
		$this->setExpectedException( '\InvalidArgumentException' );

		$server = new Server( 'host', 'root', '/var/www', 22 );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->never() )->method( 'deploy' );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->never() )->method( 'execute' );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $server );
		$this->assertTrue( $plum->deploy( 'dev', array() ), 'Deploy must be successfull' );
	}

	public function testDeployUsingOnlySshCommands()
	{
		$server = new Server( 'host', 'root', '/var/www', 22 );

		$commands = array( 'pwd' );

		$deployer = $this->getMock( '\Plumber\Deployer\NoRsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'execute' )->with( $server, $commands )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $server );
		$this->assertTrue( $plum->deploy( 'prod', array( 'commands' => $commands ) ), 'Deploy must be successfull' );
	}
}