<div class="wrapper w-full md:max-w-5xl mx-auto pt-20 px-4">
    <h1 class="text-xl font-medium">
        Proposta de Adesão Plano <b>{{ $this->proposal->plan->getLabel() }}</b>  <br>
        {{ $this->proposal->pet->name }} - {{ $this->proposal->customer->name }}
    </h1>
    {{ $this->proposalInfoList }}
    {{ $this->proceedButton }}

    <x-filament-actions::modals />
</div>
