<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\DialogHelper;
use Multiuso\ProjectCreator\Command\PushToGitBareRepo;


class PushToGitBareRepoTest extends \PHPUnit_Framework_TestCase
{
    private $command;
    private $gitRemoteDir = 'remoteuser@example.org:/srv/git/newRepo';
    private $localPath    = '/home/localuser/projects/';
    private $projectName  = 'newRepo';


    protected function setUp()
    {
        $application = new Application();
        $application->add(new PushToGitBareRepo($this->gitRemoteDir, $this->localPath, $this->projectName));

        $this->command = $application->find('push-to-git-bare-repo');
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

        $this->assertRegExp('/Git repository not pushed by user preference/', $commandTester->getDisplay());
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
        $this->assertEquals('cd /home/localuser/projects/newRepo/ && ' .
                            'git remote add origin remoteuser@example.org:/srv/git/newRepo && ' . 
                            'git push -u origin master', 
                            $this->command->createCommand());
    }
}
