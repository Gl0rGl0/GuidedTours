<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormInput extends Component
{
    /**
     * The input field's name.
     */
    public string $name;

    /**
     * The input field's label.
     */
    public string $label;

    /**
     * The input field's type (e.g., text, password, textarea).
     */
    public string $type;

    /**
     * The input field's ID (for label association).
     */
    public string $id;

    /**
     * The input field's default value.
     */
    public ?string $value;

    /**
     * The number of rows for a textarea.
     */
    public ?int $rows;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label,
        string $type = 'text', // Default type is text
        ?string $id = null, // ID can be null, will default to name
        ?string $value = null, // Value can be null
        ?int $rows = null // Rows can be null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->id = $id ?? $name; // Default ID to name if not provided
        $this->value = $value;
        $this->rows = $rows;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form-input');
    }
}
