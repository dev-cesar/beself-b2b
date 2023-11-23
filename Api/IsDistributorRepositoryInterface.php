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

interface IsDistributorRepositoryInterface
{
    /**
     * Get extension attribute from Customer Group Table
     *
     * @param int $customerGroupId
     * @return int
     */
    public function loadIsDistributorFromCustomerGroupId(int $customerGroupId): int;
}
