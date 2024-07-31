<?php

namespace Krendil\ModelTree\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\CreateResourceRequest;

class ModelTreeController
{
    public function index(CreateResourceRequest $request): JsonResponse
    {
        $tree = $this->getChildren(
            $request, $this->getModelCollection($request)
        );

        return response()->json([
            'tree' => $tree,
        ]);
    }

    public function store(CreateResourceRequest $request): JsonResponse
    {
        $tree = $request->post('tree');

        DB::beginTransaction();

        try {
            $this->saveTree($tree, $request);

            DB::commit();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 503);
        }

        return response()->json();
    }

    protected function getChildren(CreateResourceRequest $request, Collection $items): array
    {
        return $items->map(function(Model $item) use ($request) {
            return [
                'id' => $item->getKey(),
                'text' => $item->getAttribute($request->resource()::$title),
                'children' => $this->getChildren(
                    $request, $this->getModelCollection($request, $item->getKey())
                ),
            ];
        })->values()->toArray();
    }

    protected function getModelCollection(CreateResourceRequest $request, int|null $parentId = null): Collection
    {
        $builder = $request->model()->newModelQuery()->orderBy('order', 'asc');

        if ($parentId) {
            $builder->where('parent_id', $parentId);
        }
        else {
            $builder->whereNull('parent_id');
        }

        return $builder->get();
    }

    protected function saveTree(array $tree, CreateResourceRequest $request, int|null $parentId = null): void
    {
        $order = 1;

        foreach ($tree as $leaf) {
            /** @var Model $_model */
            $model = $request->model()->newModelQuery()->findOrFail($leaf['id']);

            $model->update([
                'order' => $order++,
                'parent_id' => $parentId,
            ]);

            if ($children = $leaf['children'] ?? []) {
                $this->saveTree($children, $request, $model->getKey());
            }
        }
    }
}
