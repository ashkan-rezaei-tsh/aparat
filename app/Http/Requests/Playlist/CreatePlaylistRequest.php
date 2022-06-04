<?php

namespace App\Http\Requests\Playlist;

use App\Rules\UniqueForUser;
use Illuminate\Foundation\Http\FormRequest;

class CreatePlaylistRequest extends FormRequest
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
		return [
			'title' => ['required', 'string', 'min:2', 'max:200', new UniqueForUser('playlist')],
		];
	}
}
