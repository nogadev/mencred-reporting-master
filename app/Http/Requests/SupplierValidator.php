<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierValidator extends FormRequest
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
        $perceptionIIBB = $this->request->get('perception_iibb');
        return [
            'code' => 'required|unique:suppliers,code,'.$this->id.',id',
            'name' => 'required',
            'perception_iibb' => ['max:8',
                Rule::unique('suppliers','perception_iibb')
                    ->ignore($this->id, 'id')
                    ->where('perception_iibb',$perceptionIIBB !== null ? $perceptionIIBB : 0)
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required' => 'Ingrese el código',
            'code.unique' => 'El código ya existe',
            'name.required' => 'Ingrese el nombre',
            'perception_iibb.max' => 'Percepción IIBB: puede ingresar como maximo 8 caracteres',
            'perception_iibb.unique' => 'La percepción IIBB ya existe',
        ];
    }
}
