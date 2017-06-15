<?php
declare(strict_types=1);

namespace Dkplus\Reflection\DocBlock;

use Dkplus\Reflection\Exception\ParserException;
use Hoa\Compiler\Llk\TreeNode;
use Hoa\Visitor\Element;
use Hoa\Visitor\Visit;
use phpDocumentor\Reflection\Types\Context;
use RuntimeException;

final class AnnotationVisitor implements Visit
{
    /** @var AnnotationFactory */
    private $annotationFactory;

    /** @var Context */
    private $context;

    public function __construct(AnnotationFactory $factory, Context $context)
    {
        $this->annotationFactory = $factory;
        $this->context = $context;
    }

    /**
     * @param TreeNode $element
     */
    public function visit(Element $element, &$handle = null, $eldnah = null)
    {
        $id = $element->getId();
        switch ($id) {
            case '#docblock':
                return $this->visitDocBlock($element, $handle, $eldnah);
            case '#values':
                return $this->visitValues($element, $handle, $eldnah);
            case '#pairs':
                return $this->visitPairs($element, $handle, $eldnah);
            case '#list':
                return $this->visitList($element, $handle, $eldnah);
            case '#annotations':
                return $this->visitAnnotations($element, $handle, $eldnah);
            case '#annotation':
                return $this->visitAnnotation($element, $handle, $eldnah);
            case '#value':
                return $this->visitValue($element, $handle, $eldnah);
            case '#map':
                return $this->visitMap($element, $handle, $eldnah);
            case '#pair':
                return $this->visitPair($element, $handle, $eldnah);
            case '#constant':
                return $this->visitConstant($element, $handle, $eldnah);
            case 'token':
                return $this->visitToken($element, $handle, $eldnah);
        }
        throw new RuntimeException("Unknown AST node: $id");
    }

    private function visitDocBlock(TreeNode $element, $handle, $eldnah)
    {
        $result = [];
        /* @var TreeNode $child */
        foreach ($element->getChildren() as $child) {
            // ignore comments
            if ($child->getid() !== '#annotations') {
                continue;
            }
            $annots = $child->accept($this, $handle, $eldnah);
            $result = array_merge($result, $annots);
        }
        return $result;
    }

    private function visitValues(TreeNode $element, $handle, $eldnah)
    {
        $values = [];
        /* @var $child TreeNode */
        foreach ($element->getChildren() as $child) {
            $result = $child->accept($this, $handle, $eldnah);
            $values = ((array) $values) + ((array) $result);
            // array_merge won't preserve numeric keys
        }
        return $values;
    }

    private function visitPairs(TreeNode $element, $handle, $eldnah)
    {
        $pairs = [];
        /* @var $child TreeNode */
        foreach ($element->getChildren() as $child) {
            $pair = $child->accept($this, $handle, $eldnah);
            $key = key($pair);
            $val = $pair[$key];
            $pairs[$key] = $val;
        }
        return $pairs;
    }

    private function visitList(TreeNode $element, $handle, $eldnah): array
    {
        $list = [];
        /* @var $child TreeNode */
        foreach ($element->getChildren() as $child) {
            $list[] = $child->accept($this, $handle, $eldnah);
        }
        return $list;
    }

    private function visitAnnotations(TreeNode $element, $handle, $eldnah): array
    {
        $annotations = [];
        /* @var $child TreeNode */
        foreach ($element->getChildren() as $child) {
            $annotations[] = $child->accept($this, $handle, $eldnah);
        }
        return array_filter($annotations);
    }

    private function visitAnnotation(TreeNode $element, $handle, $eldnah)
    {
        $class = $element->getChild(0)->accept($this, $handle, $eldnah);
        $values = $element->childExists(1)
            ? $this->visitValues($element->getChild(1), $handle, $eldnah)
            : [];
        return $this->annotationFactory->createReflection($class, $values, $this->context);
    }

    private function visitValue(TreeNode $element, $handle, $eldnah)
    {
        return $element->getChild(0)->accept($this, $handle, $eldnah);
    }

    private function visitMap(TreeNode $element, $handle, $eldnah)
    {
        return $element->getChild(0)->accept($this, $handle, $eldnah);
    }

    private function visitPair(TreeNode $element, $handle, $eldnah)
    {
        $key = $element->getChild(0)->accept($this, $handle, $eldnah);
        $val = $element->getChild(1)->accept($this, $handle, $eldnah);
        return [$key => $val];
    }

    private function visitConstant(TreeNode $element, $handle, $eldnah)
    {
        $identifier = $element->getChild(0)->accept($this, $handle, $eldnah);
        $property = $element->childExists(3)
            ? $element->getChild(3)->accept($this, $handle, $eldnah)
            : null;
        if (! $property) {
            if (! defined($identifier)) {
                throw ParserException::invalidConstant($identifier);
            }
            return constant($identifier);
        }
        $class = $this->resolveClass($identifier);
        $name = $class . '::' . $property;
        if ((strtolower($property) === 'class')) {
            return $class;
        }
        if (! defined($name)) {
            throw ParserException::invalidConstant($name);
        }
        return constant($name);
    }

    private function visitToken(TreeNode $element, $handle, $eldnah)
    {
        $token = $element->getValueToken();
        $value = $element->getValueValue();
        if ($token === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if ($token === 'number' && (strpos($value, '.') !== false || stripos($value, 'e') !== false)) {
            return filter_var($value, FILTER_VALIDATE_FLOAT);
        }
        if ($token === 'number') {
            return filter_var($value, FILTER_VALIDATE_INT);
        }
        if ($token === 'string') {
            return $this->visitStringValue($value);
        }
        if ($token === 'null') {
            return null;
        }
        return $value;
    }

    /**
     * Visit a string value.
     *
     * @param string $value
     *
     * @return string
     */
    private function visitStringValue(string $value): string
    {
        $string = substr($value, 1, -1);
        return str_replace('\"', '"', $string);
    }
}
