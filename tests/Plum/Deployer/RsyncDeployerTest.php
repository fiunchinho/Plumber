<?php

namespace Plum\Tests\Deployer;
use Plum\Server\Server;

class RsyncDeployerTest extends \PHPUnit_Framework_TestCase
{
	public function deployTestProvider()
	{
		$exclude_file 			= __DIR__ . '/RsyncDeployerTest.php';
		$regular_server 		= new Server( 'localhost', 'julien', '/var/www/', 's3cret', 22, array() );
		$different_path 		= new Server( 'localhost', 'julien', '/var/www/vhosts', 's3cret', 22, array() );
		$different_user			= new Server( 'localhost', 'jose', '/var/www/vhosts', 's3cret', 22, array() );
		$server_different_port 	= new Server( 'localhost', 'julien', '/var/www/vhosts', 's3cret', 50, array() );
		$server_different_host 	= new Server( '123.123.123.123', 'jose', '/var/www/htdocs', 's3cret', 35, array() );
		$server_without_dry_run = new Server( '123.123.123.123', 'jose', '/var/www/htdocs', 's3cret', 35, array( 'dry_run' => false ) );

		return array(
			'Normal deploy' 		=> array( $regular_server, 'rsync -azC --force --delete --progress ./ julien@localhost:/var/www/', array() ),
			'Deploy with dry run' 	=> array( $different_user, 'rsync -azC --force --delete --progress ./ jose@localhost:/var/www/vhosts/ --dryrun', array( 'dry_run' => true ) ),
			'Deploy with excludes' 	=> array( $different_path, 'rsync -azC --force --delete --progress ./ julien@localhost:/var/www/vhosts/ --exclude-from \'' . $exclude_file . '\'', array( 'rsync_exclude' => $exclude_file ) ),
			'Deploy with excludes and dry run' 					=> array( $different_path, 'rsync -azC --force --delete --progress ./ julien@localhost:/var/www/vhosts/ --exclude-from \'' . $exclude_file . '\' --dryrun', array( 'dry_run' => true, 'rsync_exclude' => $exclude_file ) ),
			'Deploy with dry run on different port and path' 	=> array( $server_different_port, 'rsync -azC --force --delete --progress -e "ssh -p50" ./ julien@localhost:/var/www/vhosts/ --dryrun', array( 'dry_run' => true ) ),
			'Deploy with dry run on different port, server, path and username' => array( $server_different_host, 'rsync -azC --force --delete --progress -e "ssh -p35" ./ jose@123.123.123.123:/var/www/htdocs/ --dryrun', array( 'dry_run' => true ) ),
			'Deploy with dry run but server disable it' => array( $server_without_dry_run, 'rsync -azC --force --delete --progress -e "ssh -p35" ./ jose@123.123.123.123:/var/www/htdocs/', array( 'dry_run' => true ) ),
		);
	}

	/**
	 * @dataProvider deployTestProvider
	 */
	public function testRsyncDeploy( $server, $command, $options )
	{
		$deployer 	= $this->getMock( 'Plum\Deployer\RsyncDeployer', array( 'executeCommand' ) );
		$deployer->expects( $this->once() )
			->method( 'executeCommand' )
			->with( $this->equalTo( $command ) )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $deployer->deploy( $server, $options ), 'A succesfull command returns true' );
	}

	public function testRsyncDeployWithNotExistingRsyncExcludeFile()
	{
		$this->setExpectedException( '\InvalidArgumentException' );
		$deployer = new \Plum\Deployer\RsyncDeployer();
		$deployer->deploy( new Server( 'localhost', 'julien', '/var/www/', 's3cret', 22, array() ), array( 'rsync_exclude' => 'not existing file' ) );
	}

	public function testGetDeployerName()
	{
		$deployer = new \Plum\Deployer\RsyncDeployer();
		$this->assertEquals( 'rsync', $deployer->getName(), 'The name must be rsync' );
	}
}