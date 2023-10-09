@php
    $heading = "Orçamento Mensal";
@endphp

<x-filament-widgets::widget>
    <x-filament::section :heading="$heading" class="fi-wi-chart">
        <div class="grid grid-cols-1 gap-x-4 gap-y-2 lg:grid-cols-2">
            @foreach ($items as $item)
                <div class="flex flex-col">
                    <div class="w-full text-gray-400">{{ $item->name }}</div>
                    <div class="relative flex flex-col items-center group">
                        <div class="w-full bg-gray-700 mx-auto rounded-full overflow-hidden">
                            <div class="{{  $item->percentage > 100 ? 'from-rose-500 to-red-500'
                                            : ($item->percentage > 80 && $item->percentage < 100 ? 'from-yellow-500 to-amber-500'
                                            : 'from-emerald-500 to-green-500') }}
                                            bg-gradient-to-r from-primary-500 text-xs leading-none text-center font-medium p-1 text-gray-950"
                                            style="width: {{ $item->percentage > 100 ? 100 : $item->percentage }}%">
                                {{ $item->percentage }}%
                            </div>
                            <div class="absolute bottom-0 right-10 flex-col items-center hidden mb-6 group-hover:flex">
                                <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg">R$ {{ number_format($item->budget_used, 2, ',', '.') }} / R$ {{ number_format($item->budget_limit, 2, ',', '.') }}</span>
                                <div class="w-3 h-3 -mt-2 rotate-45 bg-black"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
