<?php
namespace Plumber\Tests\Deployer;

use Plumber\Server\Server;

class NoRsyncDeployerTest extends \PHPUnit_Framework_TestCase
{
	public function testRsyncDeployWithNotExistingRsyncExcludeFile()
	{
		$server 	= new Server( 'localhost', 'julien', '/var/www/', 22 );
		$deployer 	= new \Plumber\Deployer\NoRsyncDeployer();
		$this->assertTrue( $deployer->deploy( $server, array() ), 'This deployer always returns true.' );
	}

	public function testGetDeployerName()
	{
		$deployer = new \Plumber\Deployer\NoRsyncDeployer();
		$this->assertEquals( 'no rsync', $deployer->getName(), 'The name must be no rsync' );
	}
}