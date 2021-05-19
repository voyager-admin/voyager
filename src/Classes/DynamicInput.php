<?php

namespace Voyager\Admin\Classes;

class DynamicInput implements \JsonSerializable
{
    protected $inputs;

    public function __construct()
    {
        $this->inputs = collect();
    }

    public function addSelect(?string $key = null, ?string $title = null, ?array $options = [], ?bool $multiple = false, $value = null): self
    {
        $this->addMultipleChoiceInput('select', $key, $title, $options, $multiple, $value);

        return $this;
    }

    public function addText(?string $key = null, ?string $title = null, ?string $placeholder = null, ?string $value = null): self
    {
        $this->inputs->push([
            'type'          => 'text',
            'key'           => $key,
            'title'         => $title,
            'placeholder'   => $placeholder,
            'value'         => $value,
        ]);

        $this->checkInputs();

        return $this;
    }

    public function addNumber(?string $key = null, ?string $title = null, ?string $placeholder = null, ?int $value = null, ?int $min = null, ?int $max = null): self
    {
        $this->inputs->push([
            'type'          => 'number',
            'key'           => $key,
            'title'         => $title,
            'placeholder'   => $placeholder,
            'value'         => $value,
            'min'           => $min,
            'max'           => $max,
        ]);

        $this->checkInputs();

        return $this;
    }
    public function addCheckboxes(?string $key = null, ?string $title = null, ?array $options = [], $value = null): self
    {
        $this->addMultipleChoiceInput('checkbox', $key, $title, $options, true, $value);

        return $this;
    }
    public function addRadios(?string $key = null, ?string $title = null, ?array $options = [], $value = null): self
    {
        if (is_array($value)) {
            throw new \Exception('The default value for a radio-input in a dynamic select can not be an array!');
        }
        $this->addMultipleChoiceInput('radio', $key, $title, $options, false, $value);

        return $this;
    }

    public function addSwitch(?string $key = null, ?string $title = null, ?bool $value = false)
    {
        $this->inputs->push([
            'type'      => 'switch',
            'key'       => $key,
            'title'     => $title,
            'value'     => $value,
        ]);

        $this->checkInputs();

        return $this;
    }

    private function addMultipleChoiceInput(string $type, ?string $key = null, ?string $title = null, ?array $options = [], ?bool $multiple = false, $value = null)
    {
        $this->inputs->push([
            'type'      => $type,
            'key'       => $key,
            'title'     => $title,
            'options'   => $options,
            'multiple'  => $multiple,
            'value'     => $value,
        ]);

        $this->checkInputs();
    }

    private function checkInputs()
    {
        $inputsWithoutKey = $this->inputs->where('key', null)->count();

        if ($inputsWithoutKey > 1 || ($inputsWithoutKey > 0 && $this->inputs->count() > 1)) {
            throw new \Exception('Only one input without a key can exist!');
        }
    }

    public function jsonSerialize()
    {
        return $this->inputs;
    }
}
