<?php

declare(strict_types=1);

namespace Dklementjev\Phpstan\BadIdea\CustomRules;

use Dklementjev\Phpstan\BadIdea\CustomRules\Processor\FunctionCall;
use Dklementjev\Phpstan\BadIdea\CustomRules\Processor\MethodCall;
use Dklementjev\Phpstan\BadIdea\CustomRules\Processor\ProcessorInterface;
use Dklementjev\Phpstan\BadIdea\Helper\ReflectionHelper;
use PhpParser\Node;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall as FuncCallExpr;
use PhpParser\Node\Expr\MethodCall as MethodCallExpr;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


/**
 * @implements Rule<CallLike>
 */
class BadIdeaRule implements Rule
{
    protected ProcessorInterface $functionCallProcessor;
    
    protected ProcessorInterface $methodCallProcessor;

    public function __construct(
        ReflectionProvider $reflectionProvider,
        ExpressionLanguage $expressionLanguage
    ) {
        $this->functionCallProcessor = new FunctionCall($reflectionProvider, $expressionLanguage);
        $this->methodCallProcessor = new MethodCall($reflectionProvider, $expressionLanguage);
    }

    public function getNodeType(): string
    {
        return CallLike::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        return match (true) {
            $node instanceof FuncCallExpr => $this->functionCallProcessor->processNode($node, $scope),
            $node instanceof MethodCallExpr => $this->methodCallProcessor->processNode($node, $scope),
            default => [RuleErrorBuilder::message('Unhandled node class: ' . $node::class)->identifier('badIdea.unhandled')->build()],
        };
    } 
}