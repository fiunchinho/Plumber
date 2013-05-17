<?php
namespace Plumber\Server;

class Server implements ServerInterface
{
    /**
     * The connection hostname
     *
     * @var string
     */
    protected $host;

    /**
     * The connection port
     *
     * @var string
     */
    protected $port;

    /**
     * The connection username
     *
     * @var string
     */
    protected $user;

    /**
     * The connection directory
     *
     * @var string
     */
    protected $dir;

    /**
     * The connection password
     *
     * @var string
     */
    protected $password;

    /**
     * The path to the public key for authentication
     *
     * @var string
     */
    protected $public_key;

    /**
     * The path to the private key for authentication
     *
     * @var string
     */
    protected $private_key;

    /**
     * A list of options
     *
     * @var array
     */
    protected $options;

    /**
     * 
     * @var string
     */
    protected $releases_folder;

    /**
     * The SSH connection
     *
     * @var ressource
     */
    protected $ssh;

    public function __construct($host, $user, $dir, $port = 22 )
    {
        if ('/' !== substr($dir, -1)) {
            $dir .= '/';
        }

        $this->host     = $host;
        $this->user     = $user;
        $this->port     = $port;
        $this->dir      = $dir;
        $this->ssh      = new SshConnection;
    }

    public function getPublicKey()
    {
        return $this->public_key;
    }

    public function setPublicKey( $path )
    {
        $this->public_key = $path;
    }

    public function getPrivateKey()
    {
        return $this->private_key;
    }

    public function setPrivateKey( $path )
    {
        $this->private_key = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritDoc}
     */
    public function getDir()
    {
        return $this->dir;
    }

    public function getReleasesFolder()
    {
        return $this->releases_folder;
    }

    public function setReleasesFolder( $folder )
    {
        $this->releases_folder = rtrim( $folder, '/' ) . '/';
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword( $password )
    {
        $this->password = $password;
    }

    public function executeCommand( $command )
    {
        $this->ssh->connect( $this->getHost(), $this->getPort() );
        $this->ssh->authenticate( $this->getUser(), $this->getPublicKey(), $this->getPrivateKey() );
        return $this->ssh->execute( 'cd ' . $this->getDir() . ' && ' . $command );
    }
}