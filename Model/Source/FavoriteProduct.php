<?php declare(strict_types=1);
/**
 * Beself_CustomerB2b
 *
 * @category  Beself
 * @package   Beself_CustomerB2b
 * @copyright Copyright © 2023. All rights reserved.
 * @author    cesarhndev@gmail.com
 */
namespace Beself\CustomerB2b\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class FavoriteProduct extends AbstractSource
{
    public const CARDIO_LABEL = 'Cardio';
    public const CARDIO_VALUE = 1;
    public const YOGA_LABEL = 'Yoga';
    public const YOGA_VALUE = 2;
    public const MUSCULATION_LABEL = 'Musculation';
    public const MUSCULATION_VALUE = 3;

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = [
                [
                    'label' => __(''),
                    'value' => ''
                ],
                [
                    'label' => __(self::CARDIO_LABEL),
                    'value' => self::CARDIO_VALUE
                ],
                [
                    'label' => __(self::YOGA_LABEL),
                    'value' => self::YOGA_VALUE
                ],
                [
                    'label' => __(self::MUSCULATION_LABEL),
                    'value' => self::MUSCULATION_VALUE
                ],
            ];
        }

        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray(): array
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value): bool|string
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] === $value) {
                return $option['label'];
            }
        }

        return false;
    }
}
