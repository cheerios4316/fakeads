<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class InterfaceController
{
    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
    }

    #[Route(path: '/interface/upload', methods: ['GET'])]
    public function upload()
    {
        $htmlPath = implode(DIRECTORY_SEPARATOR, [
            rtrim($this->kernel->getProjectDir(), DIRECTORY_SEPARATOR),
            'public', 'pages', 'upload.html',
        ]);

        $html = file_get_contents($htmlPath);

        return new Response($html);
    }
}
