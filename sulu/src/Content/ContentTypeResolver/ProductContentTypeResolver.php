<?php


namespace App\Content\ContentTypeResolver;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Bundle\HeadlessBundle\Content\ContentView;
use Sulu\Bundle\HeadlessBundle\Content\ContentTypeResolver\ContentTypeResolverInterface;


class ProductContentTypeResolver implements ContentTypeResolverInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;


    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;

    }

    public static function getContentType(): string
    {
        return 'product_selection';
    }

    public function resolve($data, PropertyInterface $property, string $locale, array $attributes = []): ContentView
    {
        if (empty($data)) {
            return new ContentView([], ['ids' => []]);
        }

        $content = [];
        /** @var Product $product */
        foreach ($data as $productId) {
            $product = $this->productRepository->findById($productId);
            if (!$product) {
                continue;
            }

            $content[] = [
                'name' => $product->getName(),
                'code' => $product->getCode(),
                'price' => $product->getPrice()
            ];
        }

        return new ContentView($content, ['ids' => $data ?: []]);
    }

}
