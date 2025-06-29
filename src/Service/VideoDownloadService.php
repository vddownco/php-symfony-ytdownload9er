<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Log;
use App\Entity\Source;
use App\Repository\SourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

readonly class VideoDownloadService
{
    public const BEST_VIDEO_DOWNLOAD_FORMAT     = 'bestvideo[height<=1080]+bestaudio/best';
    public const MODERATE_VIDEO_DOWNLOAD_FORMAT = 'bestvideo[height<=720]+bestaudio/best';
    public const POOR_VIDEO_DOWNLOAD_FORMAT     = 'bestvideo[height<=320]+bestaudio/best';
    public const DRAFT_VIDEO_DOWNLOAD_FORMAT    = 'bestvideo[height<=240]+bestaudio/best';
    public const NO_VIDEO_DOWNLOAD_FORMAT       = 'bestaudio/best';
    public const OUTPUT_FILE_FORMAT             = '%(title)s.%(ext)s';
    public const MERGE_OUTPUT_FORMAT            = 'mp4';

    public function __construct(
        private string $downloadsDir,
        private EntityManagerInterface $entityManager,
        private SourceRepository $sourceRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function process(string $videoUrl, string $format): void
    {
        $yt = new YoutubeDl();

        $downloadFormat = '';
        $merge          = true;

        switch ($format) {
            case 'best':
                $downloadFormat = self::BEST_VIDEO_DOWNLOAD_FORMAT;
                break;
            case 'moderate':
                $downloadFormat = self::MODERATE_VIDEO_DOWNLOAD_FORMAT;
                break;
            case 'poor':
                $downloadFormat = self::POOR_VIDEO_DOWNLOAD_FORMAT;
                break;
            case 'draft':
                $downloadFormat = self::DRAFT_VIDEO_DOWNLOAD_FORMAT;
                break;
            case 'audio':
                $downloadFormat = self::NO_VIDEO_DOWNLOAD_FORMAT;
                $merge          = false;
                break;
        }

        if ($merge) {
            $collection = $yt->download(
                Options::create()
                    ->downloadPath($this->downloadsDir)
                    ->url($videoUrl)
                    ->format($downloadFormat)
                    ->mergeOutputFormat(VideoDownloadService::MERGE_OUTPUT_FORMAT)
                    ->output(VideoDownloadService::OUTPUT_FILE_FORMAT)
            );
        } else {
            $collection = $yt->download(
                Options::create()
                    ->downloadPath($this->downloadsDir)
                    ->url($videoUrl)
                    ->format($downloadFormat)
                    ->output(VideoDownloadService::OUTPUT_FILE_FORMAT)
            );
        }

        foreach ($collection->getVideos() as $video) {
            if (null !== $video->getError()) {
                $this->logger->error('Error during downloading', ['error' => $video->getError()]);

                $log = new Log();
                $log
                    ->setType('error')
                    ->setMessage('Error during downloading')
                ;

                $this->entityManager->persist($log);
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

                $log = new Log();
                $log
                    ->setType('info')
                    ->setMessage('Video download complete - ' . $filename)
                ;
            }
        }

        $this->entityManager->flush();

        $this->logger->info('Finished downloading videos', ['videos' => $collection]);
    }
}
