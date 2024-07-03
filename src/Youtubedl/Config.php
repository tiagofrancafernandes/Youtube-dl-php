<?php

namespace Youtubedl;

use Youtubedl\Exceptions\YoutubedlException;

/**
 * @property string|null $binDirectory
 * @property string|null $binFileName
 * @property string|null $binFilePath
 */
class Config
{
    protected static $binDirectory = null;
    protected static $binFileName = null;
    protected static $binFilePath = null;

    public static function binExists(): bool
    {
        return file_exists(static::getBinFile());
    }

    public static function restorePathsToDefault(): void
    {
        static::$binDirectory = null;
        static::$binFileName = null;
        static::$binFilePath = null;
    }

    /**
     * @param string $binDirectory
     * @return void
     */
    public static function binDirectory(string $binDirectory): void
    {
        $binDirectory = is_string($binDirectory) && trim($binDirectory) ? $binDirectory : null;

        if (is_null($binDirectory) || !is_dir($binDirectory)) {
            throw new YoutubedlException('"binDirectory" param is not a valid dir');
        }

        static::$binDirectory = $binDirectory;
    }

    /**
     * @param string $binFilePath
     * @return void
     */
    public static function binFilePath(string $binFilePath): void
    {
        $binFilePath = is_string($binFilePath) && trim($binFilePath) ? $binFilePath : null;

        if (is_null($binFilePath) || !is_file($binFilePath)) {
            throw new YoutubedlException('"binFilePath" param is not a valid file');
        }

        static::$binFilePath = $binFilePath;
    }

    public static function getBinDirectory(): string
    {
        $binDir = static::$binDirectory;

        $binDir = !is_string($binDir) || !is_dir($binDir) ? __DIR__ . '/../../vendor/bin' : $binDir;

        return $binDir;
    }

    /**
     * @param string $binFileName
     * @return void
     */
    public static function binFileName(string $binFileName): void
    {
        static::$binFileName = $binFileName;
    }

    public static function getBinFile(): string
    {
        if (static::$binFilePath && is_string(static::$binFilePath)) {
            return static::$binFilePath;
        }

        $binFileName = static::$binFileName;

        $binFileName = is_string($binFileName) && trim($binFileName) ? $binFileName : 'Youtubedl';

        return static::getBinDirectory() . DIRECTORY_SEPARATOR . $binFileName;
    }
}
