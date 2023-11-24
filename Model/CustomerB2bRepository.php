<?php declare(strict_types=1);
/**
 * Beself_CustomerB2b
 *
 * @category  Beself
 * @package   Beself_CustomerB2b
 * @copyright Copyright © 2023. All rights reserved.
 * @author    cesarhndev@gmail.com
 */
namespace Beself\CustomerB2b\Model;

use Beself\CustomerB2b\Api\CustomerB2bRepositoryInterface;
use Beself\CustomerB2b\Api\IsDistributorRepositoryInterface;
use Beself\CustomerB2b\Model\Source\FavoriteProduct;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;

class CustomerB2BRepository implements CustomerB2bRepositoryInterface
{
    public const FAVORITE_PRODUCT_ATTR_CODE = 'favorite_product';

    /**
     * @param Session $customerSession
     * @param IsDistributorRepositoryInterface $distributorRepository
     * @param FavoriteProduct $favoriteProductSource
     */
    public function __construct(
        readonly Session $customerSession,
        readonly IsDistributorRepositoryInterface $distributorRepository,
        readonly FavoriteProduct $favoriteProductSource
    ) {
    }

    public function getCurrentCustomer(): Customer
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * Get is current customer B2B
     *
     * @return bool
     */
    public function getIsCurrentCustomerB2b(): bool
    {
        return ($currentCustomer = $this->getCurrentCustomer())
            && $this->distributorRepository->loadIsDistributorFromCustomerGroupId((int)$currentCustomer->getGroupId());
    }

    /**
     * Get favorite product for current customer
     *
     * @return string
     */
    public function getFavoriteProductForCurrentCustomer(): string
    {
        if ($this->getIsCurrentCustomerB2b()) {
            $favoriteProductIdValue = (int)$this->getCurrentCustomer()->getData(self::FAVORITE_PRODUCT_ATTR_CODE);

            if ($favoriteProductLabel = $this->favoriteProductSource->getOptionText($favoriteProductIdValue)) {
                return $favoriteProductLabel;
            }
        }

        return '';
    }
}
