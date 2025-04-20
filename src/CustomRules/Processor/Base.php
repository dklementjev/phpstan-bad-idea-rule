<?php


declare(strict_types=1);

namespace Dklementjev\Phpstan\BadIdea\CustomRules\Processor;

use Dklementjev\Phpstan\BadIdea\Helper\ReflectionHelper;
use PHPStan\Reflection\AttributeReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

abstract class Base implements ProcessorInterface
{
    public function __construct(
        protected ReflectionProvider $reflectionProvider,
        protected ExpressionLanguage $expressionLanguage,
    ) {}

    /**
     * @return IdentifierRuleError[]
     */
    protected function buildErrorList(array $badIdeas, string $ruleName): array
    {
        return array_map(
            function (AttributeReflection $badIdea) use ($ruleName): RuleError {                
                return $this->buildError($badIdea, $ruleName);
            },
            $badIdeas
        );        
    }

    protected function buildError(AttributeReflection $badIdea, string $ruleName): IdentifierRuleError
    {
        return RuleErrorBuilder::message(ReflectionHelper::getAttributeArgument($badIdea, 'why', '<No reason given>'))
            ->identifier($ruleName)
            ->build()
        ;
    }
}