<x-filament-panels::page>
    <div class="grid gap-5">
        <form>
            {{ $this->form }}


        </form>
        <div class="mt-3">
            {{ $this->confirmRestore }}
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>