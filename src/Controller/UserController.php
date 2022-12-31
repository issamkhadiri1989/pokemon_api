<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Trainer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class UserController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route(
     *     name="token_user",
     *     path="/api/profile",
     *     methods={"GET"},
     *     defaults={
     *          "_api_resource_class"=Trainer::class,
     *          "_api_item_opration_name"="get_token_user"
     *     }
     * )
     */
    public function __invoke(#[CurrentUser] Trainer $data): JsonResponse
    {
        return new JsonResponse(
            data: $this->serializer->serialize(data: $data, format: 'json',context: ['groups' => 'admin']),
            json: true
        );
    }
}