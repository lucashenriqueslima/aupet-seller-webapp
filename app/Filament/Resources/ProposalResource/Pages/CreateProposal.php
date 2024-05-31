<?php

namespace App\Filament\Resources\ProposalResource\Pages;

use App\Filament\Resources\ProposalResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProposal extends CreateRecord
{
    protected static string $resource = ProposalResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        $customer = Customer::create(
            [
                'document' => $data['customer']['document'],
                'email' => $data['customer']['email'],
                'name' => $data['customer']['name'],
                'phone' => $data['customer']['phone']
            ]
        );

        $customer->pets()->create([
            'type' => $data['pet']['type'],
            'size' => $data['pet']['size'],
            'name' => $data['pet']['name'],
            'age' => $data['pet']['age'],
            'breed' => $data['pet']['breed'],
            'color' => $data['pet']['color'],
            'weight' => $data['pet']['weight'],
        ]);

        return static::getModel()::create(
            [
                'customer_id' => $customer->id,
                'pet_id' => $customer->pets->first()->id,
                'user_id' => auth()->id(),
                'plan' => $data['plan'],
            ]
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
