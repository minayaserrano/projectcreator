<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\DialogHelper;
use Multiuso\ProjectCreator\Command\CreateSfProject;

class CreateSfProjectTest extends \PHPUnit_Framework_TestCase
{
    private $localPath      = "/home/user/projects/";
    private $projectName    = 'newRepo';
    private $symfonyVersion = '2.2.0';


    protected function setUp()
    {
        $application = new Application();
        $application->add(new CreateSfProject($this->localPath, $this->projectName, $this->symfonyVersion));

        $this->command = $application->find('create-symfony-project');
    }

    private function userSays($say = false)
    {
        // We mock the DialogHelper
        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askConfirmation'));
        $dialog->expects($this->at(0))
                              ->method('askConfirmation')
                              ->will($this->returnValue($say));

        $this->command->getHelperSet()->set($dialog, 'dialog');
    }

    public function testUserCancels()
    {
        // We mock the DialogHelper
        $this->userSays(false);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array('command' => $this->command->getName()));

        $this->assertRegExp('/Symfony2 project not created by user preference/', $commandTester->getDisplay());
    }

    public function testCommandToExecuteIsTheCorrect()
    {
        $this->assertEquals('php /home/user/projects/composer.phar create-project symfony/framework-standard-edition /home/user/projects/newRepo/ 2.2.0', $this->command->createCommand());
    }

    public function testCommandFixPermsToExecuteIsTheCorrect()
    {
        $this->assertEquals('sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX /home/user/projects/newRepo/app/cache /home/user/projects/newRepo/app/logs && sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx /home/user/projects/newRepo/app/cache /home/user/projects/newRepo/app/logs', 
                            $this->command->createCommandFixPerms());
    }

    public function testCommandCreateGitIgnoreToExecuteIsTheCorrect()
    {
        $this->assertEquals('cp sfgitignore /home/user/projects/newRepo/.gitignore',
                            $this->command->createCommandCreateGitIgnore());
    }
}
