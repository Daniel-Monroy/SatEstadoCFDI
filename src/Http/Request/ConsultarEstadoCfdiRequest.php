<?php

namespace DanielMonroy\SatEstadoCfdi\Http\Request;

use Closure;
use DanielMonroy\SatEstadoCfdi\Support\PrintedExpression;
use Illuminate\Foundation\Http\FormRequest;

class ConsultarEstadoCfdiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('expression')) {
            $this->merge([
                'expression' => trim((string) $this->input('expression')),
            ]);
        }
    }

    /**
     * @return array<string, array<int, string|Closure>>
     */
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
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! is_string($value) || trim($value) === '') {
                        return;
                    }

                    $missingFields = PrintedExpression::missingRequiredFields($value);

                    if ($missingFields !== []) {
                        $fail(sprintf(
                            'La expresión debe incluir los parámetros requeridos: %s.',
                            implode(', ', $missingFields)
                        ));
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'xml.required_without' => 'Debes enviar un XML o una expresión.',
            'expression.required_without' => 'Debes enviar una expresión o un XML.',
            'expression.max' => 'La expresión no puede exceder 1024 caracteres.',
        ];
    }
}
