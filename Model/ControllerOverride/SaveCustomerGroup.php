<?php
/**
 * SaveCopy
 * @category  Vivadogs
 * @package   Vivadogs_Module
 * @copyright Copyright © 2023 Vivadogs. All rights reserved.
 * @author    cesar.hernandez@vivadogs.com
 * @link      https://www.vivadogs.com/
 */

namespace Beself\CustomerB2b\Model\ControllerOverride;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Customer\Api\Data\GroupExtensionInterfaceFactory;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Controller\Adminhtml\Group\Save as DefaultSaveController;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class SaveCustomerGroup
 *
 * Override native controller to set up custom extension_attribute Is Distributor
 */
class SaveCustomerGroup extends DefaultSaveController
{
    /**
     * @var GroupExtensionInterfaceFactory
     */
    private GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory;

    /**
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param GroupRepositoryInterface $groupRepository
     * @param GroupInterfaceFactory $groupDataFactory
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupDataFactory,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        DataObjectProcessor $dataObjectProcessor,
        GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory
    ) {
        $this->groupExtensionInterfaceFactory = $groupExtensionInterfaceFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $groupRepository,
            $groupDataFactory,
            $resultForwardFactory,
            $resultPageFactory,
            $dataObjectProcessor,
            $groupExtensionInterfaceFactory
        );
    }
    /**
     * Create or save customer group.
     *
     * @return Forward|Redirect
     */
    public function execute()
    {
        $taxClass = (int)$this->getRequest()->getParam('tax_class');

        /** @var GroupInterface $customerGroup */
        $customerGroup = null;
        if ($taxClass) {
            $id = $this->getRequest()->getParam('id');
            $isDistributor = $this->getRequest()->getParam('is_distributor');

            $resultRedirect = $this->resultRedirectFactory->create();
            try {
                $customerGroupCode = (string)$this->getRequest()->getParam('code');

                if ($id !== null) {
                    $customerGroup = $this->groupRepository->getById((int)$id);
                    $customerGroupCode = $customerGroupCode ?: $customerGroup->getCode();
                } else {
                    $customerGroup = $this->groupDataFactory->create();
                }
                $customerGroup->setCode(!empty($customerGroupCode) ? $customerGroupCode : null);
                $customerGroup->setTaxClassId($taxClass);

                if ($isDistributor !== null) {
                    $customerGroupExtensionAttributes = $this->groupExtensionInterfaceFactory->create();
                    $customerGroupExtensionAttributes->setIsDistributor($isDistributor);
                    $customerGroup->setExtensionAttributes($customerGroupExtensionAttributes);
                }

                $this->groupRepository->save($customerGroup);

                $this->messageManager->addSuccessMessage(__('You saved the customer group.'));
                $resultRedirect->setPath('customer/group');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($customerGroup != null) {
                    $this->storeCustomerGroupDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $customerGroup,
                            GroupInterface::class
                        )
                    );
                }
                $resultRedirect->setPath('customer/group/edit', ['id' => $id]);
            }
            return $resultRedirect;
        }

        return $this->resultForwardFactory->create()->forward('new');
    }

}
