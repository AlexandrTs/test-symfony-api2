<?php

namespace App\Command;

use App\Provider\CustomersProviderInterface;
use App\Service\CustomersImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-customers',
    description: 'Import customers to database from remote source',
)]
class ImportCustomersCommand extends Command
{
    /**
     * @var CustomersImporter
     */
    private $importer;

    public function __construct(CustomersImporter $importer)
    {
        $this->importer = $importer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'nationality',
                'N',
                InputOption::VALUE_NONE,
                'Customer nationality'
            )
            ->addOption('count', 'C', InputOption::VALUE_OPTIONAL, 'Customers count')
        ;
    }

    /**
     * Import customers data from RandomUser API
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nationality = $input->getOption('nationality') ?: CustomersProviderInterface::NATIONALITY_AU;
        $count       = $input->getOption('count') ?: 100;

        $io = new SymfonyStyle($input, $output);

        try {
            $stats = $this->importer->import($nationality, $count);
        } catch(\Throwable $ex) {

            $io->error('Command exit with error: ' . $ex->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Command successfully finished. Inserted rows: %d, updated rows: %d',
            $stats['inserted'],
            $stats['updated']
        ));

        return Command::SUCCESS;
    }
}
