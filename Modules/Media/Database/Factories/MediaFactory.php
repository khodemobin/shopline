<?php

namespace Modules\Media\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Media\Models\Media;
use Modules\User\Models\User;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'filename' => $this->faker->text(10),
            'files' => [
                "300" => $this->faker->image(storage_path('app/public'), 300, 300, null, false)
            ],
            "type" => "image",
            "is_private" => false
        ];
    }
}
