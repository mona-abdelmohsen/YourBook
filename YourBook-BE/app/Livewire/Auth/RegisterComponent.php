<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class RegisterComponent extends Component
{
    public function render()
    {
        return view('livewire.auth.register-component')->layout('layouts.authv2');
    }


    public function test(){
//        $this->dispatch('alert');
    }

}
