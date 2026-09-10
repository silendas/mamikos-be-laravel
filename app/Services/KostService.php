<?php

namespace App\Services;

use App\Constants\ResponseMessage;
use App\Http\Requests\KostRequest;
use App\Models\Kost;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KostService
{
    private function mapToResponse(Kost $kost): array
    {
        return [
            'id' => $kost->id,
            'name' => $kost->name,
            'location' => $kost->location,
            'price' => (float) $kost->price,
            'description' => $kost->description,
            'roomCount' => $kost->room_count,
            'owner' => [
                'id' => $kost->owner->id,
                'username' => $kost->owner->username,
                'email' => $kost->owner->email,
                'role' => $kost->owner->role,
                'credits' => $kost->owner->credits,
                'createdAt' => $kost->owner->created_at,
                'updatedAt' => $kost->owner->updated_at,
            ],
            'createdAt' => $kost->created_at,
            'updatedAt' => $kost->updated_at,
        ];
    }

    public function createKost(User $owner, KostRequest $request): array
    {
        if ($owner->role !== 'OWNER') {
            throw new AccessDeniedHttpException(ResponseMessage::ONLY_OWNERS_CAN_ADD_KOSTS);
        }

        $kost = Kost::create([
            'owner_id' => $owner->id,
            'name' => $request->name,
            'location' => $request->location,
            'price' => $request->price,
            'description' => $request->description,
            'room_count' => $request->roomCount,
        ]);

        return $this->mapToResponse($kost->load('owner'));
    }

    public function updateKost(User $owner, int $kostId, KostRequest $request): array
    {
        $kost = Kost::with('owner')->find($kostId);
        if (!$kost) {
            throw new NotFoundHttpException(ResponseMessage::KOST_NOT_FOUND);
        }

        if ($kost->owner_id !== $owner->id) {
            throw new AccessDeniedHttpException(ResponseMessage::NOT_KOST_OWNER);
        }

        $kost->update([
            'name' => $request->name,
            'location' => $request->location,
            'price' => $request->price,
            'description' => $request->description,
            'room_count' => $request->roomCount,
        ]);

        return $this->mapToResponse($kost->fresh('owner'));
    }

    public function deleteKost(User $owner, int $kostId): void
    {
        $kost = Kost::find($kostId);
        if (!$kost) {
            throw new NotFoundHttpException(ResponseMessage::KOST_NOT_FOUND);
        }

        if ($kost->owner_id !== $owner->id) {
            throw new AccessDeniedHttpException(ResponseMessage::NOT_KOST_OWNER);
        }

        $kost->delete();
    }

    public function getKostById(int $kostId): array
    {
        $kost = Kost::with('owner')->find($kostId);
        if (!$kost) {
            throw new NotFoundHttpException(ResponseMessage::KOST_NOT_FOUND);
        }

        return $this->mapToResponse($kost);
    }

    public function getOwnerKosts(User $owner): array
    {
        $kosts = Kost::with('owner')->where('owner_id', $owner->id)->get();
        return $kosts->map(fn($kost) => $this->mapToResponse($kost))->toArray();
    }

    public function searchKosts(?string $name, ?string $location, ?float $minPrice, ?float $maxPrice, ?string $sort, int $page, int $size): array
    {
        $query = Kost::with('owner');

        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        if ($location) {
            $query->where('location', 'like', '%' . $location . '%');
        }
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($sort) {
            if (strtolower($sort) === 'asc') {
                $query->orderBy('price', 'asc');
            } elseif (strtolower($sort) === 'desc') {
                $query->orderBy('price', 'desc');
            }
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page + 1);

        $content = collect($paginator->items())->map(fn($kost) => $this->mapToResponse($kost))->toArray();

        return [
            'content' => $content,
            'pageNumber' => $paginator->currentPage() - 1,
            'pageSize' => $paginator->perPage(),
            'totalElements' => $paginator->total(),
            'totalPages' => $paginator->lastPage(),
            'last' => $paginator->currentPage() === $paginator->lastPage() || $paginator->lastPage() === 0,
        ];
    }
}
