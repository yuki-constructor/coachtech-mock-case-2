<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'], // お名前: 入力必須
            'email' => ['required', 'email', 'unique:users,email'], // メールアドレス: 入力必須、メール形式、重複なし
            'password' => ['required', 'string', 'min:8', 'confirmed'], // パスワード: 入力必須、8文字以上
            'password_confirmation' => ['required', 'string', 'min:8'], // パスワード: 入力必須、8文字以上、確認用パスワードとの一致
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスは正しい形式で入力してください',
            'email.unique' => 'このメールアドレスは既に使用されています',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password.confirmed' => 'パスワードと一致しません',
            'password_confirmation.required' => '確認用パスワードを入力してください',
        ];
    }
}
