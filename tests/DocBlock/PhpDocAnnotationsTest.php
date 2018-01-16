<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\ClassReflector\BuiltInClassReflector;
use Dkplus\Reflection\DocBlock\DocBlockReflector;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void_;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use test\Dkplus\Reflection\DocBlock\Fixtures\PhpDocAnnotations;
use test\Dkplus\Reflection\DocBlock\TestCase\DocBlockTestCase;

/**
 * @covers AnnotationFactory
 * @covers DocBlockVisitor
 * @covers HoaParser
 */
class PhpDocAnnotationsTest extends DocBlockTestCase
{
    /** @var DocBlockReflector */
    private $reflector;

    protected function setUp()
    {
        $this->reflector = new DocBlockReflector(new BuiltInClassReflector(), new FqsenResolver());
    }

    /**
     * @test
     * @dataProvider provideExpectedPhpDocTags
     */
    public function it_reflects_php_doc_tags(string $name, array $expectedAttributes)
    {
        $docBlock = $this->reflector->reflectDocBlockOf(new ReflectionClass(PhpDocAnnotations::class));
        self::assertDocBlockHasUnqualifiedTag($name, $docBlock);
        self::assertDocBlockHasAnnotationWithTagAndAttributes($name, $expectedAttributes, $docBlock);
    }

    public static function provideExpectedPhpDocTags()
    {
        return [
            'author' => ['author', ['name' => 'My Name']],
            'author with email' => ['author', ['name' => 'My Name', 'emailaddress' => 'my.name@example.com']],
            'copyright' => ['copyright', ['description' => '2017-2018 by some company']],
            'deprecated' => ['deprecated', ['description' => '']],
            'deprecated with version' => ['deprecated', ['version' => '1.0.0', 'description' => '']],
            'deprecated with description' => ['deprecated', ['description' => 'because we replaced it']],
            'deprecated with version and description' => [
                'deprecated',
                ['version' => '1.0.0', 'description' => 'because we replaced it'],
            ],
            'ignore' => ['ignore', ['description' => '']],
            'ignore with description' => ['ignore', ['description' => 'this tag']],
            'internal' => ['internal', ['description' => '']],
            'internal with description' => ['internal', ['description' => 'again because we need a description']],
            'license' => ['license', ['name' => 'GPL']],
            'license with url' => [
                'license',
                ['url' => 'http://opensource.org/licenses/gpl-license.php', 'name' => 'GNU Public License'],
            ],
            'link' => [
                'link',
                ['uri' => 'https://phpdoc.org/docs/latest/references/phpdoc/tags/link.html', 'description' => ''],
            ],
            'link with description' => [
                'link',
                [
                    'uri' => 'https://phpdoc.org/docs/latest/references/phpdoc/tags/link.html',
                    'description' => 'with description',
                ],
            ],
            'method' => ['method', ['return' => new String_(), 'name' => 'getString', 'params' => []]],
            'method without return type with multiple parameters' => [
                'method',
                [
                    'return' => new Void_(),
                    'name' => 'setString',
                    'params' => [
                        ['type' => new String_(), 'name' => '$param1'],
                        ['type' => new Mixed_(), 'name' => '$param2'],
                    ],
                ],
            ],
            'package' => ['package', ['name' => 'Foo\\Bar']],
            'subpackage' => ['subpackage', ['name' => 'Baz']],
            'property' => ['property', ['type' => new String_(), 'name' => '$property3', 'description' => '']],
            'property with description' => [
                'property',
                ['type' => new String_(), 'name' => '$property4', 'description' => 'with description'],
            ],
            'property-read' => [
                'property-read',
                ['type' => new String_(), 'name' => '$property5', 'description' => ''],
            ],
            'property-read with description' => [
                'property-read',
                ['type' => new String_(), 'name' => '$property6', 'description' => 'with description'],
            ],
            'property-write' => [
                'property-write',
                ['type' => new String_(), 'name' => '$property7', 'description' => ''],
            ],
            'property-write with description' => [
                'property-write',
                ['type' => new String_(), 'name' => '$property8', 'description' => 'with description'],
            ],
            'see with fqsen' => [
                'see',
                ['fqsen' => new Fqsen('\\' . PhpDocAnnotations::class . '::$property1'), 'description' => ''],
            ],
            'see with fqsen and description' => [
                'see',
                [
                    'fqsen' => new Fqsen('\\' . PhpDocAnnotations::class . '::$property2'),
                    'description' => 'with description',
                ],
            ],
            'see with uri' => ['see', ['uri' => 'http://example.org/', 'description' => '']],
            'see with uri and description' => ['see', ['uri' => 'http://example.org/', 'description' => 'description']],
            'since' => ['since', ['version' => '1.0.0', 'description' => '']],
            'since with description' => ['since', ['version' => '1.0.0', 'description' => 'with description']],
            'todo' => ['todo', ['description' => 'something is missing']],
            'uses' => [
                'uses',
                ['fqsen' => new Fqsen('\\' . PhpDocAnnotations::class . '::exampleFunction()'), 'description' => ''],
            ],
            'uses with description' => [
                'uses',
                ['fqsen' => new Fqsen('\\' . PhpDocAnnotations::class), 'description' => 'with description'],
            ],
            'version' => ['version', ['description' => '']],
            'version with vector' => ['version', ['vector' => '3.0.0', 'description' => '']],
            'version with vector and description' => [
                'version',
                ['vector' => '3.0.0', 'description' => 'with description'],
            ],
        ];
    }

    /** @test */
    public function it_reflects_the_php_doc_var_tag()
    {
        $docBlock = $this->reflector->reflectDocBlockOf(
            new ReflectionProperty(PhpDocAnnotations::class, 'property1')
        );
        self::assertDocBlockHasUnqualifiedTag('var', $docBlock);
        self::assertDocBlockHasAnnotationWithTagAndAttributes(
            'var',
            ['type' => new String_(), 'description' => ''],
            $docBlock
        );
    }

    /** @test */
    public function it_reflects_the_php_doc_var_tag_with_description()
    {
        $docBlock = $this->reflector->reflectDocBlockOf(
            new ReflectionProperty(PhpDocAnnotations::class, 'property2')
        );
        self::assertDocBlockHasUnqualifiedTag('var', $docBlock);
        self::assertDocBlockHasAnnotationWithTagAndAttributes(
            'var',
            ['type' => new String_(), 'description' => 'with description'],
            $docBlock
        );
    }

    /** @test */
    public function it_reflects_the_return_tag_with_a_description()
    {
        $docBlock = $this->reflector->reflectDocBlockOf(
            new ReflectionMethod(PhpDocAnnotations::class, 'anotherExampleFunction')
        );
        self::assertDocBlockHasUnqualifiedTag('return', $docBlock);
        self::assertDocBlockHasAnnotationWithTagAndAttributes(
            'return',
            ['type' => new Void_(), 'description' => 'will return nothing'],
            $docBlock
        );
    }

    /** @test */
    public function it_reflects_the_return_tag_without_a_description()
    {
        $docBlock = $this->reflector->reflectDocBlockOf(
            new ReflectionMethod(PhpDocAnnotations::class, 'exampleFunction')
        );
        self::assertDocBlockHasUnqualifiedTag('return', $docBlock);
        self::assertDocBlockHasAnnotationWithTagAndAttributes(
            'return',
            ['type' => new Void_(), 'description' => ''],
            $docBlock
        );
    }

    /** @test */
    public function it_reflects_the_throws_tag_with_and_without_a_description()
    {
        $docBlock = $this->reflector->reflectDocBlockOf(
            new ReflectionMethod(PhpDocAnnotations::class, 'exampleFunction')
        );
        self::assertDocBlockHasUnqualifiedTag('throws', $docBlock);
        self::assertDocBlockHasAnnotationWithTagAndAttributes(
            'throws',
            ['type' => new Object_(new Fqsen('\\RuntimeException')), 'description' => 'with description'],
            $docBlock
        );
        self::assertDocBlockHasAnnotationWithTagAndAttributes(
            'throws',
            ['type' => new Object_(new Fqsen('\\RuntimeException')), 'description' => ''],
            $docBlock
        );
    }

    /** @test */
    public function it_reflects_the_param_tag_with_and_without_a_description()
    {
        $docBlock = $this->reflector->reflectDocBlockOf(
            new ReflectionMethod(PhpDocAnnotations::class, 'exampleFunction')
        );
        self::assertDocBlockHasUnqualifiedTag('param', $docBlock);
        self::assertDocBlockHasAnnotationWithTagAndAttributes(
            'param',
            ['type' => new String_(), 'name' => '$param1', 'description' => ''],
            $docBlock
        );
        self::assertDocBlockHasAnnotationWithTagAndAttributes(
            'param',
            ['type' => new String_(), 'name' => '$param2', 'description' => 'with description'],
            $docBlock
        );
    }
}
