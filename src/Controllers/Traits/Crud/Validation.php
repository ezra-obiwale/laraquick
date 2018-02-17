<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Support\Facades\Validator;

trait Validation
{
    
    /**
     * Validator instance
     *
     * @var Illuminate\Support\Facades\Validator
     */
    protected $validator;

    /**
     * Indicates whether validation should be strict and throw errors if unwanted
     * values exists
     *
     * @return boolean
     */
    protected function strictValidation()
    {
        return false;
    }

    /**
     * Checks the request data against validation rules
     *
     * @param array $data
     * @param array $rules
     * @param boolean $ignoreStrict Indicates whether to ignore strict validation
     * @return void
     * 
     * @deprecated v3.3.4
     */
    protected function checkRequestData(array $data, array $rules, $ignoreStrict = false)
    {
        return $this->validateData($data, $rules);
    }

    /**
     * Checks the data against validation rules
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return void
     */
    protected function validateData(array $data, array $rules, array $messages = [])
    {
        $this->validator = Validator::make($data, $rules, $messages);
        if ($this->validator->fails()) {
            return $this->validationError($this->validator->errors());
        }
    }

    /**
     * Checks the request data against validation rules
     *
     * @param array $rules
     * @param array $messages
     * @return void
     */
    protected function validateRequest(array $rules = null, array $messages = null)
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
    abstract protected function validationRules(array $data, $id = null);

    /**
     * The validation messages to use with the @see validationRules()
     *
     * @param array $data The data being validated
     * @param mixed $id The id of the mode being updated, if such were the case
     * @return array
     */
    protected function validationMessages(array $data, $id = null)
    {
        return [];
    }
}
