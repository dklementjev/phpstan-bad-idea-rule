<?php

declare(strict_types=1);

namespace Dklementjev\Phpstan\BadIdea\CustomRules\Processor;

use Dklementjev\Phpstan\BadIdea\Attribute\BadIdea;
use Dklementjev\Phpstan\BadIdea\Helper\ReflectionHelper;
use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall as FunctionCallExpr;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\AttributeReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpFunctionReflection;
use PHPStan\Reflection\Php\PhpParameterReflection;

class FunctionCall extends Base
{    
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof FunctionCallExpr) {
            throw new \LogicException('Invalid node class: ' . $node::class);
        }

        $rf = $this->reflectionProvider->getFunction($node->name, $scope);        
        $badIdeas = ReflectionHelper::findAttributesByClass(BadIdea::class, $rf->getAttributes());
        $applicableBadIdeas = [];
        foreach ($badIdeas as $badIdea) {
            if ($this->isApplicable($badIdea, $node, $scope, $rf)) {
                $applicableBadIdeas[] = $badIdea;
            }
        }
      
        return $this->buildErrorList($applicableBadIdeas, 'badIdea.functionCall');
    }

    protected function isApplicable(
        AttributeReflection $badIdea, 
        FunctionCallExpr $node,
        Scope $scope,
        PhpFunctionReflection $rf
    ): bool {
        $when = ReflectionHelper::getAttributeArgument($badIdea, 'when');
        if (!$when) {
            return true;
        }

        $nodeArgs = $node->getArgs();
        $variant = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $nodeArgs,
            $rf->getVariants()
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