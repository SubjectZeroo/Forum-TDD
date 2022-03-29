<?php

namespace App\Http\Requests;

use App\Exceptions\ThrottleRequestsException;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Gate;

class CreatePostForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', new \App\Models\Reply);
    }

    public function failedAuthorization()
    {
        throw new ThrottleRequestsException(
            'Your are replying too frequently, Please teake a break'
        );
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['required', new \App\Rules\SpamFree]
        ];
    }
}
