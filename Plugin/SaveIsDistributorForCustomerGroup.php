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

use Beself\CustomerB2b\Model\IsDistributorRepository;
use Magento\Catalog\Model\Indexer\Product\Price\Processor;
use Magento\Customer\Api\Data\GroupExtensionInterfaceFactory;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

class SaveIsDistributorForCustomerGroup
{
    /**
     * @param GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory
     * @param IsDistributorRepository $distributorRepository
     * @param Processor $priceIndexProcessor
     */
    public function __construct(
        readonly GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory,
        readonly IsDistributorRepository $distributorRepository,
        readonly Processor $priceIndexProcessor
    ) {
    }

    /**
     * Save extension attribute in table by direct SQL
     *
     * @TODO Check why resource model is not saving custom column
     * @param GroupRepositoryInterface $subject
     * @param GroupInterface $group
     * @return GroupInterface
     */
    public function afterSave(
        GroupRepositoryInterface $subject,
        GroupInterface $group,
    ): GroupInterface {
        if ($group->getExtensionAttributes() && $group->getExtensionAttributes()->getIsDistributor() !== null) {
            $groupId = $group->getId();
            $isDistributorValue = (int)$group->getExtensionAttributes()->getIsDistributor();
            $prevIsDistributorValue = $this->distributorRepository->loadIsDistributorFromCustomerGroupId($groupId);

            if ($prevIsDistributorValue !== $isDistributorValue) {
                $this->distributorRepository->saveIsDistributor(
                    $groupId,
                    $isDistributorValue
                );

                // Invalidate product price index because B2B customers use to have different prices
                $priceIndexer = $this->priceIndexProcessor->getIndexer();
                $priceIndexer->invalidate();
            }
        }

        return $group;
    }
}
