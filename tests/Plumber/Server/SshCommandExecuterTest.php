<?php
namespace Plumber\Tests\Deployer;

use Plumber\Server\SshCommandExecuter;
use Plumber\Server\Server;

class SshCommandExecuterTest extends \PHPUnit_Framework_TestCase
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
		$ssh_connection = $this->getMock( '\Plumber\Server\SshConnection' );
		$ssh_connection->expects( $this->never() )->method( 'connect' );
		$ssh_connection->expects( $this->never() )->method( 'authenticate' );
		$ssh_connection->expects( $this->never() )->method( 'execute' );
		$ssh_connection->expects( $this->never() )->method( 'disconnect' );

		$this->deployer = new SshCommandExecuter( $ssh_connection );
		$this->assertTrue( $this->deployer->execute( self::$server, array() ), 'The response is false when no commands have been executed.' );
	}

	public function testExecutingACommand()
	{
		$commands = array( 'ls -lah' );

		$ssh_connection = $this->getMock( '\Plumber\Server\SshConnection' );
		$ssh_connection->expects( $this->once() )->method( 'connect' );
		$ssh_connection->expects( $this->once() )->method( 'authenticate' )->with( self::$server->getUser(), self::$server->getPublicKey(), self::$server->getPrivateKey() );
		// $ssh_connection->expects( $this->at(2) )->method( 'execute' )->with( $this->equalTo( 'cd /var/www/' ) );
		$ssh_connection->expects( $this->at(2) )->method( 'execute' )->with( $this->equalTo( 'cd /var/www/ && ls -lah' ) );
		$ssh_connection->expects( $this->once() )->method( 'disconnect' );

		$this->deployer = new SshCommandExecuter( $ssh_connection );
		$this->assertTrue( $this->deployer->execute( self::$server, $commands ), 'Commands must be succesfull' );
	}

	public function testExecutingSeveralCommands()
	{
		$commands = array( 'ls -lah', 'pwd', 'whoami' );

		$ssh_connection = $this->getMock( '\Plumber\Server\SshConnection' );
		$ssh_connection->expects( $this->once() )->method( 'connect' );
		$ssh_connection->expects( $this->once() )->method( 'authenticate' )->with( self::$server->getUser(), self::$server->getPublicKey(), self::$server->getPrivateKey() );
		// $ssh_connection->expects( $this->at(2) )->method( 'execute' )->with( $this->equalTo( 'cd /var/www/' ) );
		$ssh_connection->expects( $this->at(2) )->method( 'execute' )->with( $this->equalTo( 'cd /var/www/ && ls -lah' ) );
		$ssh_connection->expects( $this->at(3) )->method( 'execute' )->with( $this->equalTo( 'cd /var/www/ && pwd' ) );
		$ssh_connection->expects( $this->at(4) )->method( 'execute' )->with( $this->equalTo( 'cd /var/www/ && whoami' ) );
		$ssh_connection->expects( $this->once() )->method( 'disconnect' );

		$this->deployer = new SshCommandExecuter( $ssh_connection );
		$this->assertTrue( $this->deployer->execute( self::$server, $commands ), 'Commands must be succesfull' );
	}
}