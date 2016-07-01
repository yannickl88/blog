<?php

namespace App\Command;

use App\Git\Exception\GitInfoParseException;
use App\Git\RepositoryCrawler;
use App\Git\RepositoryInfoParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to add a blog to the collection of managed blog repositories.
 */
class AddBlogCommand extends Command
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
     * @param RepositoryCrawler    $crawler
     * @param RepositoryInfoParser $infoParser
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(RepositoryCrawler $crawler, RepositoryInfoParser $infoParser)
    {
        parent::__construct('app:add-blog');

        $this->crawler = $crawler;
        $this->infoParser = $infoParser;
    }

    protected function configure()
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'git URL');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $repo = $this->infoParser->parseFromUrl($input->getArgument('url'));
        } catch (GitInfoParseException $e) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln(sprintf('Failed to parse URL, error was: %s', $e->getMessage()));
            }

            return;
        }

        $this->crawler->update($repo);
    }
}
