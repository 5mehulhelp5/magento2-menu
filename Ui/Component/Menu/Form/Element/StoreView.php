<?php

declare(strict_types=1);

namespace Snowdog\Menu\Ui\Component\Menu\Form\Element;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

class StoreView implements ArrayInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function toOptionArray(): array
    {
        $stores = $this->storeManager->getStores();

        $options = [];
        foreach ($stores as $store) {
            $options[] = [
                'label' => $store->getName(),
                'value' => $store->getId(),
            ];
        }

        return $options;
    }
}