<?php declare(strict_types=1);
/**
 * Beself_CustomerB2b
 *
 * @category  Beself
 * @package   Beself_CustomerB2b
 * @copyright Copyright © 2023. All rights reserved.
 * @author    cesarhndev@gmail.com
 */
namespace Beself\CustomerB2b\Plugin;

use Beself\CustomerB2b\Helper\IsDistributorHelper;
use Magento\Customer\Api\Data\GroupExtensionInterfaceFactory;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

class ManageIsDistributorForCustomerGroup
{
    /**
     * @param GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory
     * @param IsDistributorHelper $distributorHelper
     */
    public function __construct(
        readonly GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory,
        readonly IsDistributorHelper $distributorHelper
    ) {
    }

    /**
     * Add Is Distributor as extension attributes while getting customer group by id.
     *
     * @param GroupRepositoryInterface $subject
     * @param GroupInterface $result
     * @param int $id
     * @return GroupInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetById(
        GroupRepositoryInterface $subject,
        GroupInterface $result,
        int $id
    ): GroupInterface {

        $isDistributor = $this->distributorHelper->loadIsDistributorFromCustomerGroupId($id);
        if (!empty($isDistributor)) {
            $customerGroupExtensionAttributes = $this->groupExtensionInterfaceFactory->create();
            $customerGroupExtensionAttributes->setIsDistributor($isDistributor);
            $result->setExtensionAttributes($customerGroupExtensionAttributes);
        }

        return $result;
    }
}
