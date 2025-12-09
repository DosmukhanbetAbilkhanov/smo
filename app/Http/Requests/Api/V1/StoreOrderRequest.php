<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'shop_id' => 'required|exists:shops,id',
            'delivery_address' => 'required|string|max:500',
            'delivery_entry' => 'nullable|string|max:50',
            'delivery_floor' => 'nullable|string|max:50',
            'delivery_apartment' => 'nullable|string|max:50',
            'delivery_intercom' => 'nullable|string|max:50',
            'delivery_city_id' => 'required|exists:cities,id',
            'contact_phone' => 'required|string|max:20',
            'delivery_notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'shop_id.required' => 'Shop ID is required',
            'shop_id.exists' => 'Selected shop does not exist',
            'delivery_address.required' => 'Delivery address is required',
            'delivery_address.max' => 'Delivery address cannot exceed 500 characters',
            'delivery_entry.max' => 'Entry cannot exceed 50 characters',
            'delivery_floor.max' => 'Floor cannot exceed 50 characters',
            'delivery_apartment.max' => 'Apartment cannot exceed 50 characters',
            'delivery_intercom.max' => 'Intercom code cannot exceed 50 characters',
            'delivery_city_id.required' => 'Delivery city is required',
            'delivery_city_id.exists' => 'Selected city does not exist',
            'contact_phone.required' => 'Contact phone is required',
            'contact_phone.max' => 'Contact phone cannot exceed 20 characters',
            'delivery_notes.max' => 'Delivery notes cannot exceed 1000 characters',
        ];
    }
}
