<?php

/**
 * Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Search;

/**
 * Class responsible for generating Magic values.
 */
final class MagicValue
{
    /**
     * Magic value wrapper identifier.
     */
    private const WRAPPER = '%%';

    /**
     * User info.
     *
     * @var array
     */
    private $user = [];

    /**
     * Field value.
     *
     * @var string
     */
    private $value = '';

    /**
     * Constructor method.
     *
     * @param string $value Field value
     * @param mixed[] $user User info
     * @return void
     */
    public function __construct(string $value, array $user)
    {
        if (empty($user)) {
            throw new \InvalidArgumentException('User info are required.');
        }

        $this->user = $user;
        $this->value = $value;
    }

    /**
     * Valid magic value detector.
     *
     * @param string $value Value
     * @return bool
     */
    public static function is(string $value): bool
    {
        $value = str_replace(static::WRAPPER, '', $value);

        return method_exists(MagicValue::class, $value);
    }

    /**
     * Magic value getter.
     *
     * @return string
     */
    public function get(): string
    {
        $value = str_replace(static::WRAPPER, '', $this->value);

        if (! method_exists($this, $value)) {
            return $this->value;
        }

        return $this->{$value}();
    }

    /**
     * Current user id magic value getter.
     *
     * @return string
     */
    private function me(): string
    {
        return $this->user['id'];
    }

    /**
     * Today's date magic value getter.
     *
     * @return string
     */
    private function today(): string
    {
        return (new \DateTimeImmutable('today'))->format('Y-m-d');
    }

    /**
     * Yesterday's date magic value getter.
     *
     * @return string
     */
    private function yesterday(): string
    {
        return (new \DateTimeImmutable('yesterday'))->format('Y-m-d');
    }

    /**
     * Tomorrow's date magic value getter.
     *
     * @return string
     */
    private function tomorrow(): string
    {
        return (new \DateTimeImmutable('tomorrow'))->format('Y-m-d');
    }
}
