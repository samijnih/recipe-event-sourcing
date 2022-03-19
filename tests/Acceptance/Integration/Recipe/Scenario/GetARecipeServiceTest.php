<?php

declare(strict_types=1);

namespace Recipe\Tests\Acceptance\Integration\Recipe\Scenario;

use Recipe\Domain\UseCase\Recipe\Get\CannotGetARecipe;
use Recipe\Domain\UseCase\Recipe\Get\GetARecipeModel;
use Recipe\Domain\UseCase\Recipe\Get\GetARecipeService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @coversDefaultClass \Recipe\Domain\UseCase\Recipe\Get\GetARecipeService
 * @group acceptance
 * @group integration
 */
final class GetARecipeServiceTest extends KernelTestCase
{
    private GetARecipeService $sut;

    protected function setUp(): void
    {
        $this->sut = self::getContainer()->get(GetARecipeService::class);
    }

    /**
     * @test
     * @covers ::__invoke
     */
    public function iCanGetARecipeThatExists(): void
    {
        // Act
        $actual = $this->sut->__invoke('09a91f17-a8d7-46f1-a26f-eae3bd7e7ffe');

        // Assert
        $expected = new GetARecipeModel(
            '09a91f17-a8d7-46f1-a26f-eae3bd7e7ffe',
            'recipeExists',
        );
        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     * @covers ::__invoke
     */
    public function iCannotGetARecipeThatDoesNotExist(): void
    {
        self::expectException(CannotGetARecipe::class);

        $this->sut->__invoke('cf482f16-ce44-4ca3-a543-e23e51be9d3b');
    }
}
