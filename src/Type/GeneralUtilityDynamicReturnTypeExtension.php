<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Reflection\MethodReflection;

class GeneralUtilityDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::class;
    }

    public function isStaticMethodSupported(
        MethodReflection $methodReflection
    ): bool {
        return $methodReflection->getName() === 'makeInstance';
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope
    ): Type {
        $arg = $methodCall->args[0]->value;
        if (!($arg instanceof \PhpParser\Node\Expr\ClassConstFetch)) {
            return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        }
        $class = $arg->class;

        return new ObjectType((string)$class);
    }
}
