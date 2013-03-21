<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\DialogHelper;
use Multiuso\ProjectCreator\Command\UpdateComposer;

class UpdateComposerTest extends \PHPUnit_Framework_TestCase
{
    private $localPath = "/home/user/projects/";


    protected function setUp()
    {
        $application = new Application();
        $application->add(new UpdateComposer($this->localPath));

        $this->command = $application->find('update-composer');
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

        $this->assertRegExp('/Composer not updated by user preference/', $commandTester->getDisplay());
    }

    public function testCommandToExecuteIsTheCorrect()
    {
        $this->assertEquals('php /home/user/projects/composer.phar selfupdate', $this->command->createCommand());
    }
}
