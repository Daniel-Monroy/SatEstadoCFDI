<?php

namespace DanielMonroy\SatEstadoCfdi\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class ConsultarEstadoCfdiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'xml' => [
                'nullable',
                'file',
                'mimetypes:text/xml,application/xml',
                'required_without:expression',
            ],
            'expression' => [
                'nullable',
                'string',
                'max:1024',
                'required_without:xml',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'xml.required_without' => 'Debes enviar un XML o una expresión.',
            'expression.required_without' => 'Debes enviar una expresión o un XML.',
        ];
    }
}
