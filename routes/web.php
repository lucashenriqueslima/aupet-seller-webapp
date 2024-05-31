<?php

use App\Livewire\Customers\Proposals\PaymentProposal;
use App\Livewire\Customers\Proposals\ViewProposal;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cliente/proposta/{encryptedId}', ViewProposal::class)->name('customers.proposals.view');
Route::get('/cliente/proposta/pagamento/{encryptedId}', PaymentProposal::class)->name('customers.proposals.payment');
