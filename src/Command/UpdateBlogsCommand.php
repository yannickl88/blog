<?php
namespace App\Command;

use App\Git\Exception\GitInfoParseException;
use App\Git\RepositoryCrawler;
use App\Git\RepositoryInfoParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to update the blog cache.
 */
class UpdateBlogsCommand extends Command
{
    /**
     * @var RepositoryCrawler
     */
    private $crawler;

    /**
     * @var RepositoryInfoParser
     */
    private $infoParser;

    /**
     * @var string
     */
    private $blogsLocations;

    /**
     * @param RepositoryCrawler    $crawler
     * @param RepositoryInfoParser $infoParser
     * @param string               $blogsLocations
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(RepositoryCrawler $crawler, RepositoryInfoParser $infoParser, $blogsLocations)
    {
        parent::__construct('app:update-blogs');

        $this->crawler        = $crawler;
        $this->infoParser     = $infoParser;
        $this->blogsLocations = $blogsLocations;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (glob($this->blogsLocations . '/*') as $folder) {
            if (!file_exists($folder . '/.git/config')) {
                continue;
            }

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln(sprintf('Scanning folder "%s".', $folder));
            }

            try {
                $repo = $this->infoParser->parseFromConfig($folder . '/.git/config');
            } catch (GitInfoParseException $e) {
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln(sprintf('Failed to scan folder, error was: %s', $e->getMessage()));
                }

                continue;
            }


            $this->crawler->update($repo);
        }
    }
}
