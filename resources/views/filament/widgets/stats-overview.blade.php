<x-filament-widgets::widget>
    <div>
        {{-- Filter --}}
        <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem; gap: 0.5rem; align-items: center;">
            <select
                wire:model.live="filterMode"
                style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; background: white;"
            >
                <option value="day">Tag</option>
                <option value="week">Woche</option>
                <option value="month">Monat</option>
            </select>

            @if ($this->filterMode === 'day')
                <input
                    type="date"
                    wire:model.live="filterDate"
                    style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; background: white;"
                />
            @elseif ($this->filterMode === 'week')
                <select
                    wire:model.live="filterWeek"
                    style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; background: white;"
                >
                    @foreach ($this->getWeekOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            @else
                <select
                    wire:model.live="filterMonth"
                    style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; background: white;"
                >
                    @foreach ($this->getMonthOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- Stats Grid --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            @foreach ($this->getStats() as $stat)
                <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <x-filament::icon
                            :icon="$stat['icon']"
                            style="width: 1.5rem; height: 1.5rem; color: #9ca3af;"
                        />
                        <span style="font-size: 0.875rem; font-weight: 500; color: #6b7280;">
                            {{ $stat['label'] }}
                        </span>
                    </div>
                    <div style="margin-top: 0.75rem;">
                        <span style="font-size: 1.5rem; font-weight: 600; color: #111827;">
                            {{ $stat['value'] }}
                        </span>
                    </div>
                    <div style="margin-top: 0.25rem;">
                        <span style="font-size: 0.875rem; color: #6b7280;">
                            {{ $stat['description'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
