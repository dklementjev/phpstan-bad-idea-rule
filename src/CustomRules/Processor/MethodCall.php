<?php

declare(strict_types=1);

namespace Dklementjev\Phpstan\BadIdea\CustomRules\Processor;

use Dklementjev\Phpstan\BadIdea\Attribute\BadIdea;
use Dklementjev\Phpstan\BadIdea\Helper\ReflectionHelper;
use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall as MethodCallExpr;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\AttributeReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\Php\PhpParameterReflection;
use PHPStan\Type\ObjectType;

class MethodCall extends Base
{
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof MethodCallExpr) {
            throw new \LogicException('Invalid node class: ' . $node::class);
        }

        /** @var ObjectType */
        $type = $scope->getType($node->var);
        $rc = $type->getClassReflection();
        $rm = $rc->getMethod($node->name->name, $scope);

        $badIdeas = ReflectionHelper::findAttributesByClass(BadIdea::class, $rm->getAttributes());
        $applicableBadIdeas = [];
        foreach ($badIdeas as $badIdea) {
            if ($this->isApplicable($badIdea, $node, $scope, $rm)) {
                $applicableBadIdeas[] = $badIdea;
            }
        }

        return $this->buildErrorList($applicableBadIdeas, 'badIdea.methodCall');
    }

    private function isApplicable(
        AttributeReflection $badIdea, 
        MethodCallExpr $node,
        Scope $scope,
        PhpMethodReflection $rm
    ): bool {
        $when = ReflectionHelper::getAttributeArgument($badIdea, 'when');
        if (!$when) {
            return true;
        }

        $nodeArgs = $node->getArgs();
        //...
        $variant = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $nodeArgs,
            $rm->getVariants()
        );
        $parameters = $variant->getParameters();

        $arguments = [];
        $constEvaluator = new ConstExprEvaluator();
        /** @var PhpParameterReflection $parameter */
        foreach ($parameters as $index => $parameter) {
            try {
                $argument = $constEvaluator->evaluateSilently($nodeArgs[$index]->value);
            } catch (ConstExprEvaluationException $e) {
                return false;
            }
            $arguments[$index] = $argument;
            $arguments[$parameter->getName()] = $argument;
        }        
        
        $data = [
            'arguments' => $arguments,
        ];
        $isApplicable = $this->expressionLanguage->evaluate($when, $data);

        return (bool) $isApplicable;
    }
}