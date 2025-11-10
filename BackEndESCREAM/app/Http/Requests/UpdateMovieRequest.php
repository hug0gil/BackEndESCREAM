<?php

namespace App\Http\Requests;


class UpdateMovieRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => ["required", "string", "max:255"],
            "year" => ["required", "integer", "digits:4"],
            "synopsis" => ["required", "string", "max:255"],
            "cover" => ["nullable", "string", "max:255"],
            "rating" => ["nullable", "numeric"],
            "director_id" => ["required", "integer", "exists:directors,id"],
            "production_company_id" => ["required", "integer", "exists:production_companies,id"],

            "actor_ids" => ["sometimes", "array"],
            "actor_ids.*" => ["integer", "exists:actors,id"],

            "subgenre_ids" => ["required", "array", "min:1"],
            "subgenre_ids.*" => ["integer", "exists:subgenres,id"],
        ];
    }

    public function messages()
    {
        return
            [
                'title.required' => 'The title is required.',
                'title.string' => 'The title must be a string.',
                'title.max' => 'The title may not be greater than 255 characters.',

                'year.required' => 'The year is required.',
                'year.integer' => 'The year must be a number.',
                'year.digits' => 'The year must be exactly 4 digits.',

                'synopsis.required' => 'The synopsis is required.',
                'synopsis.string' => 'The synopsis must be a string.',
                'synopsis.max' => 'The synopsis may not be greater than 255 characters.',

                'cover.string' => 'The cover path must be a string.',
                'cover.max' => 'The cover path may not be greater than 255 characters.',

                'rating.numeric' => 'The rating must be a number.',
                'rating.between' => 'The rating must be between 0 and 10.',

                'director_id.required' => 'The director is required.',
                'director_id.integer' => 'The director must be a number.',
                'director_id.exists' => 'The selected director does not exist.',

                'production_company_id.required' => 'The production company is required.',
                'production_company_id.integer' => 'The production company must be a number.',
                'production_company_id.exists' => 'The selected production company does not exist.',

                'actor_ids.array' => 'Actors must be sent as a list.',
                'actor_ids.*.integer' => 'Each actor must be identified by an integer.',
                'actor_ids.*.exists' => 'The selected actor does not exist.',

                'subgenre_ids.required' => 'The movie must have at least one subgenre.',
                'subgenre_ids.array' => 'Subgenres must be sent as a list.',
                'subgenre_ids.*.integer' => 'Each subgenre must be identified by an integer.',
                'subgenre_ids.*.exists' => 'The selected subgenre does not exist.',
            ];
    }
}
