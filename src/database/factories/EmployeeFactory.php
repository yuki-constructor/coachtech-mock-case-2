<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * 対応するモデル
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * デフォルトの状態を定義
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(), // ランダムな名前
            'email' => $this->faker->unique()->safeEmail(), // ランダムなメールアドレス
            'email_verified_at' => now(), // 認証済み
            'password' => Hash::make('password123'), // ハッシュ化したパスワード
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
