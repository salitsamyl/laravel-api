<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // STORE
        if ($this->isMethod('post') && !$this->has('_method')) {
            return [
                'kode_barang' => 'required|string|unique:produks,kode_barang',
                'nama_barang' => 'required|string|max:255',
                'harga' => 'required|numeric|min:0',
                'stok' => 'required|integer|min:0',
                'deskripsi' => 'nullable|string',
                // 'gambar' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'kategori' => 'required|string',
                'expired_date' => 'nullable|date',
                'rating' => 'nullable|numeric|min:0|max:5',
            ];
        }

        // UPDATE
        return [
            'kode_barang' => [
                'sometimes',
                'string',
                Rule::unique('produks', 'kode_barang')->ignore($this->route('id'))
            ],
            'nama_barang' => 'sometimes|string|max:255',
            'harga' => 'sometimes|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'stok' => 'sometimes|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kategori' => 'sometimes|string',
            'expired_date' => 'nullable|date',
            'rating' => 'nullable|numeric|min:0|max:5',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
