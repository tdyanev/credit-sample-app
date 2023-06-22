<?php

namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MaxCreditAmountValidator extends ConstraintValidator
{
    private $user;
    private $userMaxCredit;

    public function __construct(Security $security) {
        $this->user = $security->getUser();
        //$this->userMaxCredit = intval($userMaxCredit);
    }
    
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\MaxCreditAmount $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO I struggle to find a way to inject this param ;(
        if ($value + $this->user->getEntireCreditAmount() > 80000) {

            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }


}
