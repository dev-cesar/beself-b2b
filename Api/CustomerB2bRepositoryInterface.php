<?php declare(strict_types=1);
/**
 * Beself_CustomerB2b
 *
 * @category  Beself
 * @package   Beself_CustomerB2b
 * @copyright Copyright © 2023. All rights reserved.
 * @author    cesarhndev@gmail.com
 */
namespace Beself\CustomerB2b\Api;

interface CustomerB2bRepositoryInterface
{
    /**
     * Get is current customer B2B
     *
     * @return bool
     */
    public function getIsCurrentCustomerB2b(): bool;

    /**
     * Get favorite product for current customer
     *
     * @return string
     */
    public function getFavoriteProductForCurrentCustomer(): string;
}
