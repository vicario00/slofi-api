<?php

namespace App\Http\Controllers;

use App\Domain\Tags\Actions\CreateTagAction;
use App\Domain\Tags\DTOs\CreateTagData;
use App\Domain\Tags\Models\Tag;
use App\Domain\Tags\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return TagResource::collection($request->user()->tags()->get());
    }

    public function store(Request $request, CreateTagAction $action): JsonResponse
    {
        $data = CreateTagData::from($request->all());
        $tag = $action->execute($request->user(), $data);

        return (new TagResource($tag))->response()->setStatusCode(201);
    }

    public function show(Request $request, Tag $tag): TagResource
    {
        $this->authorize('view', $tag);

        return new TagResource($tag);
    }

    public function update(Request $request, Tag $tag): TagResource
    {
        $this->authorize('update', $tag);
        $tag->update($request->only(['name', 'color', 'icon']));

        return new TagResource($tag->fresh());
    }

    public function destroy(Request $request, Tag $tag): JsonResponse
    {
        $this->authorize('delete', $tag);
        $tag->delete();

        return response()->json(null, 204);
    }
}
