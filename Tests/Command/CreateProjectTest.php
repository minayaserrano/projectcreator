<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\Helper;
use Multiuso\ProjectCreator\Command\CreateProject;

class CreateProjectTest extends \PHPUnit_Framework_TestCase
{
    private $application;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->add(new CreateProject());

        $this->command = $this->application->find('create-project');
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

    public function testRunCommandExecutes()
    {
        // @TODO
    }

}
