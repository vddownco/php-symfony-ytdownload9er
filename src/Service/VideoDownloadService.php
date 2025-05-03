<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Source;
use App\Repository\SourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

readonly class VideoDownloadService
{
    public function __construct(
        private string                 $projectDir,
        private EntityManagerInterface $entityManager,
        private SourceRepository       $sourceRepository,
    ) {
    }

    public function process(string $videoUrl): void
    {
        $yt = new YoutubeDl();

        $collection = $yt->download(
            Options::create()
                ->downloadPath(sprintf('%s/var/downloads', $this->projectDir))
                ->url($videoUrl)
                ->format('bestvideo[height<=1080]+bestaudio/best')
                ->mergeOutputFormat('mp4')
                ->output('%(title)s.%(ext)s')
        );

        foreach ($collection->getVideos() as $video) {
            if (null !== $video->getError()) {
                // Todo some error notification
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
