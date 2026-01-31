<?php

namespace App\Controller;

use App\Dto\FiltersDto;
use App\Dto\UploadDto;
use App\Enums\SizeEnum;
use App\Service\AuthorizationService;
use App\Service\ContentService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AdsController
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly ContentService $contentService,
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    #[Route(path: '/random', methods: ['GET'])]
    public function random(
        #[MapQueryString(
            serializationContext: ['allow_extra_attributes' => false]
        )] FiltersDto $filters,
    ): JsonResponse {
        return new JsonResponse(
            $this->normalizer->normalize($this->contentService->getRandom($filters))
        );
    }

    #[Route(path: '/upload', methods: ['POST'])]
    public function upload(
        Request $request,
        #[MapUploadedFile] UploadedFile $file,
        #[MapRequestPayload] UploadDto $payload,
    ): JsonResponse {
        $this->authorizationService->check($request);

        $entity = $this->contentService->upload(
            file: $file,
            clickout: $payload->clickout,
            size: SizeEnum::from(value: $payload->size),
            description: $payload->description,
        );

        return new JsonResponse($this->normalizer->normalize($entity));
    }
}
