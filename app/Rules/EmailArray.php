<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EmailArray implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = str_replace(' ','',$value);
        $array = explode(',', $value);
        $email_to_validate = [];
        foreach($array as $email) //loop over values
        {
            $email_to_validate['alert_email'][]=$email;
        }
        $rules = array('alert_email.*'=>'email');
        $validator = Validator::make($email_to_validate,$rules);

        return $validator->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid group of emails';
    }
}
