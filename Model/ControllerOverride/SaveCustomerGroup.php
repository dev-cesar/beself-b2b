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

use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Controller\Adminhtml\Group\Save as DefaultSaveController;

class SaveCustomerGroup extends DefaultSaveController
{
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Customer\Api\Data\GroupExtensionInterfaceFactory
     */
    private $groupExtensionInterfaceFactory;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param GroupRepositoryInterface $groupRepository
     * @param GroupInterfaceFactory $groupDataFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Customer\Api\Data\GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        GroupRepositoryInterface $groupRepository,
        GroupInterfaceFactory $groupDataFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Customer\Api\Data\GroupExtensionInterfaceFactory $groupExtensionInterfaceFactory
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->groupExtensionInterfaceFactory = $groupExtensionInterfaceFactory
            ?: ObjectManager::getInstance()->get(\Magento\Customer\Api\Data\GroupExtensionInterfaceFactory::class);
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
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $taxClass = (int)$this->getRequest()->getParam('tax_class');

        /** @var \Magento\Customer\Api\Data\GroupInterface $customerGroup */
        $customerGroup = null;
        if ($taxClass) {
            $id = $this->getRequest()->getParam('id');
            $websitesToExclude = empty($this->getRequest()->getParam('customer_group_excluded_websites'))
                ? [] : $this->getRequest()->getParam('customer_group_excluded_websites');
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

                if ($websitesToExclude !== null) {
                    $customerGroupExtensionAttributes = $this->groupExtensionInterfaceFactory->create();
                    $customerGroupExtensionAttributes->setExcludeWebsiteIds($websitesToExclude);
                    $customerGroup->setExtensionAttributes($customerGroupExtensionAttributes);
                }

                $this->groupRepository->save($customerGroup);

                $this->messageManager->addSuccessMessage(__('You saved the customer group.'));
                $resultRedirect->setPath('customer/group');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($customerGroup != null) {
                    $this->storeCustomerGroupDataToSession(
                        $this->dataObjectProcessor->buildOutputDataArray(
                            $customerGroup,
                            \Magento\Customer\Api\Data\GroupInterface::class
                        )
                    );
                }
                $resultRedirect->setPath('customer/group/edit', ['id' => $id]);
            }
            return $resultRedirect;
        } else {
            return $this->resultForwardFactory->create()->forward('new');
        }
    }

}
