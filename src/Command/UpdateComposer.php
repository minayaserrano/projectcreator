<?php
namespace Multiuso\ProjectCreator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;


class UpdateComposer extends Command
{
    private $localPath;


    public function __construct($localPath)
    {
        parent::__construct();

        $this->localPath = $localPath;
    }

    public function createCommand()
    {
        return 'php ' . $this->localPath . 'composer.phar selfupdate';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->createCommand();

        $dialog  = $this->getHelperSet()->get('dialog');
        $package = $dialog->askConfirmation($output, "<question>Do you want to update composer?\n" . $command . "\n(y/N)</question>", false);

        if ($package) {
            $process = new Process($command);
            $process->setTimeout(3600);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            $output->writeln(sprintf('<info>%s</info>', $process->getOutput()));        
            $output->writeln(sprintf('<info>Composer updated</info>'));
        } else {
            $output->writeln('<info>Composer not updated by user preference</info>');
        }
    }

    protected function configure()
    {
        $this
            ->setName('update-composer');
    }
}

