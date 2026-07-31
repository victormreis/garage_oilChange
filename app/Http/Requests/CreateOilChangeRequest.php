<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CreateOilChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'odometer' => 'required|integer',
            'date' => 'required|date|before_or_equal:today',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $vehicle = $this->route('vehicle');

            $lastChange = $vehicle->getLastChange();

            if (!$lastChange) {
                return;
            }

            if ($lastChange->odometer > $this->input('odometer')) {
                $validator->errors()->add(
                    'odometer',
                    'The odometer must be greater than or equal to the odometer on last change.'
                );
            }

            if ($lastChange->date > $this->input('date')) {
                $validator->errors()->add(
                    'date',
                    'The date must be greater than or equal to the date on last change.'
                );
            }
        });
    }
}
