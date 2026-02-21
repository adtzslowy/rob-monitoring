import './bootstrap';
import { Chart } from 'chart.js/auto';
import L from 'leaflet';

window.Chart = Chart;
window.L = L;

document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => ({
        data: {},
        risk: 'AMAN',
        riskScore: 1,
        riskStyles: {},
        chart: null,

        init() {
            console.log("Alpine Init OK");

            const canvas = document.getElementById('waterChart');
            if (!canvas) return;

            if (window._mainChart) {
                this.chart = window._mainChart;
            } else {
                this.chart = new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Water Level',
                            data: [],
                            borderColor: '#38bdf8',
                            backgroundColor: 'rgba(56,189,248,0.1)',
                            tension: 0.4,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });

                window._mainChart = this.chart;
            }

            window.addEventListener('dashboard-updated', (event) => {
                console.log("EVENT MASUK", event.detail);

                const payload = event.detail;

                this.data = payload.data;
                this.risk = payload.risk;
                this.riskScore = payload.riskScore;
                this.riskStyles = payload.riskStyles;
            });

            window.addEventListener('refreshChart', (event) => {
                const chartData = event.detail;

                if (this.chart) {
                    this.chart.data.labels = chartData.labels;
                    this.chart.data.datasets[0].data = chartData.values;
                    this.chart.update();
                }
            });
        }
    }));
});


delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
    iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
    shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
});
