<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Mapper;

use PhpParser\PhpVersion;
use PhpParser\PrettyPrinter\Standard;
use Psr\Container\ContainerInterface;
use VKPHPUtils\Mapping\Exception\RuntimeException;
use VKPHPUtils\Mapping\Generator\Class\ClassBuilder;
use VKPHPUtils\Mapping\Generator\Class\ClassGenerator;
use VKPHPUtils\Mapping\Generator\Class\ClassValidator;
use VKPHPUtils\Mapping\Generator\Class\Method\MethodGenerator;
use VKPHPUtils\Mapping\Generator\Class\Method\Parameter\ParameterGenerator;
use VKPHPUtils\Mapping\Generator\Class\ValidatedClassGenerator;
use VKPHPUtils\Mapping\Generator\ClassName\ShortNameClassNameGenerator;
use VKPHPUtils\Mapping\Generator\ClassName\ValidatedClassNameGenerator;
use VKPHPUtils\Mapping\Generator\InitiationCode\CachedInitiationCodeGenerator;
use VKPHPUtils\Mapping\Generator\InitiationCode\CompositeInitiationCodeGenerator;
use VKPHPUtils\Mapping\Generator\InitiationCode\InitiationCodeGeneratorFromAST;
use VKPHPUtils\Mapping\Generator\InitiationCode\InitiationCodeGeneratorFromLoadedClass;
use VKPHPUtils\Mapping\IMapper;

class StrictTypedMapper implements IMapper
{
    public function __construct(
        private readonly ContainerInterface|null $container = null,
    ) {
    }

    public function getMapper(string $className): object
    {
        $mapperClassNameGenerator = new ShortNameClassNameGenerator();

        $targetMapperClassName = $mapperClassNameGenerator->generateMapperClassName($className);
        $classBuilder = new ClassBuilder($targetMapperClassName);

        $printer = new Standard([
            'phpVersion' => PhpVersion::getHostVersion(),
        ]);
        if (!class_exists($targetMapperClassName)) {
            $mapperClassGenerator = new ClassGenerator(
                $className,
                new MethodGenerator(
                    $classBuilder,
                    new ParameterGenerator(),
                ),
                $classBuilder
            );

            $code = $printer->prettyPrint([$mapperClassGenerator->generateMapperClass()]);

            eval($code);
            dump($code);
        }

        $initiationCodeGenerator = new CompositeInitiationCodeGenerator(
            new InitiationCodeGeneratorFromLoadedClass(),
            new InitiationCodeGeneratorFromAST($classBuilder)
        );

        $initiationCode = $printer->prettyPrint(
            $initiationCodeGenerator->generateInitiationCode($targetMapperClassName)
        );
        $instance = null;
        eval($initiationCode);

        dump($initiationCode);

        return is_object($instance) ? $instance : throw new RuntimeException(
            sprintf('Unable to get mapper "%s"', $className)
        );
    }
}
