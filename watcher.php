<?php

namespace {
    use Hoa\Console\Processus;
    use Hoa\Core\Event\Bucket;
    use Hoa\File\Watcher;

    require_once 'vendor/autoload.php';

    $file = new Watcher(1);


    echo 'Start listen' . "\n";
    $file
        ->name("#composer.json$#")
        ->in(__DIR__ . '/../')
        ->maxDepth(2)
        ->on('modify', function (Bucket $bucket) {


            $data = $bucket->getData(); //
            $file = $data['file'];
            $cwd  = realpath($file->getPath());
            $cmd  = 'php /usr/local/bin/composer.phar %s --working-dir ' . $cwd;

            if (file_exists($cwd . '/composer.lock'))
                $cmd = sprintf($cmd, 'update');
            else
                $cmd = sprintf($cmd, 'install');

            echo 'Detect an modification on composer.json on directory ' . $cwd . "\n";

            $processus = new Hoa\Console\Processus($cmd);

            $processus->on('output', function ($bucket) {

                $data = $bucket->getData();

                echo $data['line'] . "\n";

                return;
            });

            $processus->on('stop', function () {
                echo 'Wait and see new modification' . "\n";
            });

            $processus->run();

        });
    $file->run();


}
