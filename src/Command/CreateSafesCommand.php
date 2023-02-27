<?php

namespace App\Command;

use App\Repository\SafesRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand('app:safes:create', 'Create missing safes for current month in the database')]
class CreateSafesCommand extends Command
{
    /**
     * @param SafesRepository $safesRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly SafesRepository $safesRepository,
                                private readonly TranslatorInterface $translator)
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note($this->translator->trans('Commands.Dry mode'));

            $count = $this->safesRepository->countMissingSafesForCurrentMonth();
        }
        else {
            $count = $this->safesRepository->createMissingSafesForCurrentMonth();
        }

        $io->success($this->translator->trans('Commands.Safes create', [
            '%count%' => $count,
        ]));

        return Command::SUCCESS;
    }
}