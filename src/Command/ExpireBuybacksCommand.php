<?php

namespace App\Command;

use App\Repository\BuybacksRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:buybacks:expire', 'Expire all pending buybacks that have expired from the database')]
class ExpireBuybacksCommand extends Command
{
    /**
     * @param BuybacksRepository $buybacksRepository
     */
    public function __construct(private readonly BuybacksRepository $buybacksRepository)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

            $count = $this->buybacksRepository->countPendingBuybacksToExpired();
        }
        else {
            $count = $this->buybacksRepository->updatePendingBuybacksToExpired();
        }

        $io->success(sprintf('Updated "%d" expired buybacks.', $count));

        return Command::SUCCESS;
    }
}