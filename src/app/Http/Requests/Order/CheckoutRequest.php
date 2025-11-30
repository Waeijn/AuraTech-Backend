<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                Rule::in(['cash_on_delivery', 'credit_card', 'gcash', 'paymaya'])
            ],
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please select a payment method',
            'payment_method.in' => 'Invalid payment method selected',
            'shipping_name.required' => 'Shipping name is required',
            'shipping_email.required' => 'Shipping email is required',
            'shipping_email.email' => 'Please provide a valid email address',
            'shipping_phone.required' => 'Phone number is required',
            'shipping_address.required' => 'Shipping address is required',
            'shipping_city.required' => 'City is required',
            'shipping_state.required' => 'State/Province is required',
            'shipping_zip.required' => 'ZIP/Postal code is required'
        ];
    }
}