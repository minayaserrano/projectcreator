<?php
namespace ProjectCreator;

require 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use Multiuso\ProjectCreator\Config;
use Multiuso\ProjectCreator\Command\CreateGitBareRepo;
use Multiuso\ProjectCreator\Command\CloneGitRepo;
use Multiuso\ProjectCreator\Command\UpdateComposer;
use Multiuso\ProjectCreator\Command\CreateSfProject;
use Multiuso\ProjectCreator\Command\GitInitialCommit;


$config = new Config();
$application = new Application('Project Creator by multiuso', '1.0.0-alpha');
$application->add(new CreateGitBareRepo($config->gitConnection(), $config->gitPath, $config->projectName));
$application->add(new CloneGitRepo($config->gitRemoteDir(), $config->localPath(), $config->projectName));
$application->add(new UpdateComposer($config->localPath()));
$application->add(new CreateSfProject($config->localPath(), $config->projectName, $config->symfonyVersion));
$application->add(new GitInitialCommit($config->localPath(), $config->projectName));

$application->run();
