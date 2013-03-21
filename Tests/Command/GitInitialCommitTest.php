<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\DialogHelper;
use Multiuso\ProjectCreator\Command\GitInitialCommit;

class GitInitialCommitTest extends \PHPUnit_Framework_TestCase
{
    private $localPath   = "/home/user/projects/";
    private $projectName = "newRepo";


    protected function setUp()
    {
        $application = new Application();
        $application->add(new GitInitialCommit($this->localPath, $this->projectName));

        $this->command = $application->find('git-initial-commit');
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

        $this->assertRegExp('/Git initial commit not created by user preference/', $commandTester->getDisplay());
    }

    public function testCommandToExecuteIsTheCorrect()
    {
        $this->assertEquals('cd /home/user/projects/newRepo/ && git commit -a -m "Initial commit. Symfony2 project created."', 
                            $this->command->createCommand());
    }
}
