<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Source;
use App\Repository\SourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

readonly class VideoDownloadService
{
    public const VIDEO_DOWNLOAD_FORMAT = 'bestvideo[height<=1080]+bestaudio/best';
    public const OUTPUT_FILE_FORMAT    = '%(title)s.%(ext)s';
    public const MERGE_OUTPUT_FORMAT   = 'mp4';

    public function __construct(
        private string $downloadsDir,
        private EntityManagerInterface $entityManager,
        private SourceRepository $sourceRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function process(string $videoUrl): void
    {
        $yt = new YoutubeDl();

        $collection = $yt->download(
            Options::create()
                ->downloadPath($this->downloadsDir)
                ->url($videoUrl)
                ->format(VideoDownloadService::VIDEO_DOWNLOAD_FORMAT)
                ->mergeOutputFormat(VideoDownloadService::MERGE_OUTPUT_FORMAT)
                ->output(VideoDownloadService::OUTPUT_FILE_FORMAT)
        );

        foreach ($collection->getVideos() as $video) {
            if (null !== $video->getError()) {
                $this->logger->error('Error during downloading', ['error' => $video->getError()]);
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

        $this->logger->info('Finished downloading videos', ['videos' => $collection]);
    }
}
