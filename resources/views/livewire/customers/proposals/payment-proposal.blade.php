<div class="wrapper w-full md:max-w-5xl mx-auto pt-20 px-4 mb-5">
    <form wire:submit="submit">
        {{ $this->form }}

        <button type="submit" class="bg-primary-500 hover:bg-primary-300 text-white text-sm font-bold py-2 px-4 rounded-lg mt-4">
            Concluir
        </button>

        <x-filament-actions::modals />

    </form>

</div>
