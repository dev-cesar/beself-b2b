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

use Beself\CustomerB2b\Api\IsDistributorRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class IsDistributorRepository implements IsDistributorRepositoryInterface
{
    public const CUSTOMER_GROUP_TABLE = 'customer_group';
    public const IS_DISTRIBUTOR_ATTR_KEY = 'is_distributor';

    /**
     * @param ResourceConnection $connection
     */
    public function __construct(
        readonly ResourceConnection $connection
    ) {
    }

    /**
     * Get extension attribute from Customer Group Table
     *
     * @param int $customerGroupId
     * @return int
     */
    public function loadIsDistributorFromCustomerGroupId(
        int $customerGroupId
    ): int {
        $connection = $this->connection->getConnection();
        $bind = ['customer_group_id' => $customerGroupId];

        $select = $connection->select()->from(
            self::CUSTOMER_GROUP_TABLE,
            [self::IS_DISTRIBUTOR_ATTR_KEY]
        )->where(
            'customer_group_id = :customer_group_id'
        );

        return (int)$connection->fetchOne($select, $bind);
    }
}
