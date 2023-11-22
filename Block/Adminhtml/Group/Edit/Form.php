<?php declare(strict_types=1);
/**
 * Beself_CustomerB2b
 *
 * @category  Beself
 * @package   Beself_CustomerB2b
 * @copyright Copyright © 2023. All rights reserved.
 * @author    cesarhndev@gmail.com
 */

namespace Beself\CustomerB2b\Block\Adminhtml\Group\Edit;

use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Block\Adminhtml\Group\Edit\Form as CustomerGroupForm;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\TaxClass\Source\Customer;

/**
 * Class Form
 *
 * Customer Group Form customized
 */
class Form extends CustomerGroupForm
{
    /**
     * @var Customer
     */
    protected $_taxCustomer;

    /**
     * @var Data
     */
    protected $_taxHelper;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var GroupInterfaceFactory
     */
    protected $groupDataFactory;

    /**
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareLayout(): void
    {
        parent::_prepareLayout();

        $form = $this->_formFactory->create();
        $groupId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_GROUP_ID);

        if ($groupId === null) {
            $customerGroup = $this->groupDataFactory->create();
            $defaultCustomerTaxClass = $this->_taxHelper->getDefaultCustomerTaxClass();
        } else {
            $customerGroup = $this->_groupRepository->getById($groupId);
            $defaultCustomerTaxClass = $customerGroup->getTaxClassId();
        }

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Group Information')]);

        $validateClass = sprintf(
            'required-entry validate-length maximum-length-%d',
            GroupManagement::GROUP_CODE_MAX_LENGTH
        );
        $name = $fieldset->addField(
            'customer_group_code',
            'text',
            [
                'name' => 'code',
                'label' => __('Group Name'),
                'title' => __('Group Name'),
                'note' => __(
                    'Maximum length must be less then %1 characters.',
                    GroupManagement::GROUP_CODE_MAX_LENGTH
                ),
                'class' => $validateClass,
                'required' => true
            ]
        );

        if ($customerGroup->getId() == 0 && $customerGroup->getCode()) {
            $name->setDisabled(true);
        }

        $fieldset->addField(
            'tax_class_id',
            'select',
            [
                'name' => 'tax_class',
                'label' => __('Tax Class'),
                'title' => __('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $this->_taxCustomer->toOptionArray(),
            ]
        );

        //Is Distributor
        $isDistributorValue = 0;
        if ($customerGroup->getExtensionAttributes()
            && $customerGroup->getExtensionAttributes()->getIsDistributor()) {
            $isDistributorValue = $customerGroup->getExtensionAttributes()->getIsDistributor();
        }

        $fieldset->addField(
            'is_distributor',
            'checkbox',
            [
                'name'   => ' is_distributor ',
                'label' => __('Is Distributor'),
                'title' => __('Is Distributor'),
                'value' => $isDistributorValue,
                'onclick' => 'this.value = this.checked ? 1 : 0;',
                'checked' => $isDistributorValue,
                'tabindex' => 1
            ]
        );

        if ($customerGroup->getId() !== null) {
            // If edit add id
            $form->addField('id', 'hidden', ['name' => 'id', 'value' => $customerGroup->getId()]);
        }

        if ($this->_backendSession->getCustomerGroupData()) {
            $form->addValues($this->_backendSession->getCustomerGroupData());
            $this->_backendSession->setCustomerGroupData(null);
        } else {
            $form->addValues(
                [
                    'id' => $customerGroup->getId(),
                    'customer_group_code' => $customerGroup->getCode(),
                    'tax_class_id' => $defaultCustomerTaxClass,
                    'is_distributor' => $isDistributorValue,
                ]
            );
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('customer/*/save'));
        $form->setMethod('post');
        $this->setForm($form);
    }
}
