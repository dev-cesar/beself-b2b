<?php
/**
 * InstallData
 * @category  Vivadogs
 * @package   Vivadogs_Module
 * @copyright Copyright © 2023 Vivadogs. All rights reserved.
 * @author    cesar.hernandez@vivadogs.com
 * @link      https://www.vivadogs.com/
 */

namespace Beself\CustomerB2b\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Beself\CustomerB2b\Model\Source\FavoriteProduct;

class InstallFavoriteProductAttribute implements DataPatchInterface
{
    public const FAVORITE_PRODUCT_ATTR_CODE = 'favorite_product';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        readonly ModuleDataSetupInterface $moduleDataSetup,
        readonly CustomerSetupFactory $customerSetupFactory,
        readonly AttributeSetFactory $attributeSetFactory
    ) {
    }

    /**
     * Add eav attributes
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(
            [
                'setup' => $this->moduleDataSetup
            ]
        );
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $customerSetup->addAttribute(
            Customer::ENTITY,
            self::FAVORITE_PRODUCT_ATTR_CODE,
            [
                'type' => 'int',
                'label' => 'Favorite Product',
                'source' => FavoriteProduct::class,
                'input' => 'select',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 100,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true,
                'system' => 0,
            ]
        );

        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(
                Customer::ENTITY,
                self::FAVORITE_PRODUCT_ATTR_CODE
            )->addData(
                [
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer']
                ]
            );

        $attribute->save();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return void
     */
    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(
            ['setup' => $this->moduleDataSetup]
        );
        $customerSetup->removeAttribute(Customer::ENTITY, self::FAVORITE_PRODUCT_ATTR_CODE);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get dependencies
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get Aliases
     */
    public function getAliases(): array
    {
        return [];
    }
}
