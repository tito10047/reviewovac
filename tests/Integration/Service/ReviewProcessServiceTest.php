<?php

namespace App\Tests\Integration\Service;

use App\Factory\ReviewFactory;
use App\Service\ReviewProcessService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class ReviewProcessServiceTest extends KernelTestCase
{
    use Factories;

    public function testProcessReview(): void
    {
        $realAi = $_ENV['REAL_AI'] ?? $_SERVER['REAL_AI'] ?? getenv('REAL_AI');
        if (!$realAi) {
            $this->markTestSkipped('Test skipped because REAL_AI environment variable is not set.');
        }

        self::bootKernel();
        $container = static::getContainer();

        /** @var ReviewProcessService $service */
        $service = $container->get(ReviewProcessService::class);

        $review = ReviewFactory::new()->create([
            'content' => 'Tento produkt je skvelý, som veľmi spokojný!',
        ])->getProxy()->_real();

        $response = $service->processReview($review);

        $this->assertNotEmpty($response->sentiment);
        $this->assertIsBool($response->isProductIssue);
    }
}
