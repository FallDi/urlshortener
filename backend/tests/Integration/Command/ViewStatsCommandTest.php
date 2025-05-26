<?php

declare(strict_types=1);

namespace App\Tests\Integration\Command;

use App\Command\ViewStatsCommand;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversMethod(ViewStatsCommand::class, 'execute')]
class ViewStatsCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:view-stats');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Browser', $output);
        $this->assertStringContainsString('OS', $output);
    }
}
