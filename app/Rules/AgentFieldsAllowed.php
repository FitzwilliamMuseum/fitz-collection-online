<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AgentFieldsAllowed implements Rule
{

    private array $_params = array('admin.id','admin.created','admin.modified','name','summary_title');

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !Validator::make(
            [
                "{$attribute}" => explode(',', Str::lower($value))
            ],
            [
                "{$attribute}.*" => 'in:' . implode(',', $this->_params)
            ]
        )->fails();
        if(!$value){
            return false;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The listed field parameters are invalid - they can only include the following: ' . implode(',', $this->_params);
    }
}
