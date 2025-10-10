// resources/views/filament/pages/invoice-generator.blade.php

<x-filament-panels::page>
    <form wire:submit.prevent="generateInvoice">
        {{ $this->form }}

        @if($this->clientId || $this->subcontractorId)
            <x-filament::section class="mt-6">
                <x-slot name="heading">
                    Uninvoiced Timesheets
                </x-slot>

                <div class="space-y-4">
                    @forelse($this->getUninvoicedTimesheets() as $timesheet)
                        <label class="flex items-center gap-3 p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="selectedTimesheets"
                                value="{{ $timesheet->id }}"
                                class="rounded border-gray-300"
                            />
                            <div class="flex-1">
                                <div class="font-medium">
                                    {{ $timesheet->worker->full_name }} - {{ $timesheet->project->name }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $timesheet->work_date->format('d/m/Y') }} -
                                    {{ $timesheet->hours_worked }} hours @
                                    £{{ number_format($timesheet->rate_amount, 2) }} =
                                    <span class="font-semibold">£{{ number_format($timesheet->calculateAmount(), 2) }}</span>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            No uninvoiced timesheets found.
                        </div>
                    @endforelse

                    @if($this->getUninvoicedTimesheets()->isNotEmpty())
                        <div class="flex justify-between items-center pt-4 border-t">
                            <div class="text-lg font-semibold">
                                Total: £{{ number_format(
                                    $this->getUninvoicedTimesheets()
                                        ->whereIn('id', $this->selectedTimesheets)
                                        ->sum(fn($t) => $t->calculateAmount()),
                                    2
                                ) }}
                            </div>
                            <x-filament::button
                                type="submit"
                                :disabled="empty($this->selectedTimesheets)"
                            >
                                Generate Invoice
                            </x-filament::button>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endif
    </form>
</x-filament-panels::page>
