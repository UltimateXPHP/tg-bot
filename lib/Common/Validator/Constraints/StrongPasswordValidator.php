<?php

declare(strict_types=1);

namespace Lib\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class StrongPasswordValidator extends ConstraintValidator
{
    private const WEAK_PASSWORDS = [
        '12345',
        '123456',
        '123456789',
        'test1',
        'password',
        '12345678',
        'zinch',
        'g_czechout',
        'asdf',
        'qwerty',
        '1234567890',
        '1234567',
        'Aa123456.',
        'iloveyou',
        '1234',
        'abc123',
        '111111',
        '123123',
        'dubsmash',
        'test',
        'princess',
        'qwertyuiop',
        'sunshine',
        'BvtTest123',
        '11111',
        'ashley',
        '00000',
        '000000',
        'password1',
        'monkey',
        'livetest',
        '55555',
        'soccer',
        'charlie',
        'asdfghjkl',
        '654321',
        'family',
        'michael',
        '123321',
        'football',
        'baseball',
        'q1w2e3r4t5y6',
        'nicole',
        'jessica',
        'purple',
        'shadow',
        'hannah',
        'chocolate',
        'michelle',
        'daniel',
        'maggie',
        'qwerty123',
        'hello',
        '112233',
        'jordan',
        'tigger',
        '666666',
        '987654321',
        'superman',
        '12345678910',
        'summer',
        '1q2w3e4r5t',
        'fitness',
        'bailey',
        'zxcvbnm',
        'fuckyou',
        '121212',
        'buster',
        'butterfly',
        'dragon',
        'jennifer',
        'amanda',
        'justin',
        'cookie',
        'basketball',
        'shopping',
        'pepper',
        'joshua',
        'hunter',
        'ginger',
        'matthew',
        'abcd1234',
        'taylor',
        'samantha',
        'whatever',
        'andrew',
        '1qaz2wsx3edc',
        'thomas',
        'jasmine',
        'animoto',
        'madison',
        '0987654321',
        '54321',
        'flower',
        'Password',
        'maria',
        'babygirl',
        'lovely',
        'sophie',
        'Chegg123',
    ];

    /**
     * {@inheritdoc}
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!$constraint instanceof StrongPassword) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!in_array($value, self::WEAK_PASSWORDS)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation();
    }
}
