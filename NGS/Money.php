<?php
namespace NGS;

require_once(__DIR__.'/Utils.php');
require_once(__DIR__.'/BigDecimal.php');

/**
 * Represents money values
 * Money is BigDecimal with scale value fixed to 2
 *
 * @property string $value String representation of money value
 * @property int $scale Extended from NGS\BigDecimal scale, fixed to 2
 */
class Money extends \NGS\BigDecimal
{
    /**
     * @var string String representation of decimal value.
     */
    protected $value;

    protected $scale = 2;

    /**
     *
     * @param \NGS\Money|int|string|float $value
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        if (is_int($value)) {
            $this->setValue($value);
        }
        elseif (is_string($value) && preg_match('/^[-+]?\\d+([.]\\d{1,2})?$/u', $value)) {
            $this->setValue($value);
        }
        elseif (is_float($value)) {
            $value = (string) $value;
            $this->setValue($value);
        }
        elseif ($value instanceof \NGS\BigDecimal) {
            $this->setValue($value->value);
        }
        else {
            throw new \InvalidArgumentException('Money could not be constructed from invalid type "'.Utils::getType($value).'"');
        }
    }

    /**
     * Converts all elements in array to \NGS\Money instance
     *
     * @param array $items Source array, each element must be a valid argument for Money constructor
     * @param bool $allowNullValuesValues Allow elements with null value in array
     * @return array Resulting \NGS\Money array
     * @throws \InvalidArgumentException If any element is null or invalid type for Money constructor
     */
    public static function toArray(array $items, $allowNullValuesValues=false)
    {
        $results = array();
        try {
            foreach ($items as $key => $val) {
                if ($allowNullValuesValues && $val===null) {
                    $results[] = null;
                } elseif ($val === null) {
                    throw new \InvalidArgumentException('Null value found in provided array');
                } elseif (!$val instanceof \NGS\Money) {
                    $results[] = new \NGS\Money($val);
                } else {
                    $results[] = $val;
                }
            }
        }
        catch(\Exception $e) {
            throw new \InvalidArgumentException('Element at index '.$key.' could not be converted to BigDecimal!', 42, $e);
        }
        return $results;
    }
}