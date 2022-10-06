<?php

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;


class AddSimpleProduct implements DataPatchInterface
{
    protected ModuleDataSetupInterface $setup;

    protected ProductInterfaceFactory $productInterfaceFactory;

    protected ProductRepositoryInterface $productRepository;

    protected State $appState;

    protected EavSetup $eavSetup;

    protected StoreManagerInterface $storeManager;

    protected SourceItemsSaveInterface $sourceItemsSaveInterface;

    protected CategoryLinkManagementInterface $categoryLink;

    protected array $sourceItems = [];

    public function __construct(
        ModuleDataSetupInterface $setup,
        ProductInterfaceFactory $productInterfaceFactory,
        ProductRepositoryInterface $productRepository,
        State $appState,
        StoreManagerInterface $storeManager,
        EavSetup $eavSetup,
        SourceItemsSaveInterface $sourceItemsSaveInterface,
        CategoryLinkManagementInterface $categoryLink
    ) {
        $this->appState = $appState;
        $this->productInterfaceFactory = $productInterfaceFactory;
        $this->productRepository = $productRepository;
        $this->setup = $setup;
        $this->eavSetup = $eavSetup;
        $this->storeManager = $storeManager;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
        $this->categoryLink = $categoryLink;
    }

    public function apply()
    {
        // run setup in back-end area
        $this->appState->emulateAreaCode('adminhtml', [$this, 'execute']);
    }

    public function execute()
    {
        // create the product
        $product = $this->productInterfaceFactory->create();


        // get the attribute set id from EavSetup object
        $attributeSetId = $this->eavSetup->getAttributeSetId(Product::ENTITY, 'Default');

        // set attributes
        $product->setTypeId(Type::TYPE_SIMPLE)
            ->setAttributeSetId($attributeSetId)
            ->setName('Test Sample Product')
            ->setSku('sample1')
            ->setPrice(100)
            ->setVisibility(Visibility::VISIBILITY_BOTH)
            ->setStatus(Status::STATUS_ENABLED);


        // save the product to the repository
        $product = $this->productRepository->save($product);

        // added to the mens category
        $this->categoryLink->assignProductToCategories($product->getSku(), [3]);
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
