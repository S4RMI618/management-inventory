<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $messages;

    /**
     * @param  string|null  $type       'success' o 'error'. Si es null, el constructor
     *                                  detecta session('success'), session('error') o errores de validación.
     * @param  array|string  $messages  Cadena o array de mensajes a mostrar.
     */
    public function __construct($type = null, $messages = [])
    {
        // Detectar automáticamente si no pasaron un type
        if (is_null($type)) {
            if (session()->has('success')) {
                $type = 'success';
                $messages = [session('success')];
            } elseif (session()->has('error')) {
                $type = 'error';
                $messages = [session('error')];
            } elseif ($errors = session('errors')) {
                $type = 'error';
                $messages = $errors->all();
            }
        }

        $this->type     = $type;
        $this->messages = is_array($messages) ? $messages : [$messages];
    }

    public function render()
    {
        return view('components.alert');
    }
}
