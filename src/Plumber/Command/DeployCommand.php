<?php
namespace Plumber\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends PlumberCommand
{
    protected function configure()
    {
        $this
            ->setName( 'plumber:deploy' )
            ->setDescription( 'Deploys a project to another server' )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'If set, the command only shows what the deployer would do, but it does not actually do it'
            )
            ->setHelp(<<<EOF
The <info>plumber:deploy</info> command deploys a project on a server using rsync:

  <info>php src/console plumber:deploy</info>

You can find out more here https://github.com/fiunchinho/Plumber/.
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $plumber = $this->getPlumber( $output );
        $this->config['dry_run'] = $input->getOption( 'dry-run' );
        $plumber->deploy( 'prod', $this->config );
        $output->writeln('<comment>Deploy has finished</comment>');
    }
}