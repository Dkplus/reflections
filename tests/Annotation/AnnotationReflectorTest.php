<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\Annotation;

use Dkplus\Reflection\Annotation\AnnotationFactory;
use Dkplus\Reflection\Annotation\AnnotationVisitor;
use Dkplus\Reflection\Annotation\Context;
use Dkplus\Reflection\Annotation\HoaParser;
use Dkplus\Reflection\Reflector;
use Dkplus\Reflection\ReflectorStrategy\BuiltInReflectorStrategy;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use function var_dump;

class AnnotationReflectorTest extends TestCase
{
    /** @test */
    public function it_()
    {
        $parser = new HoaParser();
        var_dump($parser->parseDockBlock(
            (new ReflectionClass('Hoa\\Compiler\\Exception\\Lexer'))->getDocComment(),
            new AnnotationVisitor(
                new AnnotationFactory(new Reflector(new BuiltInReflectorStrategy())),
                new Context('Hoa\Compiler\Exception', [])
            )
        ));
    }
}
