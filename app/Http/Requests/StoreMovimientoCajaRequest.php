<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreMovimientoCajaRequest
 * 
 * Validación de datos para el registro de movimientos de caja.
 * Asegura que el movimiento tenga un tipo válido (ingreso/egreso) y un monto positivo.
 */
class StoreMovimientoCajaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     * 
     * Valida tipo de movimiento, monto y descripción opcional.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
        ];
    }
}
