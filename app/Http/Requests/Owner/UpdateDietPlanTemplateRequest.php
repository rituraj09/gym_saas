<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDietPlanTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return (new StoreDietPlanTemplateRequest())->rules();
    }

    public function messages(): array
    {
        return (new StoreDietPlanTemplateRequest())->messages();
    }
}
