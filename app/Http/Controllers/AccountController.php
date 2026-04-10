<?php

namespace App\Http\Controllers;

use App\Domain\Accounts\Actions\CreateAccountAction;
use App\Domain\Accounts\Actions\UpdateAccountAction;
use App\Domain\Accounts\DTOs\CreateAccountData;
use App\Domain\Accounts\Models\Account;
use App\Domain\Accounts\Resources\AccountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AccountController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = $request->user()->accounts()->get();

        return AccountResource::collection($accounts);
    }

    public function store(Request $request, CreateAccountAction $action): JsonResponse
    {
        $data = CreateAccountData::from($request->all());
        $account = $action->execute($request->user(), $data);

        return (new AccountResource($account))->response()->setStatusCode(201);
    }

    public function show(Request $request, Account $account): AccountResource
    {
        $this->authorize('view', $account);

        return new AccountResource($account);
    }

    public function update(Request $request, Account $account, UpdateAccountAction $action): AccountResource
    {
        $this->authorize('update', $account);
        $account = $action->execute($account, $request->all());

        return new AccountResource($account);
    }

    public function destroy(Request $request, Account $account): JsonResponse
    {
        $this->authorize('delete', $account);
        $account->delete();

        return response()->json(null, 204);
    }
}
