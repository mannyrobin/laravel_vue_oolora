<?php

namespace DanTheCoder\SaaSCore\Account\Requests;

use Hash;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Password rules and general rules
        if ( $this->current_password && $this->password ) {
            return [
                'current_password'  => 'required',
                'password'          => 'bail|required|min:6|confirmed',
            ];
        }
        else {
            return [
                'name'  => 'bail|required|string|max:255',
                'email' => 'bail|required|string|email|max:255',
            ];   
        }
    }


    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // Only run if the initial validation rules was success
        if ( ! $validator->fails() ) {

            $validator->after(function ($validator) {
                
                if ( $this->current_password && $this->password ) {

                    // The current password doesn't match what is in the DB
                    if ( ! (Hash::check($this->current_password, $this->user()->password)) )
                        $validator->errors()->add('current_password', 'Your current password is incorrect.');


                    // Current password and new password are the same
                    if( strcmp($this->current_password, $this->password) == 0 )
                        $validator->errors()->add('password', 'Your new password cannot be the same as your current active password.');

                }

            });

        }
    }
}