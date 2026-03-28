<?php

namespace App\Controller;

use App\Dto\FileResponseDto;
use App\Dto\FiltersDto;
use App\Exception\NoContentException;
use App\Service\ContentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AdsController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly ContentService $contentService,
    ) {
    }

    #[OA\Get(
        path: '/gemjak/list',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns all Gemjaks',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: FileResponseDto::class))
                )
            ),
        ]
    )]
    #[Route(path: '/gemjak/list', methods: ['GET'])]
    public function allGemjaks(
        int $limit = 10
    ): JsonResponse {
        return new JsonResponse(
            $this->normalizer->normalize($this->contentService->getAllGemJaks($limit))
        );
    }

    #[OA\Get(
        path: '/random/list',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a randomized list of resource elements',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: FileResponseDto::class))
                )
            ),
        ]
    )]
    #[Route(path: '/random/list', methods: ['GET'])]
    public function random(
        #[MapQueryString(
            serializationContext: ['allow_extra_attributes' => false]
        )] FiltersDto $filters,
    ): JsonResponse {
        return new JsonResponse(
            $this->normalizer->normalize($this->contentService->getRandomList($filters))
        );
    }

    #[OA\Get(
        path: '/random/banner',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Random banner-size resource',
                content: new OA\MediaType(
                    mediaType: 'image/*',
                    schema: new OA\Schema(type: 'string', format: 'binary')
                )
            ),
        ]
    )]
    #[Route(path: '/random/banner', methods: ['GET'])]
    public function randomBanner(): BinaryFileResponse
    {
        $element = $this->contentService->getRandomBanner();

        if (!$element) {
            throw new NoContentException();
        }

        $path = $this->contentService->getFilePathByEntity($element);

        return $this->generateRandomFileResponse($path);
    }

    #[OA\Get(
        path: '/random/popup',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Random popup-size resource',
                content: new OA\MediaType(
                    mediaType: 'image/*',
                    schema: new OA\Schema(type: 'string', format: 'binary')
                )
            ),
        ]
    )]
    #[Route(path: '/random/popup', methods: ['GET'])]
    public function randomPopup(): BinaryFileResponse
    {
        $element = $this->contentService->getRandomPopup();

        if (!$element) {
            throw new NoContentException();
        }

        $path = $this->contentService->getFilePathByEntity($element);

        return $this->generateRandomFileResponse($path);
    }

    private function generateRandomFileResponse(string $path): BinaryFileResponse
    {
        $response = new BinaryFileResponse(
            file: $path,
            status: 200,
            headers: ['Content-Type' => 'image/jpeg'],
        );

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
