<?php


namespace App\DataFixtures\Document;


use App\DataFixtures\AppFixtures;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
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

    public function load(DocumentManager $documentManager): void
    {
        $this->loadPages($documentManager);
        $this->loadHomepage($documentManager);

        $documentManager->flush();
    }

    private function loadPages(DocumentManager $documentManager): void
    {
        $pageDataList = [
            [
                'title' => 'books',
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
                'product' => [1],
                'parent_path' => '/cmf/example/contents/books'
            ],
            [
                'title' => 'Symfony Book',
                'navigationContexts' => ['main'],
                'structureType' => 'productPresentation',
                'text' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore',
                'product' => [2],
                'parent_path' => '/cmf/example/contents/books'
            ],
            [
                'title' => 'Holy Bible',
                'navigationContexts' => ['main'],
                'structureType' => 'productPresentation',
                'text' => 'Fucking Holy!',
                'product' => [3],
                'parent_path' => '/cmf/example/contents/books'
            ]
        ];

        $pages = [];

        foreach ($pageDataList as $pageData) {
            $pages[$pageData['title']] = $this->createPage($documentManager, $pageData);
        }
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
            ]
        );

        $documentManager->persist($homeDocument, AppFixtures::LOCALE);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE);
    }

    public function getOrder()
    {
        return 10;
    }

    /**
     * @param mixed[] $data
     */
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
}