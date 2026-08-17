<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i',
            ],
        ];

        if ($this->user()->penghuni) {
            $rules['no_hp'] = [
                'required',
                'string',
                'digits_between:10,13',
                Rule::unique('penghunis', 'no_hp')->ignore($this->user()->penghuni->id),
            ];
            $rules['alamat'] = ['nullable', 'string'];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'email.regex' => 'Email harus menggunakan domain @gmail.com.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.digits_between' => 'Nomor HP harus berupa angka dengan panjang antara 10 hingga 13 digit.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar. Silakan gunakan nomor HP lain.',
        ];
    }
}
