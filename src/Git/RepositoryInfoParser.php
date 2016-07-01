<?php

namespace App\Git;

use App\Git\Exception\GitInfoParseException;

class RepositoryInfoParser
{
    /**
     * @param string $file
     *
     * @return Repository
     *
     * @throws \App\Git\Exception\GitInfoParseException
     */
    public function parseFromConfig($file)
    {
        $data = parse_ini_file($file, true);

        if ($data === false) {
            throw new GitInfoParseException(sprintf('Could not parse GIT info file "%s"', $file));
        }

        if (!isset($data['remote origin']) && !isset($data['remote origin']['url'])) {
            throw new GitInfoParseException(sprintf('No repository found for remote origin in file "%s"', $file));
        }

        $url = $data['remote origin']['url'];
        $name = basename(dirname($url)).'/'.basename($url, '.git');

        return new Repository(
            $name,
            $url
        );
    }

    /**
     * @param string $url
     *
     * @return Repository
     */
    public function parseFromUrl($url)
    {
        $name = basename(dirname($url)).'/'.basename($url, '.git');

        return new Repository($name, $url);
    }
}
