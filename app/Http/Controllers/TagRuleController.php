<?php

namespace App\Http\Controllers;

use App\Domain\Tags\Actions\CreateTagRuleAction;
use App\Domain\Tags\DTOs\CreateTagRuleData;
use App\Domain\Tags\Models\Tag;
use App\Domain\Tags\Models\TagRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagRuleController extends Controller
{
    public function index(Request $request, Tag $tag): JsonResponse
    {
        $this->authorize('view', $tag);
        $rules = $tag->rules()->orderByDesc('priority')->get();

        return response()->json($rules);
    }

    public function store(Request $request, Tag $tag, CreateTagRuleAction $action): JsonResponse
    {
        $this->authorize('update', $tag);
        $data = CreateTagRuleData::validateAndCreate([...$request->all(), 'tag_id' => $tag->id]);
        $rule = $action->execute($data);

        return response()->json($rule, 201);
    }

    public function update(Request $request, TagRule $rule): JsonResponse
    {
        $this->authorize('update', $rule->tag);
        $rule->update($request->only(['field', 'operator', 'value', 'priority']));

        return response()->json($rule->fresh());
    }

    public function destroy(Request $request, TagRule $rule): JsonResponse
    {
        $this->authorize('delete', $rule->tag);
        $rule->delete();

        return response()->json(null, 204);
    }
}
