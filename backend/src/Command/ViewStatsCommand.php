<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserMetaDataRepository;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:view-stats',
    description: 'Show stats of clients who creates short URLs.',
)]
class ViewStatsCommand extends Command
{
    public function __construct(private readonly UserMetaDataRepository $userMetaDataRepository)
    {
        parent::__construct();
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);

        $osData = [];
        foreach ($this->userMetaDataRepository->getOsData() as $os => $numberOfCreatedUrls) {
            $osData[] = [$os, $numberOfCreatedUrls];
        }

        $table->setHeaders(['OS', 'number of created URLs'], )
            ->setRows($osData);
        $table->render();

        $browserData = [];
        foreach ($this->userMetaDataRepository->getBrowserData() as $browser => $numberOfCreatedUrls) {
            $browserData[] = [$browser, $numberOfCreatedUrls];
        }

        $table->setHeaders(['Browser', 'number of created URLs'], )
            ->setRows($browserData);
        $table->render();

        return Command::SUCCESS;
    }
}
