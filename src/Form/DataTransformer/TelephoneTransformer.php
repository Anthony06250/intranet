<?php

namespace App\Form\DataTransformer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class TelephoneTransformer implements DataTransformerInterface
{
    /**
     * @param RequestStack $request
     * @param PhoneNumberUtil $phoneNumberUtil
     */
    public function __construct(private RequestStack    $request,
                                private PhoneNumberUtil $phoneNumberUtil)
    {
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function transform(mixed $value): ?string
    {
        if ($value != null) {
            return $this->phoneNumberUtil->format($value, PhoneNumberFormat::NATIONAL);
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return PhoneNumber|null
     * @throws NumberParseException
     */
    public function reverseTransform(mixed $value): ?PhoneNumber
    {
        if ($value != '') {
            return $this->phoneNumberUtil->parse($value, $this->request->getCurrentRequest()->getLocale());
        }

        return null;
    }
}