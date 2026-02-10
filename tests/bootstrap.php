<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Filesystem\Filesystem;


require dirname(__DIR__).'/vendor/autoload.php';

new Dotenv()->bootEnv(dirname(__DIR__).'/.env');



if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Inicializácia testovacej databázy
$kernel = new \App\Kernel($_SERVER['APP_ENV'] ?? 'test', (bool) ($_SERVER['APP_DEBUG'] ?? false));
(new Filesystem())->remove($kernel->getCacheDir());
$application = new Application($kernel);
$application->setAutoExit(false);
$application->setCatchExceptions(false);

$runCommand = function (string $command) use ($application) {
    $input = new StringInput($command . ' --no-interaction');
    $input->setInteractive(false);
    try {
        $application->run($input);
    } catch (\Exception $e) {
        echo $e->getMessage()."\n";
    }
};

$runCommand('doctrine:database:drop --force --if-exists');
$runCommand('doctrine:database:create --if-not-exists');
$runCommand('doctrine:schema:create');

$kernel->shutdown();
