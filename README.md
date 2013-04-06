# Plumber

[![Build Status](https://secure.travis-ci.org/fiunchinho/Plumber.png)](http://travis-ci.org/fiunchinho/Plumber)

An object oriented deployer library. This library is heavily inspired by the [Plum](https://github.com/aerialls/Plum "Plum") library, but I've tried a different approach.

## Installation and configuration

Plumber follows the PSR-0 convention, so you can use Composer autoloader.

    // Options. Here you can define which unix commands you want to execute in the server
    $options = array(
        'dry_run' => true, // Optional: Default to false. If set to true, Rsync will run as dry-run mode, which only shows the output but doesn't deploy anything
        'rsync_exclude' => 'excludes.txt', // Optional. File used by rsync to exclude files.
        'rsync_options' => '-az', // Optional: Default to "-azC --force --delete --progress". You can overwrite the default rsync parameters and use your own arguments
        'commands' => array( // Optional. Commands to execute in the server being deployed. The commands'll be executed in the path defined by the server
            'php app/console doctrine:schema:create',
            'php app/console cache:warmup'
        )
    );

    // Set up
    $deployer               = new \Plumber\Deployer\RsyncDeployer();
    $ssh_command_executer   = new \Plumber\Server\SshCommandExecuter( new \Plumber\Server\SshConnection() );
    $plumber = new \Plumber\Plumber( $deployer, $ssh_command_executer );

    // Add your server
    $plumber->addServer( 'production', new \Plumber\Server\Server( 'your_server_ip_or_hostname', 'username', '/path/to/my/website' ) );
    $plumber->addServer( 'dev', new \Plumber\Server\Server( 'your_server_ip_or_hostname', 'username', '/path/to/my/website' ) );

    // Let's go!
    $plumber->deploy( 'production', $options );
    $plumber->deploy( 'dev', $options );

If you prefer to deploy using git pull, instead of using rsync, you can use the dummy rsync deployer. The following code would connect to the server that you define, and execute three commands there.

    // Options. Here you can define which unix commands you want to execute in the server
    $options = array(
        'commands' => array( // Commands to execute in the server being deployed
            'git pull',
            'rm -rf cache/',
            'php app/console assetic:dump --env=prod --no-debug'
        )
    );

    // Set up
    $no_rsync_deployer      = new \Plumber\Deployer\NoRsyncDeployer();
    $ssh_command_executer   = new \Plumber\Server\SshCommandExecuter( new \Plumber\Server\SshConnection() );
    $plumber = new \Plumber\Plumber( $no_rsync_deployer, $ssh_command_executer );

    // Add your server
    $plumber->addServer( 'prod', new \Plumber\Server\Server( 'your_server_ip_or_hostname', 'username', '/path/to/my/website' ) );

    // Let's go!
    $plumber->deploy( 'prod', $options );
