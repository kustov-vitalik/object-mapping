<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class\Method;

use PhpParser\Node\Expr\Variable;
use VKPHPUtils\Mapping\Constants;
use VKPHPUtils\Mapping\DS\Map;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;
use VKPHPUtils\Mapping\Reflection\ReflectionParameter;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

class MethodVarScope
{
    /**
     * @var Map<string|ReflectionParameter|ReflectionProperty, Variable> 
     */
    readonly private Map $map;

    readonly private BuilderFactory $builderFactory;

    readonly private VarNameProvider $varNameProvider;

    private int $parametersCount = 0;

    public function __construct(ReflectionMethod $reflectionMethod)
    {
        $this->map = new Map();
        $this->builderFactory = new BuilderFactory();
        $this->varNameProvider = new VarNameProvider();

        foreach ($reflectionMethod->getMapperParameters() as $reflectionParameter) {
            $this->putParameter($reflectionParameter);
        }

        $this->putTargetVar(
            $reflectionMethod->hasTargetParameter()
                ? $this->getParameterVar($reflectionMethod->getTargetParameter())
                : $this->createVar('target')
        );
    }

    public function getTargetVar(): Variable
    {
        return $this->map->get(Constants::RETURN_VAR_KEY);
    }

    private function putTargetVar(Variable $variable): void
    {
        $this->map->put(Constants::RETURN_VAR_KEY, $variable);
    }

    public function getParameterVar(ReflectionParameter $reflectionParameter): Variable
    {
        return $this->map->get($reflectionParameter);
    }

    public function getParametersCount(): int
    {
        return $this->parametersCount;
    }

    public function getTheOnlyParameterVar(): Variable
    {
        foreach ($this->map as $parameter => $var) {
            if ($parameter instanceof ReflectionParameter) {
                return $var;
            }
        }

        throw new RuntimeException('No parameter found');
    }

    private function putParameter(ReflectionParameter $reflectionParameter): void
    {
        $this->map->put($reflectionParameter, $this->builderFactory->var($this->varNameProvider->getUniqueName($reflectionParameter->name)));
        ++$this->parametersCount;
    }

    public function putTargetProperty(ReflectionProperty $reflectionProperty): void
    {
        $this->map->put($reflectionProperty, $this->builderFactory->var($this->varNameProvider->getUniqueName($reflectionProperty->name)));
    }

    public function getTargetPropertyVar(ReflectionProperty $reflectionProperty): Variable
    {
        return $this->map->get($reflectionProperty);
    }

    public function createVar(string $name): Variable
    {
        return $this->builderFactory->var($this->varNameProvider->getUniqueName($name));
    }

}
