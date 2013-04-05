<?php

namespace Plum\Tests\Deployer;

use Plum\Deployer\SshDeployer;
use Plum\Server\Server;

class SshDeployerTest extends \PHPUnit_Framework_TestCase
{
	public static $server;

	public static function setUpBeforeClass()
	{
		self::$server = new Server( 'host', 'root', '/var/www', 22, array() );
		self::$server->setPublicKey( '/root/.ssh/id_rsa.pub' );
		self::$server->setPrivateKey( '/root/.ssh/id_rsa' );
	}

	public function testsWithoutCommandsDoesNothing()
	{
		$ssh_connection = $this->getMock( '\Plum\SshConnection' );
		$ssh_connection->expects( $this->never() )->method( 'connect' );
		$ssh_connection->expects( $this->never() )->method( 'authenticate' );
		$ssh_connection->expects( $this->never() )->method( 'execute' );
		$ssh_connection->expects( $this->never() )->method( 'disconnect' );

		$this->deployer = new SshDeployer( $ssh_connection );
		$this->assertFalse( $this->deployer->executeCommands( self::$server, array() ), 'The response is false when no commands have been executed.' );
	}

	public function testExecutingACommand()
	{
		$options 	= array( 'commands' => array( 'ls -lah' ) );

		$ssh_connection = $this->getMock( '\Plum\SshConnection' );
		$ssh_connection->expects( $this->once() )->method( 'connect' );
		$ssh_connection->expects( $this->once() )->method( 'authenticate' )->with( self::$server->getUser(), self::$server->getPublicKey(), self::$server->getPrivateKey() );
		$ssh_connection->expects( $this->at(2) )->method( 'execute' )->with( $this->equalTo( 'cd /var/www/' ) );
		$ssh_connection->expects( $this->at(3) )->method( 'execute' )->with( $this->equalTo( 'ls -lah' ) );
		$ssh_connection->expects( $this->once() )->method( 'disconnect' );

		$this->deployer = new SshDeployer( $ssh_connection );
		$this->assertTrue( $this->deployer->executeCommands( self::$server, $options ), 'Commands must be succesfull' );
	}

	public function testExecutingSeveralCommands()
	{
		$options 	= array(
			'commands' => array( 'ls -lah', 'pwd', 'whoami' )
		);

		$ssh_connection = $this->getMock( '\Plum\SshConnection' );
		$ssh_connection->expects( $this->once() )->method( 'connect' );
		$ssh_connection->expects( $this->once() )->method( 'authenticate' )->with( self::$server->getUser(), self::$server->getPublicKey(), self::$server->getPrivateKey() );
		$ssh_connection->expects( $this->at(2) )->method( 'execute' )->with( $this->equalTo( 'cd /var/www/' ) );
		$ssh_connection->expects( $this->at(3) )->method( 'execute' )->with( $this->equalTo( 'ls -lah' ) );
		$ssh_connection->expects( $this->at(4) )->method( 'execute' )->with( $this->equalTo( 'pwd' ) );
		$ssh_connection->expects( $this->at(5) )->method( 'execute' )->with( $this->equalTo( 'whoami' ) );
		$ssh_connection->expects( $this->once() )->method( 'disconnect' );

		$this->deployer = new SshDeployer( $ssh_connection );
		$this->assertTrue( $this->deployer->executeCommands( self::$server, $options ), 'Commands must be succesfull' );
	}
}