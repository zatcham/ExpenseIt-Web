<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PhoneNumberTransformer implements DataTransformerInterface
{

    /**
     * @inheritDoc
     */
    public function transform(mixed $value)
    {
        if (null === $value) {
            return '';
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform(mixed $value)
    {
        if (null === $value) {
            return null;
        }
        $phoneNum = str_replace(' ', '', $value); // Remove spaces
        if (!str_starts_with($phoneNum, '+44')) {
            $phoneNum = '+44' . ltrim($phoneNum, '0'); // Remove leading 0 and replace with +44
        }

        return $phoneNum;
    }
}