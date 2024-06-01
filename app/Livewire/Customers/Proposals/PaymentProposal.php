<?php

namespace App\Livewire\Customers\Proposals;

use App\Enums\ProposalStatus;
use App\Models\Customer;
use App\Models\Proposal;
use App\Services\Asaas\AsaasService;
use Exception;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Illuminate\Support\Str;

class PaymentProposal extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    public Proposal $proposal;

    public ?array $data = [];
    public bool $isLoading = false;

    public function mount(string $encryptedId)
    {
        try {
            $this->proposal = Proposal::with('customer', 'pet')
                ->where('status', ProposalStatus::Pending)
                ->findOrFail(Crypt::decrypt($encryptedId));
        } catch (\Exception $e) {
            abort(404);
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados Pessoais para Cobrança')
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer.document')
                            ->label('CPF')
                            ->default($this->proposal->customer->document)
                            ->mask('999.999.999-99')
                            ->length(14)
                            ->required(),
                        TextInput::make('customer.name')
                            ->label('Nome')
                            ->default($this->proposal->customer->name)
                            ->required(),
                        TextInput::make('customer.email')
                            ->label('E-mail')
                            ->email()
                            ->default($this->proposal->customer->email)
                            ->required(),
                        TextInput::make('customer.phone')
                            ->label('Telefone')
                            ->default($this->proposal->customer->phone)
                            ->mask('(99) 99999-9999')
                            ->length(15)
                            ->required(),

                    ]),

                Section::make('Endereço de Cobrança')
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer.zip')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->required(),
                        TextInput::make('customer.city')
                            ->label('Cidadde')
                            ->default($this->proposal->customer->name)
                            ->required(),
                        TextInput::make('customer.street')
                            ->label('Rua')
                            ->default($this->proposal->customer->name)
                            ->required(),
                        TextInput::make('customer.number')
                            ->label('Número')
                            ->required(),
                        TextInput::make('customer.complement')
                            ->label('Complemento'),
                    ]),

                Section::make('Dados do Cartão de Crédito')
                    ->columns(2)
                    ->schema([
                        TextInput::make('creditCard.name')
                            ->label('Nome no Cartão')
                            ->required(),
                        TextInput::make('creditCard.number')
                            ->label('Número do Cartão')
                            ->mask('9999 9999 9999 9999')
                            ->length(19)
                            ->required(),
                        TextInput::make('creditCard.expiryMonth')
                            ->label('Mês de Expiração')
                            ->length(2)
                            ->required(),
                        TextInput::make('creditCard.expiryYear')
                            ->length(2)
                            ->label('Ano de Expiração')
                            ->required(),
                        TextInput::make('creditCard.ccv')
                            ->label('CCV')
                            ->required(),
                    ]),

            ])
            ->statePath('data');
    }




    public function submit()
    {

        if ($this->isLoading) {
            return;
        }

        $this->isLoading = true;

        $asaasService = new AsaasService();

        try {
            $asaasService->findOrCreateCustomer(
                [
                    'name' => $this->data['customer']['name'],
                    'email' => $this->data['customer']['email'],
                    'mobilePhone' => $this->data['customer']['phone'],
                    'cpfCnpj' => $this->data['customer']['document'],
                    'externalReference' => $this->proposal->customer->id,
                ]
            );
        } catch (Exception $e) {
            $this->isLoading = false;
            $this->dispatch('alert', [
                'title' => 'Erro ao Criar Cliente!',
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
            return;
        }

        try {
            $asaasService->createSignature(
                [
                    "billingType" => "CREDIT_CARD",
                    "cycle" => "MONTHLY",
                    "creditCard" => [
                        "holderName" => $this->data['creditCard']['name'],
                        "number" => Str::remove([' ', '.'], $this->data['creditCard']['number']),
                        "expiryMonth" => $this->data['creditCard']['expiryMonth'],
                        "expiryYear" => "20{$this->data['creditCard']['expiryYear']}",
                        "ccv" => $this->data['creditCard']['ccv'],
                    ],
                    "creditCardHolderInfo" => [
                        "name" => $this->data['customer']['name'],
                        "email" => $this->data['customer']['email'],
                        "cpfCnpj" => Str::remove(['.', '-'], $this->data['customer']['document']),
                        "postalCode" => $this->data['customer']['zip'],
                        "addressNumber" => $this->data['customer']['number'],
                        "addressComplement" => $this->data['customer']['complement'],
                        "phone" => Str::remove(['(', ')', ' ', '-'], $this->data['customer']['phone']),
                        "mobilePhone" => Str::remove(['(', ')', ' ', '-'], $this->data['customer']['phone'])
                    ],
                    "customer" => $asaasService->asaassCustomer['id'],
                    "nextDueDate" => now()->format('Y-m-d'),
                    "value" => $this->proposal->plan->getPrice(),
                    "description" => "Assinatura AupetHeinsten - Plano {$this->proposal->plan->getLabel()} - Pet {$this->proposal->pet->name}",
                ]
            );
        } catch (Exception $e) {
            $this->isLoading = false;
            $this->dispatch('alert', [
                'title' => 'Erro ao Criar Assinatura',
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
            return;
        }

        $this->isLoading = false;

        $this->dispatch('alert', [
            'title' => 'Sucesso!',
            'type' => 'success',
            'message' => 'Assinatura Realizada com Sucesso!',
        ]);
    }
    public function render()
    {
        return view('livewire.customers.proposals.payment-proposal');
    }
}
