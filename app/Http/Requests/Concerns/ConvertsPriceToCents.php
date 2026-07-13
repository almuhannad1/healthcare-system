<?php

namespace App\Http\Requests\Concerns;

trait ConvertsPriceToCents
{
    /**
     * Users type money the natural way — dollars, like 12.50 — but storage
     * is integer cents. Convert here, before validation, so the model only
     * ever sees a clean integer.
     *
     * round() matters: 12.50 * 100 can land at 1249.9999… in binary float,
     * and a bare (int) cast would truncate that to 1249. Round first.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('price')) {
            $this->merge([
                'price_cents' => (int) round(((float) $this->price) * 100),
            ]);
        }
    }
}
