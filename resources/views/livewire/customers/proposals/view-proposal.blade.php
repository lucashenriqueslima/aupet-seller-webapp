<div class="wrapper w-full md:max-w-5xl mx-auto pt-20 px-4">
    <h1 class="text-xl font-medium">
        Proposta de Adesão Plano <b>{{ $this->proposal->plan->getLabel() }}</b> - R${{  number_format($this->proposal->plan->getPrice(), 2, ',', '.') }} <br>
        {{ $this->proposal->pet->name }} - {{ $this->proposal->customer->name }}
    </h1>
    {{ $this->proposalInfoList }}

    <div class="flex items-center my-4">
        <input wire:model.live='checkedTermsAndConditions' id="link-checkbox" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
        <label for="link-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Eu aceito os <a href="{{ route('download.termo') }}" class="text-blue-600 dark:text-blue-500 hover:underline">termos e condições</a>.</label>
    </div>

    <a href="{{ route('customers.proposals.payment', ['encryptedId' => $encryptedId]) }}" class="bg-primary-500 hover:bg-primary-300 text-white text-sm font-bold py-2 px-4 rounded-lg mt-4 @if(!$checkedTermsAndConditions) opacity-50 cursor-not-allowed pointer-events-none @endif">
        Prosseguir
    </a>

    <x-filament-actions::modals />
</div>
