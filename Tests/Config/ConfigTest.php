<?php

use Multiuso\ProjectCreator\Config;


class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $config;


    public function setUp()
    {
        $this->config = new Config();

        $this->config->projectName = "newRepo";
        $this->config->host        = "example.org";
        $this->config->remoteUser  = "remoteuser";
        $this->config->gitPath     = "/srv/git/";
        $this->config->localUser   = "localuser";
    }

    public function testFramework()
    {
        $this->assertTrue(true);
    }

    public function testGitConnection()
    {
        $this->assertEquals('remoteuser@example.org', $this->config->gitConnection());
    }

    public function testGitRemoteDir()
    {
        $this->assertEquals('remoteuser@example.org:/srv/git/newRepo', $this->config->gitRemoteDir());
    }

    public function testLocalPath()
    {
        $this->assertEquals('/home/localuser/projects/', $this->config->localPath());
    }
}

