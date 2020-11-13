<?php

namespace Database\Factories;

use App\AdBlock;

class AdBlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdBlock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => uniqid(),
            'code' => '<script type="text/javascript">alert("test");</script>',
            'description' => $this->faker->realText(100),
            'enabled' => false
        ];
    }

    public function enabled()
    {
        return $this->afterMaking(function (AdBlock $adBlock) {
            $adBlock->enable();
        });
    }
}
