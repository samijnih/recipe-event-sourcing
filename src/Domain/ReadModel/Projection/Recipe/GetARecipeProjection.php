<?php

declare(strict_types=1);

namespace Recipe\Domain\ReadModel\Projection\Recipe;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: GetARecipeProjector::class, readOnly: false)]
#[Table(name: 'get_a_recipe', schema: 'public')]
final class GetARecipeProjection
{
    #[Id, Column(type: 'guid')]
    #[GeneratedValue(strategy: 'NONE')]
    private string $id;

    #[Column(type: 'string', nullable: false)]
    private string $name;

    public function __construct(
        string $id,
        string $name,
    ) {
        $this->id = $id;
        $this->name = $name;
    }
}
