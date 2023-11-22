<?php declare(strict_types=1);
/**
 * Beself_CustomerB2b
 *
 * @category  Beself
 * @package   Beself_CustomerB2b
 * @copyright Copyright © 2023. All rights reserved.
 * @author    cesarhndev@gmail.com
 */
namespace Beself\CustomerB2b\Helper;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class IsDistributorHelper extends AbstractDb
{
    public const CUSTOMER_GROUP_TABLE = 'customer_group';
    public const IS_DISTRIBUTOR_ATTR_KEY = 'is_distributor';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::CUSTOMER_GROUP_TABLE, 'entity_id');
    }

    /**
     * SQL Query to get extension attribute from Customer Group Table
     *
     * @param int $customerGroupId
     * @return array
     */
    public function loadIsDistributorFromCustomerGroupId(int $customerGroupId): int
    {
        $connection = $this->getConnection();
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
