<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $singular = $this->faker->userName();

        return [
            'singular' => $singular,
            'plural' => $singular . "s",
            'item_id' => Type::all()->random()->id,
        ];
    }
}
