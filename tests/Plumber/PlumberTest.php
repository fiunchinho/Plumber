<?php
namespace Plumber\Tests;

use Plumber\Server\Server;
use Plumber\Plumber;

class PlumberTest extends \PHPUnit_Framework_TestCase
{
	public static $server;

	public function testDeployProdServerWhenOnlyProdIsPresent()
	{
		$commands = array( 'pwd' );
		$server = new Server( 'host', 'root', '/var/www', 22, array() );
		$server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$server->setPrivateKey( '/root/.ssh/id_rsa' );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $server, array() )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'execute' )->with( $server, $commands )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $server );
		$this->assertTrue( $plum->deploy( 'prod', array( 'commands' => $commands ) ), 'Deploy must be successfull' );
	}

	public function testDeployProdServerWhenThereAreMoreServers()
	{
		$commands = array( 'pwd', 'ls', 'whoami' );
		$prod_server = new Server( 'prod', 'root', '/var/www', 22, array() );
		$prod_server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$prod_server->setPrivateKey( '/root/.ssh/id_rsa' );

		$dev_server = new Server( 'dev', 'root', '/var/www', 22, array() );
		$dev_server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$dev_server->setPrivateKey( '/root/.ssh/id_rsa' );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $prod_server, array() )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'execute' )->with( $prod_server, $commands )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'prod', array( 'commands' => $commands ) ), 'Deploy must be successfull' );
	}

	public function testDeployDevServerWhenThereAreMoreServers()
	{
		$commands = array();
		$prod_server = new Server( 'prod', 'root', '/var/www', 22, array() );
		$prod_server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$prod_server->setPrivateKey( '/root/.ssh/id_rsa' );

		$dev_server = new Server( 'dev', 'root', '/var/www', 22, array() );
		$dev_server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$dev_server->setPrivateKey( '/root/.ssh/id_rsa' );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $dev_server, array() )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'execute' )->with( $dev_server, $commands )->will( $this->returnValue( true ) );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'dev', array( 'commands' => $commands ) ), 'Deploy must be successfull' );
	}

	public function testDeployServerNotPresent()
	{
		$this->setExpectedException( '\InvalidArgumentException' );

		$server = new Server( 'host', 'root', '/var/www', 22, array() );

		$deployer = $this->getMock( '\Plumber\Deployer\RsyncDeployer' );
		$deployer->expects( $this->never() )->method( 'deploy' );

		$ssh = $this->getMock( '\Plumber\Server\SshCommandExecuter', array(), array( $this->getMock( '\Plumber\Server\SshConnection' ) ) );
		$ssh->expects( $this->never() )->method( 'execute' );

		$plum = new Plumber( $deployer, $ssh );
		$plum->addServer( 'prod', $server );
		$this->assertTrue( $plum->deploy( 'dev', array() ), 'Deploy must be successfull' );
	}
}