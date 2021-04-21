<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use SplFileInfo;
use Sulu\Bundle\MediaBundle\Entity\Collection;
use Sulu\Bundle\MediaBundle\Entity\CollectionInterface;
use Sulu\Bundle\MediaBundle\Entity\CollectionMeta;
use Sulu\Bundle\MediaBundle\Entity\CollectionType;
use Sulu\Bundle\MediaBundle\Entity\File;
use Sulu\Bundle\MediaBundle\Entity\FileVersion;
use Sulu\Bundle\MediaBundle\Entity\FileVersionMeta;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaType;
use Sulu\Bundle\MediaBundle\Media\Storage\StorageInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    const LOCALE = 'en';

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getOrder()
    {
        return \PHP_INT_MAX;
    }

    public function load(ObjectManager $manager)
    {
        $collections = $this->loadCollections($manager);
        $this->loadImages($manager, $collections['Header']);

        $suluBookProduct = new Product();
        $suluBookProduct->setId(1);
        $suluBookProduct->setCode('sulu-book');
        $suluBookProduct->setName('Sulu 2: The Fast Track Book');
        $suluBookProduct->setPrice(30);
        $manager->persist($suluBookProduct);

        $symfonyBookProduct = new Product();
        $symfonyBookProduct->setId(2);
        $symfonyBookProduct->setCode('symfony-book');
        $symfonyBookProduct->setName('Symfony 5: The Fast Track Book');
        $symfonyBookProduct->setPrice(35);
        $manager->persist($symfonyBookProduct);

        $bibleBookProduct = new Product();
        $bibleBookProduct->setId(3);
        $bibleBookProduct->setCode('holy-bible');
        $bibleBookProduct->setName('The Holy Bible');
        $bibleBookProduct->setPrice(12);
        $manager->persist($bibleBookProduct);

        $manager->flush();
    }

    private function loadCollections(ObjectManager $manager): array
    {
        $collections = [];

        $collections['Header'] = $this->createCollection(
            $manager,
            ['title' => 'Header']
        );

        return $collections;
    }

    private function createCollection(ObjectManager $manager, array $data): CollectionInterface
    {
        $collection = new Collection();

        /** @var CollectionType|null $collectionType */
        $collectionType = $manager->getRepository(CollectionType::class)->find(1);

        if (!$collectionType) {
            throw new \RuntimeException('CollectionType "1" not found. Have you loaded the Sulu fixtures?');
        }

        $collection->setType($collectionType);

        $meta = new CollectionMeta();
        $meta->setLocale(self::LOCALE);
        $meta->setTitle($data['title']);
        $meta->setCollection($collection);

        $collection->addMeta($meta);
        $collection->setDefaultMeta($meta);

        $manager->persist($collection);
        $manager->persist($meta);

        return $collection;
    }

    private function loadImages(ObjectManager $manager, CollectionInterface $collection): array
    {
        $media = [];
        $finder = new Finder();

        foreach ($finder->files()->in(__DIR__ . '/images') as $file) {
            $media[pathinfo($file, \PATHINFO_BASENAME)] = $this->createMedia($manager, $collection, $file);
        }

        return $media;
    }

    private function createMedia(
        ObjectManager $manager,
        CollectionInterface $collection,
        SplFileInfo $fileInfo
    ): MediaInterface {
        $fileName = $fileInfo->getBasename();
        $title = $fileInfo->getFilename();
        $uploadedFile = new UploadedFile($fileInfo->getPathname(), $fileName);

        $storageOptions = $this->storage->save(
            $uploadedFile->getPathname(),
            $fileName
        );

        $mediaType = $manager->getRepository(MediaType::class)->find(2);
        if (!$mediaType instanceof MediaType) {
            throw new \RuntimeException('MediaType "2" not found. Have you loaded the Sulu fixtures?');
        }

        $media = new Media();

        $file = new File();
        $file->setVersion(1)
            ->setMedia($media);

        $media->addFile($file)
            ->setType($mediaType)
            ->setCollection($collection);

        $fileVersion = new FileVersion();
        $fileVersion->setVersion($file->getVersion())
            ->setSize($uploadedFile->getSize())
            ->setName($fileName)
            ->setStorageOptions($storageOptions)
            ->setMimeType($uploadedFile->getMimeType() ?: 'image/jpeg')
            ->setFile($file);

        $file->addFileVersion($fileVersion);

        $fileVersionMeta = new FileVersionMeta();
        $fileVersionMeta->setTitle($title)
            ->setDescription('')
            ->setLocale(self::LOCALE)
            ->setFileVersion($fileVersion);

        $fileVersion->addMeta($fileVersionMeta)
            ->setDefaultMeta($fileVersionMeta);

        $manager->persist($fileVersionMeta);
        $manager->persist($fileVersion);
        $manager->persist($media);

        return $media;
    }
}
