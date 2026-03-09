@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endassets

<x-filament-widgets::widget>
    <div style="background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #111827;">Lieferungen (kg)</h3>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <select wire:model.live="filterMode" style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; background: white;">
                    <option value="day">Tag</option>
                    <option value="week">Woche</option>
                    <option value="month">Monat</option>
                </select>

                @if ($this->filterMode === 'day')
                    <input type="date" wire:model.live="filterDate" style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; background: white;" />
                @elseif ($this->filterMode === 'week')
                    <select wire:model.live="filterWeek" style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; background: white;">
                        @foreach ($this->getWeekOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @else
                    <select wire:model.live="filterMonth" style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; background: white;">
                        @foreach ($this->getMonthOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        <div
            style="position: relative; height: 300px;"
            wire:key="delivery-chart-{{ $this->filterMode }}-{{ $this->getFilterKey() }}"
            x-data="{
                chart: null,
                init() {
                    this.$nextTick(() => {
                        this.chart = new Chart(this.$refs.canvas, {{ Js::from($this->getChartConfig()) }});
                    });
                },
                destroy() {
                    if (this.chart) { this.chart.destroy(); this.chart = null; }
                }
            }"
        >
            <canvas x-ref="canvas"></canvas>
        </div>
    </div>
</x-filament-widgets::widget>
