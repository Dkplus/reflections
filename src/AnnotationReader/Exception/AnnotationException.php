<?php
declare(strict_types=1);

namespace Dkplus\Reflection\AnnotationReader\Exception;

use Exception;

class AnnotationException extends Exception
{
    public static function optimizerPlusSaveComments() : AnnotationException
    {
        return new self(
            "You have to enable opcache.save_comments=1 or zend_optimizerplus.save_comments=1."
        );
    }

    public static function optimizerPlusLoadComments() : AnnotationException
    {
        return new self(
            "You have to enable opcache.load_comments=1 or zend_optimizerplus.load_comments=1."
        );
    }
}
