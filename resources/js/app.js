import { Chart } from "chart.js/auto";
import L from "leaflet";
import "./preload";

window.Chart = Chart;

// =========================
// GLOBAL CLEANUP
// =========================
function cleanupGlobalCharts() {
    if (window.__robMainChart) {
        try { window.__robMainChart.destroy(); } catch (_) {}
        window.__robMainChart = null;
    }

    if (window.__robMetricChart) {
        try { window.__robMetricChart.destroy(); } catch (_) {}
        window.__robMetricChart = null;
    }
}

document.addEventListener("livewire:navigated", cleanupGlobalCharts);
window.addEventListener("beforeunload", cleanupGlobalCharts);

// =========================
// COLORS
// =========================
const SENSOR_COLORS = {
    suhu:            { border: "#fb923c", bg: "rgba(251,146,60,0.18)"  },
    kelembapan:      { border: "#22d3ee", bg: "rgba(43,211,238,0.18)"  },
    tekanan_udara:   { border: "#34d399", bg: "rgba(52,211,153,0.18)"  },
    kecepatan_angin: { border: "#fbbf24", bg: "rgba(251,191,36,0.18)"  },
    arah_angin:      { border: "#a78bfa", bg: "rgba(167,139,250,0.18)" },
    ketinggian_air:  { border: "#38bdf8", bg: "rgba(56,189,248,0.18)"  },
};

// =========================
// CHART GLOBAL STATE
// =========================
window.__robMainChart   = null;
window.__robMetricChart = null;

function applyChartPayload(payload) {
    const chart = window.__robMainChart;
    if (!chart) return;

    const labels = payload.labels ?? [];
    const values = payload.values ?? [];
    const metric = payload.metric || "ketinggian_air";
    const color  = SENSOR_COLORS[metric] ?? SENSOR_COLORS.ketinggian_air;

    chart.data.labels                       = labels;
    chart.data.datasets[0].data            = values;
    chart.data.datasets[0].borderColor     = color.border;
    chart.data.datasets[0].backgroundColor = color.bg;

    chart.update("none");
}

// =========================
// METRIC CHART (modal)
// =========================
function renderMetricChart(payload) {
    const canvas = document.getElementById("metricChart");
    if (!canvas) return;

    if (window.__robMetricChart) {
        try { window.__robMetricChart.destroy(); } catch (_) {}
        window.__robMetricChart = null;
    }

    const metric = payload.metric || "ketinggian_air";
    const color  = SENSOR_COLORS[metric] ?? SENSOR_COLORS.ketinggian_air;

    window.__robMetricChart = new Chart(canvas.getContext("2d"), {
        type: "line",
        data: {
            labels: payload.labels ?? [],
            datasets: [{
                label:           payload.title ?? "Metric",
                data:            payload.values ?? [],
                tension:         0.4,
                fill:            true,
                borderWidth:     2,
                borderColor:     color.border,
                backgroundColor: color.bg,
            }],
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            animation:           false,
        },
    });
}

// ✅ Named functions agar bisa di-removeEventListener
function handleModalChart(e)       { renderMetricChart(e.detail || {}); }
function handleDestroyModalChart() {
    if (window.__robMetricChart) {
        try { window.__robMetricChart.destroy(); } catch (_) {}
        window.__robMetricChart = null;
    }
}

// ✅ Remove dulu sebelum add — cegah duplicate saat HMR / Livewire re-init
window.removeEventListener("modalChart",        handleModalChart);
window.removeEventListener("destroyModalChart", handleDestroyModalChart);
window.addEventListener("modalChart",           handleModalChart);
window.addEventListener("destroyModalChart",    handleDestroyModalChart);

// =========================
// ALPINE COMPONENTS
// =========================
document.addEventListener("alpine:init", () => {

    // ----------------------
    // DASHBOARD CHART
    // ----------------------
    Alpine.data("dashboard", () => ({
        _chartHandler: null,

        init() {
            this.$nextTick(() => {
                const canvas = this.$refs.waterChart;
                if (!canvas) return;

                if (window.__robMainChart) {
                    try { window.__robMainChart.destroy(); } catch (_) {}
                    window.__robMainChart = null;
                }

                window.__robMainChart = new Chart(canvas.getContext("2d"), {
                    type: "line",
                    data: {
                        labels: [],
                        datasets: [{
                            label:           "Grafik Sensor",
                            data:            [],
                            tension:         0.4,
                            fill:            true,
                            borderWidth:     2,
                            borderColor:     SENSOR_COLORS.ketinggian_air.border,
                            backgroundColor: SENSOR_COLORS.ketinggian_air.bg,
                        }],
                    },
                    options: {
                        responsive:          true,
                        maintainAspectRatio: false,
                        animation:           false,
                    },
                });

                // ✅ Simpan reference agar bisa di-remove saat destroy
                this._chartHandler = (e) => applyChartPayload(e.detail || {});
                window.addEventListener("refreshChart", this._chartHandler);
            });
        },

        destroy() {
            if (this._chartHandler) {
                window.removeEventListener("refreshChart", this._chartHandler);
                this._chartHandler = null;
            }
            cleanupGlobalCharts();
        },
    }));

    // ----------------------
    // WINDY MAP
    // ----------------------
    Alpine.data("windyMapComponent", (cfg) => ({
        map:                null,
        markersLayer:       null,
        _resizeHandler:     null,
        _visibilityHandler: null,
        _intersectionObs:   null,
        _windyReady:        false,

        init() {
            const container = document.getElementById("windy");
            if (!container) return;

            // ✅ Lazy init — hanya mulai load Windy saat section masuk viewport
            // Ini mencegah Windy memakan memory saat user belum scroll ke peta
            this._intersectionObs = new IntersectionObserver(
                (entries) => {
                    if (!entries[0].isIntersecting) return;
                    this._intersectionObs.disconnect();
                    this._intersectionObs = null;
                    this._loadWindy(cfg);
                },
                { threshold: 0.1 }
            );

            this._intersectionObs.observe(container);
        },

        async _loadWindy(cfg) {
            // Tunggu 2 frame agar DOM benar-benar siap
            await new Promise((r) =>
                requestAnimationFrame(() => requestAnimationFrame(r))
            );

            // ✅ Jangan append script Windy kalau sudah ada di <head>
            if (!document.querySelector('script[src*="libBoot.js"]')) {
                await new Promise((resolve) => {
                    const script   = document.createElement("script");
                    script.src     = "https://api.windy.com/assets/map-forecast/libBoot.js";
                    script.onload  = resolve;
                    script.onerror = resolve; // jangan hang kalau gagal load
                    document.head.appendChild(script);
                });
            } else {
                // Script sudah ada, tunggu windyInit tersedia via polling
                await new Promise((resolve) => {
                    if (window.windyInit) return resolve();
                    const check = setInterval(() => {
                        if (window.windyInit) {
                            clearInterval(check);
                            resolve();
                        }
                    }, 50);
                });
            }

            // ✅ Guard: jangan double-init kalau sudah ada map
            if (this.map || !window.windyInit) return;

            window.windyInit(
                {
                    key:  cfg.key,
                    lat:  cfg.lat,
                    lon:  cfg.lon,
                    zoom: cfg.zoom,
                },
                (windyAPI) => {
                    this.map         = windyAPI.map;
                    this._windyReady = true;

                    const LLeaflet    = window.L;
                    this.markersLayer = LLeaflet.layerGroup().addTo(this.map);

                    // ✅ Resize handler
                    this._resizeHandler = () => this.map?.invalidateSize();
                    window.addEventListener("resize", this._resizeHandler);

                    // ✅ Kurangi activity Windy saat tab tidak aktif
                    // Windy tidak punya pause API resmi, tapi kita bisa
                    // lepas resize listener saat hidden dan pasang kembali saat visible
                    this._visibilityHandler = () => {
                        if (!this.map) return;
                        if (document.hidden) {
                            window.removeEventListener("resize", this._resizeHandler);
                        } else {
                            window.addEventListener("resize", this._resizeHandler);
                            this.map.invalidateSize();
                        }
                    };
                    document.addEventListener("visibilitychange", this._visibilityHandler);
                }
            );
        },

        destroy() {
            // ✅ Stop IntersectionObserver kalau user navigate sebelum scroll ke peta
            if (this._intersectionObs) {
                this._intersectionObs.disconnect();
                this._intersectionObs = null;
            }

            // ✅ Remove semua event listener
            if (this._resizeHandler) {
                window.removeEventListener("resize", this._resizeHandler);
                this._resizeHandler = null;
            }

            if (this._visibilityHandler) {
                document.removeEventListener("visibilitychange", this._visibilityHandler);
                this._visibilityHandler = null;
            }

            // ✅ Bersihkan markers layer
            if (this.markersLayer) {
                try { this.markersLayer.clearLayers(); } catch (_) {}
                this.markersLayer = null;
            }

            // ✅ Destroy map — bebaskan WebGL context & tile cache Windy
            if (this.map) {
                try { this.map.remove(); } catch (_) {}
                this.map = null;
            }

            this._windyReady = false;
        },
    }));

});

// =========================
// LEAFLET ICON FIX
// =========================
try {
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: new URL("leaflet/dist/images/marker-icon-2x.png", import.meta.url).href,
        iconUrl:       new URL("leaflet/dist/images/marker-icon.png",    import.meta.url).href,
        shadowUrl:     new URL("leaflet/dist/images/marker-shadow.png",  import.meta.url).href,
    });
} catch (_) {}