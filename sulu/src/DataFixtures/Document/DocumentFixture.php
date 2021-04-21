<?php

namespace App\DataFixtures\Document;

use App\DataFixtures\AppFixtures;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use RuntimeException;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\PageBundle\Document\HomeDocument;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\Content\Document\RedirectType;
use Sulu\Component\Content\Document\WorkflowStage;
use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Component\PHPCR\PathCleanupInterface;

class DocumentFixture implements DocumentFixtureInterface
{
    /**
     * @var PathCleanupInterface
     */
    private $pathCleanup;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(PathCleanupInterface $pathCleanup, EntityManagerInterface $entityManager)
    {
        $this->pathCleanup = $pathCleanup;
        $this->entityManager = $entityManager;
    }

    public function getOrder()
    {
        return 10;
    }

    public function load(DocumentManager $documentManager): void
    {
        $this->loadPages($documentManager);
        $this->loadHomepage($documentManager);

        $documentManager->flush();
    }

    private function loadPages(DocumentManager $documentManager): array
    {
        $pageDataList = [
            [
                'title' => 'Books',
                'navigationContexts' => ['main'],
                'structureType' => 'default',
            ],
            [
                'title' => 'imprint',
                'navigationContexts' => ['footer'],
                'structureType' => 'default',
                'content' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.'
            ],
            [
                'title' => 'Conditions',
                'navigationContexts' => ['footer'],
                'structureType' => 'default',
                'content' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.'
            ],
            [
                'title' => 'Sulu Book',
                'navigationContexts' => ['main'],
                'structureType' => 'productPresentation',
                'text' => 'The best book in the world!',
                'products' => [1],
                'image' => ['id' => $this->getMediaId('Camera.jpg')],
                'parent_path' => '/cmf/example/contents/books'
            ],
            [
                'title' => 'Symfony Book',
                'navigationContexts' => ['main'],
                'structureType' => 'productPresentation',
                'text' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore',
                'products' => [2],
                'image' => ['id' => $this->getMediaId('Headphones.jpg')],
                'parent_path' => '/cmf/example/contents/books'
            ],
            [
                'title' => 'Holy Bible',
                'navigationContexts' => ['main'],
                'structureType' => 'productPresentation',
                'text' => 'Fucking Holy!',
                'products' => [3],
                'image' => ['id' => $this->getMediaId('Shoe.jpg')],
                'parent_path' => '/cmf/example/contents/books'
            ]
        ];

        $pages = [];
        foreach ($pageDataList as $pageData) {
            $pages[$pageData['title']] = $this->createPage($documentManager, $pageData);
        }

        return $pages;
    }

    private function loadHomepage(DocumentManager $documentManager): void
    {
        /** @var HomeDocument $homeDocument */
        $homeDocument = $documentManager->find('/cmf/example/contents', AppFixtures::LOCALE);

        $homeDocument->setTitle('Great Shop');
        $homeDocument->getStructure()->bind(
            [
                'title' => $homeDocument->getTitle(),
                'url' => '/',
                'text' => 'Welcome to our Book Shop',
                'products' => [1, 2, 3],
                'headerImages' => [
                    'ids' => [
                        $this->getMediaId('Camera.jpg'),
                        $this->getMediaId('Headphones.jpg'),
                        $this->getMediaId('Shoe.jpg'),
                    ],
                ],
            ]
        );

        $documentManager->persist($homeDocument, AppFixtures::LOCALE);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE);
    }

    private function createPage(DocumentManager $documentManager, array $data): PageDocument
    {
        if (!isset($data['url'])) {
            $url = $this->pathCleanup->cleanup('/' . $data['title']);
            if (isset($data['parent_path'])) {
                $url = mb_substr($data['parent_path'], mb_strlen('/cmf/example/contents')) . $url;
            }

            $data['url'] = $url;
        }

        $extensionData = [
            'seo' => $data['seo'] ?? [],
            'excerpt' => $data['excerpt'] ?? [],
        ];

        unset($data['excerpt']);
        unset($data['seo']);

        /** @var PageDocument $pageDocument */
        $pageDocument = $documentManager->create('page');
        $pageDocument->setNavigationContexts($data['navigationContexts'] ?? []);
        $pageDocument->setLocale(AppFixtures::LOCALE);
        $pageDocument->setTitle($data['title']);
        $pageDocument->setResourceSegment($data['url']);
        $pageDocument->setStructureType($data['structureType'] ?? 'default');
        $pageDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $pageDocument->getStructure()->bind($data);
        $pageDocument->setAuthor(1);
        $pageDocument->setExtensionsData($extensionData);

        if (isset($data['redirect'])) {
            $pageDocument->setRedirectType(RedirectType::EXTERNAL);
            $pageDocument->setRedirectExternal($data['redirect']);
        }

        $documentManager->persist(
            $pageDocument,
            AppFixtures::LOCALE,
            ['parent_path' => $data['parent_path'] ?? '/cmf/example/contents']
        );

        // Set dataSource to current page after persist as uuid is before not available
        if (isset($data['pages']['dataSource']) && '__CURRENT__' === $data['pages']['dataSource']) {
            $pageDocument->getStructure()->bind(
                [
                    'pages' => array_merge(
                        $data['pages'],
                        [
                            'dataSource' => $pageDocument->getUuid(),
                        ]
                    ),
                ]
            );

            $documentManager->persist(
                $pageDocument,
                AppFixtures::LOCALE,
                ['parent_path' => $data['parent_path'] ?? '/cmf/example/contents']
            );
        }

        $documentManager->publish($pageDocument, AppFixtures::LOCALE);

        return $pageDocument;
    }

    private function getMediaId(string $name): int
    {
        try {
            $id = $this->entityManager->createQueryBuilder()
                ->from(Media::class, 'media')
                ->select('media.id')
                ->innerJoin('media.files', 'file')
                ->innerJoin('file.fileVersions', 'fileVersion')
                ->where('fileVersion.name = :name')
                ->setMaxResults(1)
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleScalarResult();

            return (int) $id;
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException(sprintf('Too many images with the name "%s" found.', $name), 0, $e);
        }
    }
}
