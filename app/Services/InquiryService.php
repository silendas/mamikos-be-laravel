<?php

namespace App\Services;

use App\Constants\ResponseMessage;
use App\Http\Requests\InquiryRequest;
use App\Models\Inquiry;
use App\Models\Kost;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InquiryService
{
    private function mapToResponse(Inquiry $inquiry): array
    {
        return [
            'id' => $inquiry->id,
            'message' => $inquiry->message,
            'user' => [
                'id' => $inquiry->user->id,
                'username' => $inquiry->user->username,
                'email' => $inquiry->user->email,
                'role' => $inquiry->user->role,
                'credits' => $inquiry->user->credits,
                'createdAt' => $inquiry->user->created_at,
                'updatedAt' => $inquiry->user->updated_at,
            ],
            'kost' => [
                'id' => $inquiry->kost->id,
                'name' => $inquiry->kost->name,
                'location' => $inquiry->kost->location,
                'price' => (float) $inquiry->kost->price,
                'description' => $inquiry->kost->description,
                'roomCount' => $inquiry->kost->room_count,
                'owner' => [
                    'id' => $inquiry->kost->owner->id,
                    'username' => $inquiry->kost->owner->username,
                    'email' => $inquiry->kost->owner->email,
                    'role' => $inquiry->kost->owner->role,
                    'credits' => $inquiry->kost->owner->credits,
                    'createdAt' => $inquiry->kost->owner->created_at,
                    'updatedAt' => $inquiry->kost->owner->updated_at,
                ],
                'createdAt' => $inquiry->kost->created_at,
                'updatedAt' => $inquiry->kost->updated_at,
            ],
            'createdAt' => $inquiry->created_at,
            'updatedAt' => $inquiry->updated_at,
        ];
    }

    public function askAvailability(User $user, InquiryRequest $request): array
    {
        $kost = Kost::with('owner')->find($request->kostId);
        if (!$kost) {
            throw new NotFoundHttpException(ResponseMessage::KOST_NOT_FOUND);
        }

        $creditCost = 5;
        if ($user->credits < $creditCost) {
            throw new BadRequestHttpException(ResponseMessage::INSUFFICIENT_CREDITS);
        }

        $user->decrement('credits', $creditCost);
        $user->refresh();

        $inquiry = Inquiry::create([
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'message' => $request->message,
        ]);

        return $this->mapToResponse($inquiry->load(['user', 'kost.owner']));
    }

    public function getUserInquiries(User $user): array
    {
        $inquiries = Inquiry::with(['user', 'kost.owner'])->where('user_id', $user->id)->get();
        return $inquiries->map(fn($inquiry) => $this->mapToResponse($inquiry))->toArray();
    }
}
