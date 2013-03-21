<?php
namespace Multiuso\ProjectCreator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;


class CreateGitBareRepo extends Command
{
    private $host;
    private $path;
    private $projectName;


    public function __construct($host, $path, $projectName)
    {
        parent::__construct();

        $this->host        = $host;
        $this->path        = $path;
        $this->projectName = $projectName;
    }

    public function createCommand()
    {
        return 'ssh ' . $this->host . ' git init --bare ' . $this->path . $this->projectName;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog  = $this->getHelperSet()->get('dialog');
        $package = $dialog->askConfirmation($output, '<question>Do you want to create a new git bare repository? (y/N)</question>', false);

        if ($package) {
            $process = new Process($this->createCommand());
            $process->setTimeout(5);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            $output->writeln(sprintf('<info>%s</info>', $process->getOutput()));        
        } else {
            $output->writeln('<info>Git Bare Repository not created by user preference</info>');
        }
    }

    protected function configure()
    {
        $this
            ->setName('create-git-bare-repo');
    }
}

