<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalResource\Pages;
use App\Filament\Resources\ProposalResource\RelationManagers;
use App\Models\Customer;
use App\Models\Proposal;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webbingbrasil\FilamentCopyActions\Tables\Actions\CopyAction;
use Illuminate\Support\Facades\Crypt;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Proposta';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cliente')
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer.name')
                            ->label('Nome')
                            ->required(),
                        TextInput::make('customer.document')
                            ->label('CPF')
                            ->required()
                            ->mask('999.999.999-99')
                            ->length(14),
                        TextInput::make('customer.email')
                            ->label('E-mail')
                            ->email(),
                        TextInput::make('customer.phone')
                            ->label('Telefone')
                            ->mask('(99) 99999-9999')
                            ->length(15),
                    ]),

                Section::make('Pet')
                    ->columns(2)
                    ->schema([
                        TextInput::make('pet.name')
                            ->label('Nome')
                            ->columnSpan(2)
                            ->required(),
                        Select::make('pet.type')
                            ->label('Espécie')
                            ->options([
                                'dog' => 'Cachorro',
                                'cat' => 'Gato',
                            ])
                            ->required(),
                        Select::make('pet.size')
                            ->label('Porte')
                            ->options([
                                'small' => 'Pequeno',
                                'medium' => 'Médio',
                                'large' => 'Grande',
                            ])
                            ->required(),
                        TextInput::make('pet.weight')
                            ->label('Peso')
                            ->numeric()
                            ->required(),
                        TextInput::make('pet.age')
                            ->label('Idade')
                            ->numeric()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set): void {

                                if (!$state) {
                                    return;
                                }

                                $petAgeClass = $state <= 7 ? 'Junior' : 'Senior';

                                Notification::make()
                                    ->title("Classificação Alterada")
                                    ->body("O pet foi classificado como $petAgeClass")
                                    ->info()
                                    ->send();

                                $set('plan', null);
                            }),
                        TextInput::make('pet.breed')
                            ->label('Raça')
                            ->required(),
                        TextInput::make('pet.color')
                            ->label('Cor')
                            ->required(),
                        Select::make('plan')
                            ->columnSpan(2)
                            ->label('Plano')
                            ->disabled(fn (Get $get) => (!$get('pet.age')))
                            ->required()
                            ->live()
                            ->options(function (Get $get): array {
                                $age = $get('pet.age');

                                if ($age <= 7) {
                                    return [
                                        'basic_junior' => 'Vital Junior - até 7 anos - R$ 79,90',
                                        'medium_junior' => 'Exclusivo Junior - até 7 anos - R$ 159,90',
                                        'premium' => 'Supremo - R$ 250,00',
                                    ];
                                }

                                return [
                                    'basic_senior' => 'Vital Senior - mais de 7 anos - R$ 110,90',
                                    'medium_senior' => 'Exclusivo Senior - mais de 7 anos - R$ 190,00',
                                    'premium' => 'Supremo - R$ 250,00',
                                ];
                            }),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('user_id', auth()->id());
            })
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('customer.document')
                    ->label('CPF')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customer.name')
                    ->label('Nome | Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pet.name')
                    ->label('Nome | Pet')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->searchable()
                    ->sortable()
                    ->since(),
            ])
            ->filters([])
            ->actions([
                CopyAction::make('Copiar Link')->copyable(fn (Proposal $record) => route('customers.proposals.view', ['encryptedId' => Crypt::encrypt($record->id)])),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }
}
