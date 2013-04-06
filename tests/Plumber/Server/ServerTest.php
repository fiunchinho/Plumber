<?php
namespace Plumber\Tests\Server;

use Plumber\Server\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testServerArg()
    {
        $server = new Server('localhost', 'julien', '/var/www/', 1234 );

        $this->assertEquals('localhost', $server->getHost());
        $this->assertEquals('julien', $server->getUser());
        $this->assertEquals('/var/www/', $server->getDir());
        $this->assertEquals(1234, $server->getPort());
        $server->setPassword( $password = 'password' );
        $this->assertEquals( $password, $server->getPassword() );
        $server->setPublicKey( $public_key = 'public' );
        $this->assertEquals( $public_key, $server->getPublicKey() );
        $server->setPrivateKey( $private_key = 'private' );
        $this->assertEquals( $private_key, $server->getPrivateKey() );
    }

    public function testServerDefaultArg()
    {
        $server = new Server('localhost', 'julien', '/home/julien/website');

        $this->assertNull($server->getPassword());
        $this->assertEquals(22, $server->getPort());
    }

    public function testServerPath()
    {
        $s1 = new Server('localhost', 'julien', '/home/julien/website');
        $s2 = new Server('localhost', 'julien', '/home/julien/website/');

        $this->assertEquals('/home/julien/website/', $s1->getDir());
        $this->assertEquals('/home/julien/website/', $s2->getDir());
    }
}
