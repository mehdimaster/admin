<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;

class uniqueMobile implements Rule
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
        $user = User::where('mobile', $value)
            ->whereNotNull('mobile')
            ->where('mobile', '!=', '')
            ->first();

        if (! is_object($user)) {
            return true;
        }

        if ($user->active == 0) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('common.existMobile');
    }
}
