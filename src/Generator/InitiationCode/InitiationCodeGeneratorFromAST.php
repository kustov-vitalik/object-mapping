<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\InitiationCode;

use PhpParser\Node\Arg;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\UnionType;
use Psr\Container\ContainerInterface;
use VKPHPUtils\Mapping\Constants;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\IClassGenerator;
use VKPHPUtils\Mapping\Generator\IClassNameGenerator;
use VKPHPUtils\Mapping\Generator\IInitiationCodeGenerator;
use VKPHPUtils\Mapping\Helper\BuilderFactory;
use VKPHPUtils\Mapping\Helper\ClassASTHolder;

final readonly class InitiationCodeGeneratorFromAST implements IInitiationCodeGenerator
{
    public function __construct(
        private ClassBuilder $classBuilder,
    ) {
    }

    public function generateInitiationCode(string $instanceClassName): array
    {
        return $this->generateMapperInstanceCreationCodeStmts($instanceClassName);
    }

    public function generateMapperInstanceCreationCodeStmts(string $instanceClassName): array
    {
        $builderFactory = new BuilderFactory();
        $constructor = $this->classBuilder->getConstructor();

        $args = array_map(
            static function (Param $param) use ($builderFactory): Arg {
                $paramName = (string)$param->var->name;
                if (Constants::MAPPER_PROPERTY_NAME === $paramName) {
                    return $builderFactory->argument(
                        $builderFactory->this(),
                        $param->byRef,
                        $param->variadic,
                        [],
                        $builderFactory->id($paramName)
                    );
                }

                $type = $param->type;
                if ($type instanceof NullableType) {
                    $serviceId = $type->type->name;
                } elseif ($type instanceof IntersectionType || $type instanceof UnionType) {
                    $serviceId = $type->types[0]->name;
                } elseif ($type instanceof Name) {
                    if ($type->isFullyQualified()) {
                        $serviceId = $type->toString();
                    } else {
                        $serviceId = $type->toString();
                        throw new NotImplementedYet("Find FQCN for " . $serviceId);
                    }
                } elseif ($type instanceof Identifier) {
                    $serviceId = $type->name;
                } else {
                    // null
                    throw new RuntimeException(sprintf('Unknown type: %s', print_r($type, true)));
                }

                return $builderFactory->argument(
                    $builderFactory->methodCall(
                        $builderFactory->propertyFetch($builderFactory->var('this'), 'container'),
                        'get',
                        [$builderFactory->fullyQualified($serviceId)]
                    ),
                    $param->byRef,
                    $param->variadic,
                    [],
                    $builderFactory->id($paramName)
                );
            }, $constructor->getNode()->params
        );

        $stmts = [];


        if (count($args) > 1) {
            $stmts[] = $builderFactory->if(
                $builderFactory->not(
                    $builderFactory->instanceOf(
                        $builderFactory->propertyFetch($builderFactory->var('this'), 'container'),
                        $builderFactory->fullyQualified(ContainerInterface::class)
                    )
                ),
                [
                    $builderFactory->expression(
                        $builderFactory->throwNewException(
                            \RuntimeException::class,
                            "DI Container should be provided"
                        )
                    )
                ]
            );
        }

        $stmts[] = $builderFactory->expression(
            $builderFactory->assign(
                $builderFactory->var('instance'),
                $builderFactory->new($builderFactory->fullyQualified($instanceClassName), $args)
            )
        );

        return $stmts;
    }
}
