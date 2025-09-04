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
            'xml' => ['nullable', 'file', 'mimetypes:text/xml,application/xml'],
            'expression' => ['nullable', 'string', 'max:1024'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (!$this->file('xml') && !$this->string('expression')) {
                $v->errors()->add('xml', 'Debes enviar un XML o una expresión.');
            }
        });
    }
}
