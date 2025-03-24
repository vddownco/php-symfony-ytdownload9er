<?php

namespace App\Controller\Ui;

use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;
use App\Form\DownloadType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class YoutubeDownloadController extends AbstractController
{
    #[Route('/youtube/download', name: 'ui_youtube_download_index', methods: [Request::METHOD_GET, Request::METHOD_POST,])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(DownloadType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $url = $form->get('link')->getViewData();
            
            $yt = new YoutubeDl();

            $projectDir = $this->getParameter('kernel.project_dir');

            $collection = $yt->download(
                Options::create()
                    ->downloadPath("{$projectDir}/var/downloads")
                    ->url($url)
            );

            dd($collection);

            foreach ($collection->getVideos() as $video) {
                if ($video->getError() !== null) {
                    echo "Error downloading video: {$video->getError()}.";
                } else {
                    echo $video->getTitle(); // Will return Phonebloks
                    // $video->getFile(); // \SplFileInfo instance of downloaded file
                }
            }
        }

        return $this->render('youtube_download/index.html.twig', [
            'form' => $form
        ]);
    }
}
