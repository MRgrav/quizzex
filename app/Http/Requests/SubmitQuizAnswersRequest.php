<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitQuizAnswersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be authenticated
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'integer', 'exists:questions,id'],
            'answers.*.option_id' => ['nullable', 'integer', 'exists:options,id'],
            'answers.*.answer_text' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'answers.required' => 'At least one answer is required.',
            'answers.array' => 'Answers must be provided as an array.',
            'answers.min' => 'You must provide at least one answer.',
            'answers.*.question_id.required' => 'Each answer must have a question ID.',
            'answers.*.question_id.exists' => 'One or more questions do not exist.',
            'answers.*.option_id.exists' => 'One or more selected options do not exist.',
            'answers.*.answer_text.max' => 'Answer text cannot exceed 1000 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Ensure no duplicate question_ids in answers
            $answers = $this->input('answers', []);
            $questionIds = array_column($answers, 'question_id');

            if (count($questionIds) !== count(array_unique($questionIds))) {
                $validator->errors()->add('answers', 'Each question can only be answered once.');
            }

            // Validate that each answer has either option_id or answer_text
            foreach ($answers as $index => $answer) {
                if (empty($answer['option_id']) && empty($answer['answer_text'])) {
                    $validator->errors()->add(
                        "answers.{$index}",
                        'Each answer must have either an option_id or answer_text.'
                    );
                }
            }
        });
    }
}
