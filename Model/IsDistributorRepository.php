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
use Magento\Customer\Model\Data\GroupFactory as ResourceGroupFactory;
use Magento\Framework\App\ResourceConnection;

class IsDistributorRepository implements IsDistributorRepositoryInterface
{
    public const CUSTOMER_GROUP_TABLE = 'customer_group';
    public const IS_DISTRIBUTOR_ATTR_KEY = 'is_distributor';
    public const CUSTOMER_GROUP_ID_KEY = 'customer_group_id';

    /**
     * @param ResourceConnection $connection
     * @param ResourceGroupFactory $resourceGroupFactory
     */
    public function __construct(
        readonly ResourceConnection $connection,
        readonly ResourceGroupFactory $resourceGroupFactory
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
        $bind = [self::CUSTOMER_GROUP_ID_KEY => $customerGroupId];

        $select = $connection->select()->from(
            self::CUSTOMER_GROUP_TABLE,
            [self::IS_DISTRIBUTOR_ATTR_KEY]
        )->where(
            self::CUSTOMER_GROUP_ID_KEY . ' = :' . self::CUSTOMER_GROUP_ID_KEY
        );

        return (int)$connection->fetchOne($select, $bind);
    }

    /**
     * @param int $customerGroupId
     * @param int $isDistributor
     */
    public function saveIsDistributor(
        int $customerGroupId,
        int $isDistributor
    ): void {
        $connection = $this->connection->getConnection();
        $connection->update(
            self::CUSTOMER_GROUP_TABLE,
            [self::IS_DISTRIBUTOR_ATTR_KEY => $isDistributor],
            [self::CUSTOMER_GROUP_ID_KEY . ' = ?' => $customerGroupId]
        );
    }
}
