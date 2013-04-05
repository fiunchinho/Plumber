<?php

namespace Plum\Tests;

use Plum\Deployer\RsyncDeployer;
use Plum\Deployer\SshDeployer;
use Plum\Server\Server;

class PlumTest extends \PHPUnit_Framework_TestCase
{
	public static $server;

	public function testDeployProdServerWhenOnlyProdIsPresent()
	{
		$server = new Server( 'host', 'root', '/var/www', 22, array() );
		$server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$server->setPrivateKey( '/root/.ssh/id_rsa' );

		$deployer = $this->getMock( '\Plum\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $server, array() )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plum\Deployer\SshDeployer', array(), array( $this->getMock( '\Plum\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'executeCommands' )->with( $server, array() )->will( $this->returnValue( true ) );

		$plum = new \Plum\Plum( $deployer, $ssh );
		$plum->addServer( 'prod', $server );
		$this->assertTrue( $plum->deploy( 'prod', $deployer ), 'Deploy must be successfull' );
	}

	public function testDeployProdServerWhenThereAreMoreServers()
	{
		$prod_server = new Server( 'prod', 'root', '/var/www', 22, array() );
		$prod_server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$prod_server->setPrivateKey( '/root/.ssh/id_rsa' );

		$dev_server = new Server( 'dev', 'root', '/var/www', 22, array() );
		$dev_server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$dev_server->setPrivateKey( '/root/.ssh/id_rsa' );

		$deployer = $this->getMock( '\Plum\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $prod_server, array() )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plum\Deployer\SshDeployer', array(), array( $this->getMock( '\Plum\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'executeCommands' )->with( $prod_server, array() )->will( $this->returnValue( true ) );

		$plum = new \Plum\Plum( $deployer, $ssh );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'prod', $deployer ), 'Deploy must be successfull' );
	}

	public function testDeployDevServerWhenThereAreMoreServers()
	{
		$prod_server = new Server( 'prod', 'root', '/var/www', 22, array() );
		$prod_server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$prod_server->setPrivateKey( '/root/.ssh/id_rsa' );

		$dev_server = new Server( 'dev', 'root', '/var/www', 22, array() );
		$dev_server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		$dev_server->setPrivateKey( '/root/.ssh/id_rsa' );

		$deployer = $this->getMock( '\Plum\Deployer\RsyncDeployer' );
		$deployer->expects( $this->once() )->method( 'deploy' )->with( $dev_server, array() )->will( $this->returnValue( true ) );

		$ssh = $this->getMock( '\Plum\Deployer\SshDeployer', array(), array( $this->getMock( '\Plum\SshConnection' ) ) );
		$ssh->expects( $this->once() )->method( 'executeCommands' )->with( $dev_server, array() )->will( $this->returnValue( true ) );

		$plum = new \Plum\Plum( $deployer, $ssh );
		$plum->addServer( 'prod', $prod_server );
		$plum->addServer( 'dev', $dev_server );
		$this->assertTrue( $plum->deploy( 'dev', $deployer ), 'Deploy must be successfull' );
	}

	public function testDeployServerNotPresent()
	{
		$this->setExpectedException( '\InvalidArgumentException' );

		$server = new Server( 'host', 'root', '/var/www', 22, array() );

		$deployer = $this->getMock( '\Plum\Deployer\RsyncDeployer' );
		$deployer->expects( $this->never() )->method( 'deploy' );

		$ssh = $this->getMock( '\Plum\Deployer\SshDeployer', array(), array( $this->getMock( '\Plum\SshConnection' ) ) );
		$ssh->expects( $this->never() )->method( 'executeCommands' );

		$plum = new \Plum\Plum( $deployer, $ssh );
		$plum->addServer( 'prod', $server );
		$this->assertTrue( $plum->deploy( 'dev', $deployer ), 'Deploy must be successfull' );
	}
}