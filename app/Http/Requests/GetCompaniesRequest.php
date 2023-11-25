<?php

namespace App\Http\Requests;

use App\Services\Response;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class GetCompaniesRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'min:2', 'max:30', 'regex:/^[A-Za-z0-9\s\-\&\(\)\.\/\,]+$/'],
            'address' => ['string', 'min:2', 'max:50', "regex:/^[A-Za-z0-9\s\-\&\(\)\.\/\,]+$/"],
            'perPage' => ['int', 'min:1', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'  => "Company name",
            'address'  => "Company address",
            'perPage'  => "Companies per page",
        ];
    }

    /**
     * Response
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            (new Response())->validationError($validator->errors())
        );
    }
}
