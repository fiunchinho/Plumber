<?php
namespace Plumber\Server;

interface ServerInterface
{
    /**
     * Return the connection port
     */
    function getPort();

    /**
     * Returns the host
     */
    function getHost();

    /**
     * Returns the directory
     */
    function getDir();

    /**
     * Returns the user
     */
    function getUser();

    /**
     * Returns the password
     */
    function getPassword();

    /**
     * Returns options
     */
    function getOptions();
}