<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use FOS\RestBundle\View\View;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("product")
 */
class ProductController extends AbstractRestController implements ClassResourceInterface
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var FieldDescriptorFactoryInterface
     */
    private $fieldDescriptorFactory;

    /**
     * @var DoctrineListBuilderFactoryInterface
     */
    private $listBuilderFactory;

    /**
     * @var RestHelperInterface
     */
    private $restHelper;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        DoctrineListBuilderFactoryInterface $listBuilderFactory,
        RestHelperInterface $restHelper,
        ProductRepository $productRepository,
        ?TokenStorageInterface $tokenStorage = null
    )
    {
        $this->viewHandler = $viewHandler;
        $this->fieldDescriptorFactory = $fieldDescriptorFactory;
        $this->listBuilderFactory = $listBuilderFactory;
        $this->restHelper = $restHelper;
        $this->productRepository = $productRepository;

        parent::__construct($viewHandler, $tokenStorage);
    }

    public function cgetAction(): Response
    {
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(Product::RESOURCE_KEY);
        $listBuilder = $this->listBuilderFactory->create(Product::class);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        $listRepresentation = new PaginatedRepresentation(
            $listBuilder->execute(),
            Product::RESOURCE_KEY,
            $listBuilder->getCurrentPage(),
            $listBuilder->getLimit(),
            $listBuilder->count()
        );

        return $this->viewHandler->handle(View::create($listRepresentation));
    }

    public function getAction(int $id, Request $request): Response
    {
        $entity = $this->productRepository->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($entity));
    }

    public function putAction(int $id, Request $request): Response
    {
        /** @var Product $entity */
        $entity = $this->productRepository->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEntity($request->request->all(), $entity);

        $this->productRepository->save($entity);

        return $this->handleView($this->view($entity));
    }

    public function deleteAction(int $id): Response
    {
        $this->productRepository->remove($id);

        return $this->handleView($this->view());
    }

    public function postAction(Request $request): Response
    {
        $entity = $this->productRepository->create();

        $this->mapDataToEntity($request->request->all(), $entity);

        $this->productRepository->save($entity);

        return $this->handleView($this->view($entity));
    }

    public function mapDataToEntity(array $data, Product $product): Product
    {
        $product->setCode($data['code']);
        $product->setName($data['name']);
        $product->setPrice($data['price']);

        return $product;
    }
}
