<?php

namespace App\Http\Requests\MovieShow;

use App\Models\MovieShow;
use App\Models\Screen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMovieShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movie_id' => "required|int|exists:movies,id",
            'screen_id' => "required|int|exists:screens,id",
            'duration' => "required|numeric",
            'price' => "required|numeric|decimal:2",
            'scheduled_at' => "required|date|after_or_equal:now",
        ];
    }

    /**
     * Determine custom validation rules that apply after validating the request.
     *
     * @return (callable(Validator ):void)[]
     */
    public function after(): array
    {
        return [
            /**
             * Check is screen related to selected theater
             */
            function (Validator $validator) {
                $screen = Screen::find($this->screen_id);
                $theaterId = $this->route('theater');

                if (!$screen || !$theaterId) {
                    return;
                }

                if ((int) $screen->theater_id !== (int) $theaterId) {
                    $validator->errors()
                        ->add(
                            'screen_id',
                            'The selected screen doe not belongs to this theater.'
                        );
                }
            },

            /**
             * Check is scheduled slot overlapping
             */
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $scheduledAt = $this->scheduled_at;
                $endAt = now()->parse($scheduledAt)->addMinutes($this->duration);

                $overlap = MovieShow::where('screen_id', $this->screen_id)
                    ->where(function ($query) use ($scheduledAt, $endAt) {
                        $query->whereBetween('scheduled_at', [$scheduledAt, $endAt])
                            ->orWhereBetween('end_at', [$scheduledAt, $endAt])
                            ->orWhere(function ($q) use ($scheduledAt, $endAt) {
                                $q->where('scheduled_at', '<=', $scheduledAt)
                                    ->where('end_at', '>=', $endAt);
                            });
                    })->exists();
                if ($overlap) {
                    $validator->errors()
                        ->add(
                            'scheduled_at',
                            'This screen alread has a show during this time slot.'
                        );
                }
            },

            // function (Validator $validator) {
            //     # Check is show duration more than movie
            // },

        ];
    }
}
