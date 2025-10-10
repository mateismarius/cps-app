// resources/views/filament/pages/dashboard.blade.php

<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="$this->getColumns()"
        />
    </div>
</x-filament-panels::page>
