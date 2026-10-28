<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaasPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:255',
            'code'                 => 'required|string|max:50|unique:saas_plans,code',
            'description'          => 'nullable|string|max:1000',
            'monthly_price'        => 'required|numeric|min:0|max:99999.99',
            'yearly_price'         => 'required|numeric|min:0|max:99999.99',
            'max_trainers'         => 'required|integer|min:0',
            'max_members'          => 'required|integer|min:0',
            'has_exercise_module'  => 'boolean',
            'has_diet_module'      => 'boolean',
            'has_reports_module'   => 'boolean',
            'has_priority_support' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_exercise_module'  => $this->boolean('has_exercise_module'),
            'has_diet_module'      => $this->boolean('has_diet_module'),
            'has_reports_module'   => $this->boolean('has_reports_module'),
            'has_priority_support' => $this->boolean('has_priority_support'),
        ]);
    }
}
