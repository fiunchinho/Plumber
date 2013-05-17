<?php
namespace Plumber\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends PlumberCommand
{
    protected function configure()
    {
        $this
            ->setName( 'plumber:rollback' )
            ->setDescription( 'Deploys a project to another server' )
            ->addArgument(
                'versions_back',
                InputArgument::OPTIONAL,
                'How many versions do you want to go back?'
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
        $plumber = $this->getPlumber();
        $rollback_folder = $plumber->rollback( 'prod', ( $input->getArgument('versions_back')?: 1 ) );
        $output->writeln( 'You\'ve made a rollback to the release: ' . $rollback_folder );
    }
}