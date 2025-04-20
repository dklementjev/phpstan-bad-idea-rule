<?php

declare(strict_types=1);

namespace Dklementjev\Phpstan\BadIdea\CustomRules\Processor;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleError;

interface ProcessorInterface
{
    /**
     * @return IdentifierRuleError[]
     */
    public function processNode(Node $node, Scope $scope): array;
}