<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait Validation
{

    /**
     * Checks the data against validation rules
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return array
     */
    protected function validateData(array $data, array $rules, array $messages = []): array
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Arr::only($data, array_keys($rules));
    }

    /**
     * Checks the request data against validation rules
     *
     * @param array $rules
     * @param array $messages
     * @return array
     */
    protected function validateRequest(array $rules = null, array $messages = null): array
    {
        $data = request()->all();

        return $this->validateData(
            $data,
            $rules ?: $this->validationRules($data),
            $messages ?: $this->validationMessages($data)
        );
    }

    /**
     * Should return the validation rules for when using @see store() and @see update().
     *
     * @param array $data The data being validated
     * @param mixed $id Id of the model being updated, if such were the case
     * @return array
     */
    protected function validationRules(array $data, $id = null): array
    {
        return [];
    }

    /**
     * Should return the validation rules for when using @see storeMany()
     *
     * @param array $data The data being validated
     * @param mixed $id Id of the model being updated, if such were the case
     * @return array
     */
    final protected function manyValidationRules(array $data, $id = null): array
    {
        $rules = $this->validationRules($data, $id);

        return $this->manyrize($rules);
    }

    /**
     * The validation messages to use with the @see validationRules()
     *
     * @param array $data The data being validated
     * @param mixed $id The id of the model being updated, if such were the case
     * @return array
     */
    protected function validationMessages(array $data, $id = null): array
    {
        return [];
    }

    /**
     * The validation messages to use with the @see manyValidationRules()
     *
     * @param array $data The data being validated
     * @param mixed $id Id of the model being updated, if such were the case
     * @return array
     */
    final protected function manyValidationMessages(array $data, $id = null): array
    {
        $messages = $this->validationMessages($data, $id);

        return $this->manyrize($messages);
    }

    private function manyrize($array): array
    {
        $manyrized = [];

        foreach ($array as $key => $value) {
            $manyrized['many.*.' . $key] = $value;
        }

        return $manyrized;
    }
}
