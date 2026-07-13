<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ConvertsPriceToCents;
use App\Models\Medication;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequest extends FormRequest
{
    use ConvertsPriceToCents;

    /**
     * Staff-only, per MedicationPolicy — the one place the rule lives.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Medication::class) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'strength' => ['nullable', 'string', 'max:255'],
            // The user types dollars; the trait converts to integer cents before we get here.
            // 'price' carries all the user-facing rules; 'price_cents' is the derived
            // value, listed only so it flows through to validated().
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'price_cents' => ['required', 'integer'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ];
    }
}
