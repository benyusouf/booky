<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:highest-reviews',
    description: 'Displays the day or month with the highest number of published reviews',
)]
class HighestReviewsCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addOption('month', null, InputOption::VALUE_NONE, 'Display the month instead of the day');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $monthMode = $input->getOption('month');

        $connection = $this->entityManager->getConnection();
        $sql = $monthMode ? $this->getMonthQuery() : $this->getDayQuery();
        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $result = $resultSet->fetchAssociative();

        if ($result) {
            $period = $monthMode ? $result['month'] : $result['day'];
            $io->success(sprintf('The highest number of reviews were published on %s', $period));
        } else {
            $io->warning('No reviews found in the database.');
        }

        return Command::SUCCESS;
    }

    private function getDayQuery(): string
    {
        return "
            SELECT 
                DATE(published_at) as day, COUNT(*) as review_count 
            FROM 
                review 
            GROUP BY 
                day 
            ORDER BY 
                review_count DESC, day DESC 
            LIMIT 1
        ";
    }

    private function getMonthQuery(): string
    {
        return "
            SELECT 
                TO_CHAR(published_at, 'YYYY-MM') as month, COUNT(*) as review_count 
            FROM 
                review 
            GROUP BY 
                month 
            ORDER BY 
                review_count DESC, month DESC 
            LIMIT 1
        ";
    }
}
