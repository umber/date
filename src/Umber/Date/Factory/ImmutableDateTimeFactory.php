<?php

declare(strict_types=1);

namespace Umber\Date\Factory;

use DateTimeImmutable;

/**
 * {@inheritdoc}
 */
final class ImmutableDateTimeFactory implements DateTimeFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
