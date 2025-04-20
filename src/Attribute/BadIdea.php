<?php

declare(strict_types=1);

namespace Dklementjev\Phpstan\BadIdea\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
readonly class BadIdea
{
    public function __construct(
        public ?string $why = null,
        public ?string $when = null, 
    ) {}
}