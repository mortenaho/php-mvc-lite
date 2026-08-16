<?php

declare(strict_types=1);

namespace Lite\Validation;

final class Validator
{
    /** @var array<string, list<string>> */
    private array $errors = [];

    /** @var array<string, mixed> */
    private array $validated = [];

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     */
    public function __construct(
        private readonly array $data,
        private readonly array $rules,
    ) {
        $this->validate();
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return array<string, mixed>
     */
    public function validated(): array
    {
        return $this->validated;
    }

    private function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $rules = array_filter(array_map('trim', explode('|', $ruleString)));
            $nullable = in_array('nullable', $rules, true);

            if (($value === null || $value === '') && $nullable && ! in_array('required', $rules, true)) {
                $this->validated[$field] = $value;
                continue;
            }

            foreach ($rules as $rule) {
                $this->apply($field, $value, $rule);
            }

            if (! isset($this->errors[$field])) {
                $this->validated[$field] = $value;
            }
        }
    }

    private function apply(string $field, mixed $value, string $rule): void
    {
        [$name, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

        $failed = match ($name) {
            'required' => $value === null || $value === '',
            'email' => ! is_string($value) || filter_var($value, FILTER_VALIDATE_EMAIL) === false,
            'min' => is_string($value) ? mb_strlen($value) < (int) $parameter : (is_numeric($value) && $value < (int) $parameter),
            'max' => is_string($value) ? mb_strlen($value) > (int) $parameter : (is_numeric($value) && $value > (int) $parameter),
            'integer' => filter_var($value, FILTER_VALIDATE_INT) === false,
            'numeric' => ! is_numeric($value),
            'nullable' => false,
            default => false,
        };

        if ($failed) {
            $this->errors[$field][] = $this->message($field, (string) $name, $parameter);
        }
    }

    private function message(string $field, string $rule, ?string $parameter): string
    {
        $label = str_replace('_', ' ', $field);

        return match ($rule) {
            'required' => "The {$label} field is required.",
            'email' => "The {$label} must be a valid email address.",
            'min' => "The {$label} must be at least {$parameter} characters.",
            'max' => "The {$label} may not be greater than {$parameter} characters.",
            'integer' => "The {$label} must be an integer.",
            'numeric' => "The {$label} must be a number.",
            default => "The {$label} is invalid.",
        };
    }
}
