<?php

namespace App\Controller;

use App\Dto\FileResponseDto;
use App\Dto\UploadDto;
use App\Enums\CategoryEnum;
use App\Exception\UnauthorizedException;
use App\Service\AuthorizationService;
use App\Service\ContentService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class FileController
{
    public function __construct(
        private ContentService $contentService,
        private AuthorizationService $authorizationService,
        private NormalizerInterface $normalizer,
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
            ),
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

    #[OA\Post(
        path: '/upload',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'file',
                            type: 'string',
                            format: 'binary'
                        ),
                        new OA\Property(
                            property: 'payload',
                            ref: new Model(type: UploadDto::class)
                        ),
                    ],
                    type: 'object'
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'File response',
                content: new OA\JsonContent(
                    ref: new Model(type: FileResponseDto::class)
                )
            ),
        ]
    )]
    #[Route(path: '/upload', methods: ['POST'])]
    public function upload(
        Request $request,
        #[MapUploadedFile] UploadedFile $file,
        #[MapRequestPayload] UploadDto $payload,
    ): JsonResponse {
        try {
            $this->authorizationService->check($request);

            $entity = $this->contentService->upload(
                file: $file,
                clickout: $payload->clickout,
                category: CategoryEnum::from(value: $payload->category),
                description: $payload->description,
            );

            return new JsonResponse($this->normalizer->normalize($entity));
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['Unauthorized: '.$e->getMessage()], $e->getCode());
        } catch (\Throwable $e) {
            return new JsonResponse(['Server error: '.$e->getMessage()], 500);
        }
    }
}
