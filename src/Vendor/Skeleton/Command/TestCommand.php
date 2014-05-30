<?php

namespace Vendor\Skeleton\Command;

use Saxulum\Console\Command\AbstractPimpleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends AbstractPimpleCommand
{
    protected function configure()
    {
        $this
            ->setName('vendor:skeleton:test')
            ->setDescription('Test command')
        ;
    }

    /**
     * @param  InputInterface    $input
     * @param  OutputInterface   $output
     * @return int|null|void
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>success</info>');
    }
}
