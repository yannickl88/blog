<?php
namespace App\Command;

use App\Git\RepositoryCrawler;
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
     * @param RepositoryCrawler $crawler
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(RepositoryCrawler $crawler)
    {
        parent::__construct('app:update-blogs');

        $this->crawler = $crawler;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->crawler->updateAll();
    }
}
