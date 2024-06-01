<?php

use App\Livewire\Customers\Proposals\PaymentProposal;
use App\Livewire\Customers\Proposals\ViewProposal;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    echo "Site em manutenção";
    return;
});

Route::get('/cliente/proposta/{encryptedId}', ViewProposal::class)->name('customers.proposals.view');
Route::get('/cliente/proposta/pagamento/{encryptedId}', PaymentProposal::class)->name('customers.proposals.payment');
Route::get('/download/termo', function () {
    return response()->download("/home/aupetheinstencom/aupet-seller-webapp/storage/app/public/termo.pdf");
})->name('download.termo');
