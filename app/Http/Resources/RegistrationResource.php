<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'student_name'            => $this->student_name,
            'responsible_name'        => $this->responsible_name,
            'indicated_student_name'            => $this->indicated_student_name,
            'indicated_responsible_name'        => $this->indicated_responsible_name,

            'indicated_education_level'         => $this->indicated_education_level,
           
        ];
    }
}