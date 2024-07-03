<?php

namespace Youtubedl;

use Symfony\Component\Process\Process;
use Youtubedl\Exceptions\YoutubedlException;

class Youtubedl
{
    private $async = false;
    private $verbose = false;
    private $option;
    private $link;
    protected $binFilePath = null;

    public function __construct()
    {
        $this->option = new Option();
    }

    /**
     * @param string $binFilePath
     *
     * @return static
     */
    public function binFilePath(string $binFilePath): static
    {
        $binFilePath = is_string($binFilePath) && trim($binFilePath) ? $binFilePath : null;

        if (is_null($binFilePath) || !is_file($binFilePath)) {
            throw new YoutubedlException('"binFilePath" param is not a valid file');
        }

        $this->binFilePath = $binFilePath;

        return $this;
    }

    public function getBinFilePath(): string
    {
        if ($this->binFilePath && is_file($this->binFilePath)) {
            return $this->binFilePath;
        }

        return Config::getBinFile();
    }

    public function isAsync(bool $bool = false): Youtubedl
    {
        $this->async = $bool;

        return $this;
    }

    public function isVerbose(bool $bool = false): Youtubedl
    {
        $this->verbose = $bool;

        return $this;
    }

    public function getOption(): Option
    {
        return $this->option;
    }

    public function download(mixed $link): Youtubedl
    {
        if (is_array($link)) {
            $link = implode(' ', $link);
        }
        $this->link = $link;

        return $this;
    }

    public function execute(): array
    {
        $commands = array_filter(
            array_merge([
                $this->getBinFilePath(),
                $this->link
            ], $this->option->format())
        );

        $process = new Process($commands);
        if ($this->verbose) {
            Helper::runProcess($process);
        } else {
            ($this->async) ? $process->start() : $process->run();
        }
        if (!$process->isSuccessful()) {
            throw new YoutubedlException($process->getErrorOutput());
        }

        return explode("\n", trim($process->getOutput()));
    }
}
