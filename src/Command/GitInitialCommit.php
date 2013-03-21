<?php
namespace Multiuso\ProjectCreator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;


class GitInitialCommit extends Command
{
    private $localPath;
    private $projectName;


    public function __construct($localPath, $projectName)
    {
        parent::__construct();

        $this->localPath   = $localPath;
        $this->projectName = $projectName;
    }

    public function createCommand()
    {
        return 'cd ' . $this->localPath . $this->projectName . '/ && git commit -a -m "Initial commit. Symfony2 project created."';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog  = $this->getHelperSet()->get('dialog');
        $package = $dialog->askConfirmation($output, "<question>Do you want to commit the project?\n" . $this->createCommand() . "\n(y/N)</question>", false);

        if ($package) {
            $process = new Process($this->createCommand());
            $process->setTimeout(3600);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            $output->writeln(sprintf('<info>%s</info>', $process->getOutput()));        
            $output->writeln(sprintf('<info>Git initial commit created</info>'));
        } else {
            $output->writeln('<info>Git initial commit not created by user preference</info>');
        }
    }

    protected function configure()
    {
        $this
            ->setName('git-initial-commit');
    }
}

