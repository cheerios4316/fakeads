<?php

namespace App\Controller;

use App\Service\ContentService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

final class FileController
{
    public function __construct(
        private ContentService $contentService,
    ) {
    }

    #[OA\Get(
        path: '/file/{uuid}',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Responds with file of given uuid',
                content: new OA\MediaType(
                    mediaType: 'image/*',
                    schema: new OA\Schema(type: 'string', format: 'binary')
                )
            )
        ]
    )]
    #[Route(path: '/file/{uuid}', methods: ['GET'])]
    public function file(
        string $uuid,
    ): BinaryFileResponse {
        $path = $this->contentService->getFilePathByUuid($uuid);

        return new BinaryFileResponse(
            $path,
            200,
            ['Content-Type' => 'image/jpeg']
        );
    }
}
