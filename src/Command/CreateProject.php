<?php
namespace Multiuso\ProjectCreator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;


class CreateProject extends Command
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function runCommand($command, OutputInterface $output)
    {
        $command = $this->getApplication()->find($command);
    
        $arguments = array(
            'command' => $command,
        );
    
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = array(
            'create-git-bare-repo',
            'clone-git-repo',
            'update-composer',
            'create-symfony-project',
            'git-initial-commit'
        );

        foreach($commands as $command) {
            $this->runCommand($command, $output);
        }
    }

    protected function configure()
    {
        $this
            ->setName('create-project');
    }
}

