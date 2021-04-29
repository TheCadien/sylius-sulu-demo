<?php

namespace App\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sulu\Bundle\SyliusConsumerBundle\Adapter\ProductAdapterInterface;
use Sulu\Bundle\SyliusConsumerBundle\Payload\ProductPayload;

class ProductAdapter implements ProductAdapterInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function synchronize(ProductPayload $payload): void
    {
        $product = $this->productRepository->findOneBy(['code' => $payload->getCode()]);
        if (!$product) {
            $product = new Product();
            $product->setCode($payload->getCode());
        }

        $product->setName($payload->getTranslations()['de_de']->getName());
        $product->setPrice($payload->getVariants()[0]->getPayload()->getArrayValue('channelPricings')['FASHION_WEB']['price'] / 100);

        $this->productRepository->save($product);
    }

    public function remove(string $code): void
    {
        $product = $this->productRepository->findOneBy(['code' => $code]);
        if (!$product) {
            return;
        }

        $this->productRepository->remove($product->getId());
    }
}
