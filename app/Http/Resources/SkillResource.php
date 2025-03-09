<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SkillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'skill',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'createdAt' => $this->created_at ? Carbon::parse($this->created_at)->toIso8601String() : null,
                'updatedAt' => $this->updated_at ? Carbon::parse($this->updated_at)->toIso8601String() : null,
            ],
            'links' => [
                'self' => route('skills.show', ['skill' => $this->id]),
            ],
            'relationships' => [
                'skillable' => [
                    'data' => [
                        'type' => $this->getSkillableType(),
                        'id' => $this->skillable_id,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the skillable type formatted for JSON:API.
     *
     * @return string
     */
    private function getSkillableType(): string
    {

        $parts = explode('\\', $this->skillable_type);
        $className = end($parts);
        
        return strtolower($className);
    }
}