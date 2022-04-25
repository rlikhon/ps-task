<?php

declare(strict_types=1);

namespace CommissionGenerator\Command;

use CommissionGenerator\Service\IOHelpers\CommissionsOutput;
use CommissionGenerator\Service\IOHelpers\CSVFileReaderByLine;
use CommissionGenerator\Service\TransactionsProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CalculateCommissionsCommand.
 */
class CalculateCommissionsCommand extends Command
{
    /**
     * @var string
     */
    //protected static $defaultName = 'app:calculate-commissions';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * CalculateCommissionsCommand constructor.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('app:fees')->addArgument('file', InputArgument::REQUIRED, 'CSV with transactions')->setHidden(true);
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        if (!$this->filesystem->exists($file)) {
            $output->writeln('Error! File not found: '.$file);

            return Command::FAILURE;
        }

        $csvFile = new CSVFileReaderByLine($file);
        $commissionsOutput = new CommissionsOutput();
        $transactionsProcessor = new TransactionsProcessor($csvFile, $commissionsOutput);

        $transactionsProcessor->process();

        return Command::SUCCESS;
    }
}
