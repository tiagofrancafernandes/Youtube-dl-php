<?php

namespace Youtubedl;

use GuzzleHttp\Client;
use Symfony\Component\Process\Process;

class Installation
{
    public static function postUpdate()
    {
        if (file_exists(Config::getBinFile())) {
            self::setPermission();
            self::update();
        } else {
            self::postInstall();
        }
    }

    public static function postInstall()
    {
        self::download();
        self::setPermission();
        self::update();
    }

    private static function setPermission()
    {
        $process = new Process(['chmod', '+x', Config::getBinFile()]);
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });
    }

    private static function download()
    {
        $file = Config::getBinFile();
        $url = 'https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp';
        $client = new Client([
            'verify' => false
        ]);
        $client->request('GET', $url, ['sink' => $file]);
    }

    private static function update() {
        $process = new Process([Config::getBinFile(), '-U']);
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });
    }
}
