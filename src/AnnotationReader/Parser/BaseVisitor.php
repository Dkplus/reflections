<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Parser;

use Dkplus\Reflection\AnnotationReader\Exception\ParserException;
use Dkplus\Reflection\AnnotationReader\Reference;
use Hoa\Visitor\Element;
use Hoa\Visitor\Visit;

abstract class BaseVisitor implements Visit
{
    /** @var bool */
    private $isNested = false;

    abstract protected function createAnnotation(Reference $reference);

    abstract protected function resolveClass(string $class) : string;

    public function visit(Element $element, &$handle = null, $eldnah = null)
    {
        $id = $element->getId();
        if ($id === '#dockblock') {
            $result = [];
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
        if ($id === '#values') {
            $values = [];
            foreach ($element->getChildren() as $child) {
                $result = $child->accept($this, $handle, $eldnah);
                $values = $values + $result;
                // array_merge won't preserve numeric keys
            }
            return $values;
        }
        if ($id === '#pairs') {
            $pairs = [];
            foreach ($element->getChildren() as $child) {
                $pair = $child->accept($this, $handle, $eldnah);
                $key  = key($pair);
                $val  = $pair[$key];
                $pairs[$key] = $val;
            }
            return $pairs;
        }
        if ($id === '#list') {
            $list = [];
            foreach ($element->getChildren() as $child) {
                $list[] = $child->accept($this, $handle, $eldnah);
            }
            return $list;
        }
        if ($id === '#annotations') {
            $annotations = [];
            foreach ($element->getChildren() as $child) {
                $annotations[] = $child->accept($this, $handle, $eldnah);
            }
            return array_filter($annotations);
        }
        if ($id === '#annotation') {
            return $this->visitAnnotation($element, $handle, $eldnah);
        }
        if ($id === '#value' || $id === '#map') {
            return $element->getChild(0)->accept($this, $handle, $eldnah);
        }
        if ($id === '#pair') {
            $key = $element->getChild(0)->accept($this, $handle, $eldnah);
            $val = $element->getChild(1)->accept($this, $handle, $eldnah);
            return [$key => $val];
        }
        if ($id === '#constant') {
            return $this->visitConstant($element, $handle, $eldnah);
        }
        if ($id === 'token') {
            return $this->visitToken($element, $handle, $eldnah);
        }
        throw new \RuntimeException("Unknown AST node: $id");
    }
    /**
     * Visit an annotation.
     *
     * @param \Hoa\Visitor\Element $element
     * @param mixed                $handle
     * @param mixed                $eldnah
     *
     * @return object
     */
    private function visitAnnotation(Element $element, &$handle = null, $eldnah = null)
    {
        $class  = $element->getChild(0)->accept($this, $handle, $eldnah);
        $values = $element->childExists(1)
            ? $this->visitValues($element->getChild(1), $handle, $eldnah)
            : [];
        return $this->createAnnotation(new Reference($class, $values, $this->isNested));
    }
    /**
     * Visit annotation values.
     *
     * @param \Hoa\Visitor\Element $element
     * @param mixed                $handle
     * @param mixed                $eldnah
     *
     * @return array
     */
    private function visitValues(Element $element, &$handle = null, $eldnah = null)
    {
        $children = $element->getChildren();
        $isNested = $this->isNested;
        $values   = [];
        $this->isNested = true;
        foreach ($children as $params) {
            foreach ($params->getChildren() as $child) {
                if ($child->getId() === '#pair') {
                    $key = $child->getChild(0)->accept($this, $handle, $eldnah);
                    $val = $child->getChild(1)->accept($this, $handle, $eldnah);
                    $values[$key] = $val;
                    continue;
                }
                if ( ! isset($values['value'])) {
                    $values['value'] = $child->accept($this, $handle, $eldnah);
                    continue;
                }
                if ( ! is_array($values['value'])) {
                    $values['value'] = [$values['value']];
                }
                $values['value'][] = $child->accept($this, $handle, $eldnah);
            }
        }
        $this->isNested = $isNested;
        return $values;
    }
    /**
     * Visit annotation constant.
     *
     * @param \Hoa\Visitor\Element $element
     * @param mixed                $handle
     * @param mixed                $eldnah
     *
     * @return mixed
     */
    private function visitConstant(Element $element, &$handle = null, $eldnah = null)
    {
        $identifier = $element->getChild(0)->accept($this, $handle, $eldnah);
        $property   = $element->childExists(3)
            ? $element->getChild(3)->accept($this, $handle, $eldnah)
            : null;
        if ( ! $property) {
            if ( ! defined($identifier)) {
                throw ParserException::invalidConstant($identifier);
            }
            return constant($identifier);
        }
        $class = $this->resolveClass($identifier);
        $name  = $class . '::' . $property;
        if ((strtolower($property) === 'class')) {
            return $class;
        }
        if ( ! defined($name)) {
            throw ParserException::invalidConstant($name);
        }
        return constant($name);
    }
    /**
     * Visit a token.
     *
     * @param \Hoa\Visitor\Element $element
     *
     * @return mixed
     */
    private function visitToken(Element $element)
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
    private function visitStringValue(string $value) : string
    {
        $string = substr($value, 1, -1);
        return str_replace('\"', '"', $string);
    }
}
