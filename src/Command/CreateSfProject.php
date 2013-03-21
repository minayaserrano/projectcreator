<?php
namespace Multiuso\ProjectCreator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;


class CreateSfProject extends Command
{
    private $localPath;
    private $projectName;


    public function __construct($localPath, $projectName, $symfonyVersion)
    {
        parent::__construct();

        $this->localPath      = $localPath;
        $this->projectName    = $projectName;
        $this->symfonyVersion = $symfonyVersion;
    }

    public function createCommand()
    {
        return 'php ' . $this->localPath . 
                'composer.phar create-project symfony/framework-standard-edition ' . 
                $this->localPath . $this->projectName . '/ ' . $this->symfonyVersion;
    }

    public function createCommandFixPerms()
    {
        $cachedir = $this->localPath . $this->projectName . '/app/cache';
        $logsdir  = $this->localPath . $this->projectName . '/app/logs';

        return 'sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX ' . $cachedir . ' ' . $logsdir .
           ' && sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx ' . $cachedir . ' ' . $logsdir;
    }

    public function createCommandCreateGitIgnore()
    {
        return 'cp sfgitignore ' . $this->localPath . $this->projectName . '/.gitignore';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog  = $this->getHelperSet()->get('dialog');
        $package = $dialog->askConfirmation($output, "<question>Do you want to create a symfony2 project?\n" . $this->createCommand() . "\n(y/N)</question>", false);

        if ($package) {
            $process = new Process($this->createCommand());
            $process->setTimeout(3600);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            $output->writeln(sprintf('<info>%s</info>', $process->getOutput()));        
            $output->writeln(sprintf('<info>Symfony2 Project created</info>'));

            // Fix Directory Perms
            $process = new Process($this->createCommandFixPerms());
            $process->setTimeout(3600);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            $output->writeln(sprintf('<info>%s</info>', $process->getOutput()));        
            $output->writeln(sprintf('<info>Cache and logs directories permissions fixed</info>'));

            // Generate .gitignore
            $process = new Process($this->createCommandCreateGitIgnore());
            $process->setTimeout(3600);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            $output->writeln(sprintf('<info>%s</info>', $process->getOutput()));        
            $output->writeln(sprintf('<info>.gitignore file generated</info>'));

        } else {
            $output->writeln('<info>Symfony2 project not created by user preference</info>');
        }
    }

    protected function configure()
    {
        $this
            ->setName('create-symfony-project');
    }
}

