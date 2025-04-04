<?php

namespace App\Controller\Ui;

use App\Form\DownloadType;
use App\Service\DiskSpaceChecker;
use App\Service\ProcessYoutubeVideo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class YoutubeDownloadController extends AbstractController
{
    #[Route('/ui/youtube/download', name: 'ui_youtube_download_index', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function index(
        Request $request,
        DiskSpaceChecker $diskSpaceChecker,
        ProcessYoutubeVideo $processYoutubeVideo,
    ): Response|RedirectResponse {
        $form = $this->createForm(DownloadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videoUrl = $form->get('link')->getViewData();

            $processYoutubeVideo->process($videoUrl);

            return $this->redirectToRoute('ui_source_index');
        }

        return $this->render('ui/youtube_download/index.html.twig', [
            'form'       => $form,
            'disk_space' => $diskSpaceChecker->getFreeSpace(),
        ]);
    }
}
