<?php

namespace App\JsonApi\V1\Relationships;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class RelationshipRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'description' => 'required',
        ];
    }

}
