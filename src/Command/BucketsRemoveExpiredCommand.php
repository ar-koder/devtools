<?php

declare(strict_types=1);

namespace App\Command;

use App\Manager\BinManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\SplFileInfo;

#[AsCommand(
    name: 'buckets:remove-expired',
    description: 'Remove expired buckets',
)]
class BucketsRemoveExpiredCommand extends Command
{
    public function __construct(private BinManager $binManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $deleted = 0;
        /** @var SplFileInfo $bin */
        foreach ($this->binManager->getAllBin() as $bin) {
            $binId = $bin->getFilenameWithoutExtension();
            $this->binManager->setBinByUuid($binId);
            if ($this->binManager->isExpired()) {
                $this->binManager->deleteBin();
                ++$deleted;
            }
        }

        $io->success(sprintf('%s bins deleted', $deleted));

        return Command::SUCCESS;
    }
}
