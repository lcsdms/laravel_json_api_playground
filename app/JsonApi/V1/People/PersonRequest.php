<?php

namespace App\JsonApi\V1\People;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class PersonRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:200',
            'socialName' => 'nullable|string|max:200',
            'birthDate' => 'nullable|date',
            'documentNumber' => 'nullable|string|max:32',
            'emails'=> JsonApiRule::toMany()
        ];
    }

}
