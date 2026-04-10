<?php

namespace App\Http\Controllers;

use App\Domain\CreditCards\Actions\CreateCreditCardAction;
use App\Domain\CreditCards\Actions\UpdateCreditCardAction;
use App\Domain\CreditCards\DTOs\CreateCreditCardData;
use App\Domain\CreditCards\Models\CreditCard;
use App\Domain\CreditCards\Resources\CreditCardResource;
use App\Domain\CreditCards\Services\BillingCycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CreditCardController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return CreditCardResource::collection($request->user()->creditCards()->get());
    }

    public function store(Request $request, CreateCreditCardAction $action): JsonResponse
    {
        $data = CreateCreditCardData::from($request);
        $card = $action->execute($request->user(), $data);

        return (new CreditCardResource($card))->response()->setStatusCode(201);
    }

    public function show(Request $request, CreditCard $card): CreditCardResource
    {
        $this->authorize('view', $card);

        return new CreditCardResource($card);
    }

    public function update(Request $request, CreditCard $card, UpdateCreditCardAction $action): CreditCardResource
    {
        $this->authorize('update', $card);
        $card = $action->execute($card, $request->all());

        return new CreditCardResource($card);
    }

    public function destroy(Request $request, CreditCard $card): JsonResponse
    {
        $this->authorize('delete', $card);
        $card->delete();

        return response()->json(null, 204);
    }

    public function balance(CreditCard $credit_card, BillingCycleService $service): JsonResponse
    {
        $this->authorize('view', $credit_card);

        return response()->json($service->currentPeriodBalance($credit_card));
    }
}
