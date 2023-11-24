<?php declare(strict_types=1);
/**
 * Beself_CustomerB2b
 *
 * @category  Beself
 * @package   Beself_CustomerB2b
 * @copyright Copyright © 2023. All rights reserved.
 * @author    cesarhndev@gmail.com
 */

namespace Beself\CustomerB2b\Block\Account;

use Beself\CustomerB2b\Api\CustomerB2bRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class CustomerB2bInfo extends Template
{
    public function __construct(
        readonly CustomerB2bRepositoryInterface $customerB2bRepository,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isB2bCustomer(): bool
    {
        return $this->customerB2bRepository->getIsCurrentCustomerB2b();
    }

    /**
     * @return string
     */
    public function getIsB2bCustomerLabel(): string
    {
        if ($this->customerB2bRepository->getIsCurrentCustomerB2b()) {
            return 'You are included in our B2B program.';
        }

        return 'You are not included in our B2B program';
    }

    /**
     * @return string
     */
    public function getB2bFavoriteProductLabel(): string
    {
        if ($this->customerB2bRepository->getIsCurrentCustomerB2b()) {
            return $this->customerB2bRepository->getFavoriteProductForCurrentCustomer();
        }

        return '';
    }
}
