<?php

declare(strict_types=1);

namespace Recipe\Tests\Helper;

use Assert\Assertion;
use Doctrine\DBAL\Connection;
use function is_subclass_of;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

trait DatabaseHelper
{
    public function getConnection(): Connection
    {
        Assertion::true(is_subclass_of($this, KernelTestCase::class) || is_subclass_of($this, WebTestCase::class));

        /** @var KernelTestCase|WebTestCase $self */
        $self = $this;

        return $self::getContainer()->get(Connection::class);
    }
}
