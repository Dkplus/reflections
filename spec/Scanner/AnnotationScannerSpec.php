<?php
namespace spec\Dkplus\Reflection\Scanner;

use Dkplus\Reflection\Annotations;
use Dkplus\Reflection\Scanner\AnnotationScanner;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use PhpSpec\ObjectBehavior;

/**
 * @mixin AnnotationScanner
 */
class AnnotationScannerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AnnotationScanner::class);
    }

    function it_provides_the_annotations_of_a_doc_block()
    {
        $docBlock = <<<'CODE'
/**
 * Some text
 *
 * Some more text
 * @Annotation\Attributes(value={
 *  @Annotation\Attribute("42", name="foo", type="int"),
 *  @Annotation\Attribute("BAR", name="bar", type="string")
 * })
 */
CODE;
        $expected = new Attributes();
        $foo = new Attribute();
        $foo->name = 'foo';
        $foo->type = 'int';
        $bar = new Attribute();
        $bar->name = 'bar';
        $bar->type = 'string';
        $expected->value = [$foo, $bar];

        $this
            ->scanForAnnotations(
                $docBlock,
                '/var/www/src/MyFile.php',
                ['Annotation' => 'Doctrine\\Common\\Annotations\\Annotation']
            )
            ->shouldBeLike(new Annotations([$expected]));
    }

    function it_ignores_phpdoc_tags()
    {
        $docBlock = <<<'CODE'
/**
 * @var string
 * @param test
 * @return bool
 * @see
 */
CODE;

        $this
            ->scanForAnnotations($docBlock, '/var/www/src/MyFile.php', [])
            ->shouldBeLike(new Annotations([]));
    }
}
