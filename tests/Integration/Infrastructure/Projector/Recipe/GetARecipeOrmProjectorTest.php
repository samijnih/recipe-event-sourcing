<?php

declare(strict_types=1);

namespace Recipe\Tests\Integration\Infrastructure\Projector\Recipe;

use Recipe\Domain\ReadModel\Projection\Recipe\GetARecipeProjection;
use Recipe\Infrastructure\Projector\Recipe\GetARecipeOrmProjector;
use Recipe\Tests\Helper\DatabaseHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @coversDefaultClass \Recipe\Infrastructure\Projector\Recipe\GetARecipeOrmProjector
 * @group integration
 */
final class GetARecipeOrmProjectorTest extends KernelTestCase
{
    use DatabaseHelper;

    private GetARecipeOrmProjector $sut;

    protected function setUp(): void
    {
        $this->sut = self::getContainer()->get(GetARecipeOrmProjector::class);
    }

    /**
     * @test
     * @covers ::saveGetARecipe
     */
    public function iCanSaveTheProjection(): void
    {
        $this->sut->saveGetARecipe(
            new GetARecipeProjection(
                '4c5d6044-72e0-4720-902a-fca7f7402b08',
                'Pressure-Fried Asparagus Yak',
            ),
        );

        $actual = $this->getConnection()
            ->fetchOne('SELECT count(*) FROM public.get_a_recipe WHERE id = :id AND name = :name', [
                'id' => '4c5d6044-72e0-4720-902a-fca7f7402b08',
                'name' => 'Pressure-Fried Asparagus Yak',
            ])
        ;
        self::assertSame(1, $actual);
    }
}
