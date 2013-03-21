<?php
namespace Multiuso\ProjectCreator;

use Symfony\Component\Yaml\Yaml;


class Config
{
    public $projectName;

    // GIT config
    public $host;
    public $remoteUser;
    public $gitPath;

    // Local
    public $localUser;

    // Symfony
    public $symfonyVersion;


    public function __construct()
    {
        if (!$this->remoteUser) {
            $this->remoteUser = `whoami`;
            $this->remoteUser = trim($this->remoteUser, "\n");
        }

        if (!$this->localUser) {
            $this->localUser = `whoami`;
            $this->localUser = trim($this->localUser, "\n");
        }

        // Load config.yml
        $yaml = Yaml::parse('/home/' . $this->localUser . '/projects/projectcreator/config.yml');

        foreach ($yaml as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function gitConnection()
    {
        return $this->remoteUser . '@' . $this->host;
    }

    public function gitRemoteDir()
    {
        return $this->gitConnection() . ':' . $this->gitPath . $this->projectName;
    }

    public function localPath()
    {
        return "/home/". $this->localUser . "/projects/";
    }
}
