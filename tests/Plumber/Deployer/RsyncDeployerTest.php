<?php
namespace Plumber\Tests\Deployer;

use Plumber\Server\Server;

class RsyncDeployerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test the deployer using different combinations.
	 */
	public function deployTestProvider()
	{
		$exclude_file 			= __DIR__ . '/RsyncDeployerTest.php';
		$regular_server 		= new Server( 'localhost', 'julien', '/var/www/', 22 );
		$different_path 		= new Server( 'localhost', 'julien', '/var/www/vhosts', 22 );
		$different_user			= new Server( 'localhost', 'jose', '/var/www/vhosts', 22 );
		$server_different_port 	= new Server( 'localhost', 'julien', '/var/www/vhosts', 50 );
		$server_different_host 	= new Server( '123.123.123.123', 'jose', '/var/www/htdocs', 35 );

		return array(
			'Normal deploy' 		=> array( $regular_server, 'rsync -azC --force --delete --progress ./ julien@localhost:/var/www/', array() ),
			'Deploy with dry run' 	=> array( $different_user, 'rsync -azC --force --delete --progress ./ jose@localhost:/var/www/vhosts/ --dry-run', array( 'dry_run' => true ) ),
			'Deploy with excludes' 	=> array( $different_path, 'rsync -azC --force --delete --progress ./ julien@localhost:/var/www/vhosts/ --exclude-from \'' . $exclude_file . '\'', array( 'rsync_exclude' => $exclude_file ) ),
			'Deploy with excludes and dry run' 					=> array( $different_path, 'rsync -azC --force --delete --progress ./ julien@localhost:/var/www/vhosts/ --exclude-from \'' . $exclude_file . '\' --dry-run', array( 'dry_run' => true, 'rsync_exclude' => $exclude_file ) ),
			'Deploy with dry run on different port and path' 	=> array( $server_different_port, 'rsync -azC --force --delete --progress -e "ssh -p50" ./ julien@localhost:/var/www/vhosts/ --dry-run', array( 'dry_run' => true ) ),
			'Deploy with dry run on different port, server, path and username' => array( $server_different_host, 'rsync -azC --force --delete --progress -e "ssh -p35" ./ jose@123.123.123.123:/var/www/htdocs/ --dry-run', array( 'dry_run' => true ) ),
		);
	}

	/**
	 * @dataProvider deployTestProvider
	 */
	public function testRsyncDeploy( $server, $command, $options )
	{
		$deployer 	= $this->getMock( 'Plumber\Deployer\RsyncDeployer', array( 'executeCommand' ) );
		$deployer->expects( $this->once() )
			->method( 'executeCommand' )
			->with( $this->equalTo( $command ) )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $deployer->deploy( $server, $options ), 'A succesfull command returns true' );
	}

	public function testRsyncDeployWithNotExistingRsyncExcludeFile()
	{
		$this->setExpectedException( '\InvalidArgumentException' );
		$server 	= new Server( 'localhost', 'julien', '/var/www/', 22 );
		$deployer 	= new \Plumber\Deployer\RsyncDeployer();
		$deployer->deploy( $server, array( 'rsync_exclude' => 'not existing file' ) );
	}

	public function testGetDeployerName()
	{
		$deployer = new \Plumber\Deployer\RsyncDeployer();
		$this->assertEquals( 'rsync', $deployer->getName(), 'The name must be rsync' );
	}
}