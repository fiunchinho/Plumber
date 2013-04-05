<?php

/*
 * This file is part of the Plum package.
 *
 * (c) 2010-2012 Julien Brochet <mewt@madalynn.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plum\Server;

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

    public function __construct($host, $user, $dir, $port = 22, $options = array())
    {
        if ('/' !== substr($dir, -1)) {
            $dir .= '/';
        }

        $this->host     = $host;
        $this->user     = $user;
        $this->port     = $port;
        $this->dir      = $dir;
        $this->options  = $options;
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

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }
}