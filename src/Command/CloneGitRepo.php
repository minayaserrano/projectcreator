<?php
namespace Multiuso\ProjectCreator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;


class CloneGitRepo extends Command
{
    private $gitRemoteDir;
    private $localPath;
    private $projectName;


    public function __construct($gitRemoteDir, $localPath, $projectName)
    {
        parent::__construct();

        $this->gitRemoteDir = $gitRemoteDir;
        $this->localPath    = $localPath;
        $this->projectName  = $projectName;
    }

    public function createCommand()
    {
        return 'git clone ' . $this->gitRemoteDir . ' ' . $this->localPath . $this->projectName;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->createCommand();

        $dialog  = $this->getHelperSet()->get('dialog');
        $package = $dialog->askConfirmation($output, "<question>Do you want to clone this git repository?\n" . $command . "\n(y/N)</question>", false);

        if ($package) {
            $process = new Process($command);
            $process->setTimeout(3600);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            $output->writeln(sprintf('<info>%s</info>', $process->getOutput()));        
            $output->writeln(sprintf('<info>Git repository cloned</info>'));
        } else {
            $output->writeln('<info>Git Repository not cloned by user preference</info>');
        }
    }

    protected function configure()
    {
        $this
            ->setName('clone-git-repo');
    }
}

