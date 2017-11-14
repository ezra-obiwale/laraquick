<?php
namespace Laraquick\Controllers\Traits\Crud;

use Illuminate\Support\Facades\Validator;

trait Validation {
    
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
     */
    protected function checkRequestData(array $data, array $rules, $ignoreStrict = false)
    {
        $this->validator = Validator::make($data, $rules);
        if ($this->validator->fails())
            return $this->validationError($this->validator->errors());

        if (!$ignoreStrict && $this->strictValidation()) {
            $left_overs = collection($data)->except(array_keys($rules));
            if ($left_overs->count())
                return $this->error('Too many parameters', null, 406);
        }
    }
    
    /**
     * Should return the validation rules for when using @see store() and @see update().
     *
     * @param array $data The data being validated
     * @param mixed $id Id of the model being updated, if such were the case
     * @return array
     */
    abstract protected function validationRules(array $data, $id = null);
}