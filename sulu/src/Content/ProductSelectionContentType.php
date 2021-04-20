<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class ProductSelectionContentType extends SimpleContentType
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        parent::__construct('product_selection');

        $this->productRepository = $productRepository;
    }

    /**
     * @return Product[]
     */
    public function getContentData(PropertyInterface $property): array
    {
        $ids = $property->getValue();

        $products = [];
        foreach ($ids ?: [] as $id) {
            $product = $this->productRepository->findById((int)$id);
            if ($product) {
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * @return mixed[]
     */
    public function getViewData(PropertyInterface $property): array
    {
        return (!$property->getValue()) ? [] :$property->getValue();
    }
}