<div
    wire:ignore
    x-data="fluxChart({
        type: @js($type),
        data: { labels: @js($labels), datasets: @js($datasets) },
        options: @js($options),
    })"
    x-init="render()"
    class="border border-zinc-200 bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900"
>
    <canvas id="{{ $canvasId }}" style="height: {{ $height }}; width: 100%;"></canvas>
</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script>
        window.fluxChart = function (config) {
            return {
                chart: null,
                render() {
                    const boot = () => {
                        if (!window.Chart) {
                            setTimeout(boot, 50);
                            return;
                        }
                        const canvas = this.$el.querySelector('canvas');
                        if (!canvas) return;
                        this.chart = new window.Chart(canvas, {
                            type: config.type,
                            data: config.data,
                            options: Object.assign({ maintainAspectRatio: false, responsive: true }, config.options || {}),
                        });
                    };
                    boot();
                },
            };
        };
    </script>
@endonce
