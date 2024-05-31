<?php

namespace App\Livewire\Customers\Proposals;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Crypt;

class ViewProposal extends Component implements HasForms, HasInfolists, HasActions
{
    use InteractsWithInfolists;
    use InteractsWithForms;
    use InteractsWithActions;

    public Proposal $proposal;

    public function mount(string $encryptedId)
    {
        try {
            $this->proposal = Proposal::with('customer', 'pet')
                ->where('status', ProposalStatus::Pending)
                ->findOrFail(Crypt::decrypt($encryptedId));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function proposalInfoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->proposal)
            ->schema([
                Section::make('Informações do Pet')
                    ->extraAttributes(['class' => 'mt-4'])
                    ->columns(2)
                    ->schema([
                        TextEntry::make('pet.name')->label('Nome:'),
                        TextEntry::make('pet.type')->label('Espécie:'),
                        TextEntry::make('pet.breed')->label('Raça:'),
                        TextEntry::make('pet.size')->label('Tamanho:'),
                        TextEntry::make('pet.weight')
                            ->getStateUsing(function (Proposal $record): string {
                                return $record->pet->weight . ' kg';
                            })
                            ->label('Peso:'),
                        TextEntry::make('pet.age')
                            ->label('Idade:')
                            ->getStateUsing(function (Proposal $record): string {
                                return $record->pet->age . ' anos';
                            }),
                        TextEntry::make('pet.color')->label('Cor:'),
                        TextEntry::make('pet.age_category')
                            ->label('Categoria de Idade:')
                            ->getStateUsing(function (Proposal $record): string {
                                return $record->pet->age <= 7 ? 'Junior' : 'Senior';
                            }),
                    ]),
            ]);
    }

    public function proceedButton(): Action
    {
        return Action::make('delete')
            ->label('Prosseguir')
            ->extraAttributes(['class' => 'mt-4'])
            ->url(fn (): string => route('customers.proposals.payment', ['encryptedId' => Crypt::encrypt($this->proposal->id)]))
            ->requiresConfirmation();
    }


    public function render()
    {
        return view('livewire.customers.proposals.view-proposal');
    }
}
