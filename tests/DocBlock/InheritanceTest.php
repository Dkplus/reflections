<?php
declare(strict_types=1);

namespace test\Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\DocBlock\ClassReflector\BuiltInClassReflector;
use Dkplus\Reflection\DocBlock\DocBlockReflector;
use phpDocumentor\Reflection\FqsenResolver;
use ReflectionClass;
use test\Dkplus\Reflection\DocBlock\Fixtures\Inheritance\ClassThatCouldNotInheritTheDescription;
use test\Dkplus\Reflection\DocBlock\Fixtures\Inheritance\ClassThatDoesNotInherit;
use test\Dkplus\Reflection\DocBlock\Fixtures\Inheritance\ClassThatInherits;
use test\Dkplus\Reflection\DocBlock\Fixtures\Inheritance\ClassWithInheritDoc;
use test\Dkplus\Reflection\DocBlock\TestCase\DocBlockTestCase;

/**
 * @covers AnnotationFactory
 * @covers DocBlockVisitor
 * @covers HoaParser
 */
class InheritanceTest extends DocBlockTestCase
{
    /** @var DocBlockReflector */
    private $reflector;

    protected function setUp()
    {
        $this->reflector = new DocBlockReflector(new BuiltInClassReflector(), new FqsenResolver());
    }

    /** @test */
    public function it_inherits_the_summary_for_classes_if_the_class_has_no_summary_but_the_parent_one_has()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatInherits::class));
        $overwrites = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatDoesNotInherit::class));

        self::assertSummaryEquals('My summary', $inherits);
        self::assertSummaryEquals('My own summary', $overwrites);
    }

    /** @test */
    public function it_inherits_the_description_for_classes_if_the_class_has_no_description_but_the_parent_one_has()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatInherits::class));
        $overwrites = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatDoesNotInherit::class));

        self::assertDescriptionEquals('My description.', $inherits);
        self::assertDescriptionEquals('My own description.', $overwrites);
    }

    /** @test */
    public function it_resolves_inheritDoc_in_description_if_one_class_docblock_contains_it_and_the_parent_one_has_a_description()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassWithInheritDoc::class));
        $couldNotInherit = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatCouldNotInheritTheDescription::class));

        self::assertDescriptionEquals('This is my description. My description. And here the show goes on.', $inherits);
        self::assertDescriptionEquals('This is my description. And here the show goes on.', $couldNotInherit);
    }

    /** @test */
    public function it_inherits_the_author_tag_for_classes_if_the_class_has_no_author_tag_but_the_parent_one_has()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatInherits::class));
        $overwrites = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatDoesNotInherit::class));

        self::assertDocBlockHasTag('author', $inherits);
        self::assertDocBlockHasAnnotationWithTagAndAttributes('author', ['name' => 'Erika Mustermann'], $overwrites);
    }

    /** @test */
    public function it_inherits_the_copyright_tag_for_classes_if_the_class_has_no_copyright_tag_but_the_parent_one_has()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatInherits::class));
        $overwrites = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatDoesNotInherit::class));

        self::assertDocBlockHasTag('copyright', $inherits);
        self::assertDocBlockHasAnnotationWithTagAndAttributes('copyright', ['description' => '2018'], $overwrites);
    }

    /** @test */
    public function it_inherits_the_package_tag_for_classes_if_the_class_has_no_package_tag_but_the_parent_one_has()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatInherits::class));
        $overwrites = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatDoesNotInherit::class));

        self::assertDocBlockHasTag('package', $inherits);
        self::assertDocBlockHasAnnotationWithTagAndAttributes('package', ['name' => 'ErikasPackage'], $overwrites);
    }

    /** @test */
    public function it_inherits_the_subpackage_tag_for_classes_only_if_the_class_has_no_package_nor_a_subpackage_tag()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatInherits::class));
        $overwrites = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatDoesNotInherit::class));

        self::assertDocBlockHasTag('subpackage', $inherits);
        self::assertDocBlockDoesNotHaveTag('subpackage', $overwrites);
    }
    /** @test */
    public function it_inherits_the_version_tag_for_classes_if_the_class_has_no_version_tag_but_the_parent_one_has()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatInherits::class));
        $overwrites = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatDoesNotInherit::class));

        self::assertDocBlockHasTag('version', $inherits);
        self::assertDocBlockHasAnnotationWithTagAndAttributes('version', ['description' => '2.1'], $overwrites);
    }

    /** @test */
    public function it_does_not_stupidly_inherit_all_annotations_for_classes()
    {
        $inherits = $this->reflector->reflectDocBlockOf(new ReflectionClass(ClassThatInherits::class));
        self::assertDocBlockDoesNotHaveTag('link', $inherits);
    }
}
