<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TaxRuleResource;
use App\Models\TaxRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class TaxRuleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer'],
            'type' => ['nullable', 'string'],
        ]);

        return TaxRuleResource::collection(
            TaxRule::query()
                ->with($this->orderedBrackets())
                ->when(isset($validated['year']), static function ($query) use ($validated): void {
                    $query->where('year', $validated['year']);
                })
                ->when(isset($validated['type']), static function ($query) use ($validated): void {
                    $query->where('type', $validated['type']);
                })
                ->orderByDesc('year')
                ->orderBy('type')
                ->paginate()
        );
    }

    public function show(TaxRule $taxRule): TaxRuleResource
    {
        return TaxRuleResource::make($taxRule->load($this->orderedBrackets()));
    }

    /**
     * @return array<string, \Closure>
     */
    private function orderedBrackets(): array
    {
        return [
            'brackets' => static function ($query): void {
                $query->orderBy('sort_order');
            },
        ];
    }
}
