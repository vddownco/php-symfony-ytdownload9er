<?php

namespace App\Service;

use App\Entity\Source;
use App\Repository\SourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class ProcessYoutubeVideo
{
    public function __construct(
        private readonly string $projectDir,
        private readonly EntityManagerInterface $entityManager,
        private readonly SourceRepository $sourceRepository,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function process(string $videoUrl): void
    {
        /**
         * @var FlashBagAwareSessionInterface $session
         */
        $session = $this->requestStack->getSession();

        $yt = new YoutubeDl();

        $collection = $yt->download(
            Options::create()
                ->downloadPath("{$this->projectDir}/var/downloads")
                ->url($videoUrl)
                ->format('bestvideo[height<=1080]+bestaudio/best')
                ->mergeOutputFormat('mp4')
                ->output('%(title)s.%(ext)s')
        );

        foreach ($collection->getVideos() as $video) {
            if (null !== $video->getError()) {
                $session->set('error', "Error downloading video: {$video->getError()}.");
            } else {
                $filename = $video->getFile()->getBasename();
                $path     = $video->getFile()->getPath();
                $size     = $video->getFile()->getSize();

                $source = $this->sourceRepository->findOneByFilename($filename);

                if (null === $source) {
                    $source = new Source();
                    $source
                        ->setFilename($filename)
                        ->setFilepath($path)
                        ->setSize((float) $size)
                    ;

                    $this->entityManager->persist($source);
                }
            }
        }

        $this->entityManager->flush();
    }
}
