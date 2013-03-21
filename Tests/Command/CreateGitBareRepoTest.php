<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\DialogHelper;
use Multiuso\ProjectCreator\Command\CreateGitBareRepo;


class CreateGitBarRepoTest extends \PHPUnit_Framework_TestCase
{
    private $command;
    private $host;
    private $path;
    private $projectName;


    protected function setUp()
    {
        $this->host = 'remoteuser@example.org';
        $this->path = '/srv/git/';
        $this->projectName = 'newRepo';

        $application = new Application();
        $application->add(new CreateGitBareRepo($this->host, $this->path, $this->projectName));

        $this->command = $application->find('create-git-bare-repo');
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

        $this->assertRegExp('/Git Bare Repository not created by user preference/', $commandTester->getDisplay());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testUserAcceptsAndIsNotSuccessful()
    {
        // We mock the DialogHelper
        $this->userSays(true);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute(array('command' => $this->command->getName()));
    }
    
    public function testCommandToExecuteIsTheCorrect()
    {
        $this->assertEquals('ssh remoteuser@example.org git init --bare /srv/git/newRepo', $this->command->createCommand());
    }
}
