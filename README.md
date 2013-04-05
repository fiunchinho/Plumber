# Plumber

[![Build Status](https://secure.travis-ci.org/fiunchinho/Plumber.png)](http://travis-ci.org/fiunchinho/Plumber)

An object oriented deployer library. This library is heavily inspired by the [Plum|https://github.com/aerialls/Plum] library, but I've tried a different approach.

## Installation and configuration

Plumber follows the PSR-0 convention, so you can use Composer autoloader.

    $options = array(
        'dry_run' => true, // Rsync will run as dry run mode, which only shows the output but does not deploy anything
        'rsync_exclude' => 'excludes.txt', // File used by rsync to exclude files.
        'rsync_options' => '-az', // You can even overwrite the default rsync parameters and use your own arguments
        'commands' => array( // Commands to execute in the server being deployed
            'cd /var/www',
            'ls'
        )
    );

    $deployer               = new \Plumber\Deployer\RsyncDeployer();
    $ssh_command_executer   = new \Plumber\Server\SshCommandExecuter( new \Plumber\Server\SshConnection() );

    $plumber = new \Plumber\Plumber( $deployer, $ssh_command_executer );

    // Add your server
    $plumber->addServer( 'production', new \Plumber\Server\Server( 'your_server_ip_or_hostname', 'username', '/path/to/my/website' ) );
    $plumber->addServer( 'dev', new \Plumber\Server\Server( 'your_server_ip_or_hostname', 'username', '/path/to/my/website' ) );

    // Let's go!
    $plumber->deploy( 'production', $options );
    $plumber->deploy( 'dev', $options );
