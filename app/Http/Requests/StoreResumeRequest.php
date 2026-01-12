<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'resumeTitle' => 'nullable|string|max:255',
            'resumeType'  => 'required|in:Classic,Modern',
        ];
    }
}
