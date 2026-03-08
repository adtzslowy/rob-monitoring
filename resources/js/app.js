// resources/js/app.js
import { Chart } from "chart.js/auto";
import L from "leaflet";

window.Chart = Chart;
const leafletFromNpm = L;

function loadScriptOnce(src) {
    return new Promise((resolve, reject) => {
        if ([...document.scripts].some((s) => s.src === src)) {
            resolve();
            return;
        }
        const script = document.createElement("script");
        script.src = src;
        script.async = true;
        script.onload = resolve;
        script.onerror = () => reject(new Error("Failed to load " + src));
        document.head.appendChild(script);
    });
}

// =========================
// Chart globals
// =========================
window.__robMainChart = window.__robMainChart || null;
window.__robMetricChart = window.__robMetricChart || null;
window.__robChartPending = window.__robChartPending || null;
window.__robMetricPending = window.__robMetricPending || null;

function normalizePayload(payload) {
    if (!payload) return {};
    if (payload.labels) return payload;
    if (payload[0]?.labels) return payload[0];
    if (payload?.detail?.labels) return payload.detail;
    return payload;
}

function applyChartPayload(payload) {
    const chart = window.__robMainChart;
    const p = normalizePayload(payload);

    if (!chart) {
        window.__robChartPending = p;
        return;
    }

    const labels = p.labels ?? [];
    const values = p.values ?? [];
    const title = p.title ?? "Water Level";

    chart.data.labels = labels;

    if (!chart.data.datasets.length) {
        chart.data.datasets = [
            {
                label: title,
                data: [],
                tension: 0.4,
                fill: true,
            },
        ];
    }

    chart.data.datasets[0].label = title;
    chart.data.datasets[0].data = values;
    chart.update();
}

window.addEventListener("refreshChart", (e) => applyChartPayload(e.detail || {}));

function renderMetricChart(payload) {
    const p = normalizePayload(payload);
    const canvas = document.getElementById("metricChart");

    if (!canvas) {
        window.__robMetricPending = p;
        return;
    }

    const labels = p.labels ?? [];
    const values = p.values ?? [];

    if (!window.__robMetricChart) {
        window.__robMetricChart = new Chart(canvas.getContext("2d"), {
            type: "line",
            data: {
                labels,
                datasets: [
                    {
                        label: p.title ?? "Trend",
                        data: values,
                        tension: 0.4,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
            },
        });
        return;
    }

    window.__robMetricChart.data.labels = labels;
    window.__robMetricChart.data.datasets[0].label = p.title ?? "Trend";
    window.__robMetricChart.data.datasets[0].data = values;
    window.__robMetricChart.update();
}

window.addEventListener("modalChart", (e) => renderMetricChart(e.detail || {}));

// =========================
// Dashboard Alpine
// =========================
// =========================
// Dashboard Alpine
// =========================
document.addEventListener("alpine:init", () => {
    Alpine.data("dashboard", (liveTheme) => ({
        data: {},
        theme: liveTheme ?? "dark",

        risk: "AMAN",
        riskStyles: {
            bg: "bg-emerald-500/10",
            border: "border-emerald-500/30",
            text: "text-emerald-600",
        },

        init() {
            this.applyTheme(this.theme);

            this.$nextTick(() => {
                const canvas = this.$refs.waterChart;
                if (!canvas) return;

                if (!window.__robMainChart) {
                    window.__robMainChart = new Chart(canvas.getContext("2d"), {
                        type: "line",
                        data: {
                            labels: [],
                            datasets: [
                                {
                                    label: "Water Level",
                                    data: [],
                                    tension: 0.4,
                                    fill: true,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false,
                        },
                    });
                }

                if (window.__robChartPending) {
                    applyChartPayload(window.__robChartPending);
                    window.__robChartPending = null;
                }
            });

            window.addEventListener("dashboard-updated", (e) => {
                this.data = e.detail?.data || {};
                this.risk = e.detail?.risk || "AMAN";
                this.riskStyles = e.detail?.riskStyles || {
                    bg: "bg-emerald-500/10",
                    border: "border-emerald-500/30",
                    text: "text-emerald-600",
                };
            });

            this.$watch("theme", (value) => {
                this.applyTheme(value);
            });
        },

        applyTheme(theme) {
            const nextTheme = theme === "light" ? "light" : "dark";
            this.theme = nextTheme;
            document.documentElement.classList.toggle("dark", nextTheme === "dark");
        },

        toggleTheme() {
            this.theme = this.theme === "dark" ? "light" : "dark";
        },
    }));
});

// =========================
// Windy Map
// =========================
document.addEventListener("alpine:init", () => {
    Alpine.data("windyMapComponent", (cfg) => ({
        key: cfg?.key || "",
        lat: cfg?.lat ?? -6.2,
        lon: cfg?.lon ?? 106.8,
        zoom: cfg?.zoom ?? 9,
        overlay: cfg?.overlay ?? "wind",
        devices: cfg?.devices || [],

        map: null,
        markersLayer: null,
        loading: true,
        error: "",
        _observer: null,
        _vw: null,
        _vh: null,
        __resizeBound: false,

        async init() {
            if (window.__windyMap) {
                this.map = window.__windyMap;
                this.markersLayer = window.__windyMarkers;
                this._watchHidden();
                this.renderMarkers(this.devices);
                this.loading = false;
                this._invalidateSoon();
                this.fitToDevices(this.devices);
                return;
            }

            await this.$nextTick();
            await new Promise((r) => requestAnimationFrame(() => requestAnimationFrame(r)));

            const container = document.getElementById("windy");
            if (!container) {
                this.error = "Container #windy tidak ditemukan";
                return;
            }

            try {
                await loadScriptOnce("https://api.windy.com/assets/map-forecast/libBoot.js");
            } catch (e) {
                this.error = "Gagal load libBoot.js. Cek koneksi internet.";
                return;
            }

            const windyReady = await new Promise((resolve) => {
                let tries = 0;
                const iv = setInterval(() => {
                    tries++;
                    if (typeof window.windyInit === "function") {
                        clearInterval(iv);
                        resolve(true);
                    } else if (tries > 100) {
                        clearInterval(iv);
                        resolve(false);
                    }
                }, 100);
            });

            if (!windyReady) {
                this.error = "Windy API timeout. Cek API key atau koneksi.";
                return;
            }

            const rect = container.getBoundingClientRect();
            const parent = container.parentElement;
            const prect = parent ? parent.getBoundingClientRect() : rect;

            const vw = (rect.width > 0 ? rect.width : prect.width) || window.innerWidth;
            const vh = (rect.height > 0 ? rect.height : prect.height) || (window.innerHeight - 200);

            this._vw = vw;
            this._vh = vh;

            container.style.width = vw + "px";
            container.style.height = vh + "px";
            container.style.display = "block";

            await new Promise((r) => requestAnimationFrame(() => requestAnimationFrame(r)));

            window.windyInit(
                {
                    key: this.key,
                    lat: this.lat,
                    lon: this.lon,
                    zoom: this.zoom,
                    overlay: this.overlay,
                    verbose: false,
                },
                (windyAPI) => {
                    this.map = windyAPI.map;
                    window.__windyMap = this.map;

                    const windyEl = document.getElementById("windy");
                    if (windyEl) {
                        windyEl.classList.remove("hidden", "free-model");
                        windyEl.style.width = this._vw + "px";
                        windyEl.style.height = this._vh + "px";
                        windyEl.style.display = "block";
                    }

                    this._watchHidden(this._vw, this._vh);

                    const LLeaflet = window.L;
                    this.markersLayer = LLeaflet.layerGroup().addTo(this.map);
                    window.__windyMarkers = this.markersLayer;

                    this.renderMarkers(this.devices);
                    this.loading = false;

                    this._bindResize();
                    this._invalidateSoon();
                    this.fitToDevices(this.devices);
                }
            );
        },

        fitToDevices(devices) {
            if (!this.map) return;

            const LLeaflet = window.L;
            const pts = (devices || [])
                .map((d) => [parseFloat(d.lat), parseFloat(d.lng)])
                .filter(([lat, lng]) => Number.isFinite(lat) && Number.isFinite(lng));

            if (!pts.length) return;

            const doFit = () => {
                try {
                    const b = LLeaflet.latLngBounds(pts);

                    if (pts.length === 1) {
                        this.map.setView(pts[0], 13, { animate: false });
                        return;
                    }

                    this.map.fitBounds(b, { padding: [50, 50], animate: false });
                } catch (e) {
                    setTimeout(() => {
                        try {
                            const b = LLeaflet.latLngBounds(pts);
                            this.map.fitBounds(b, { padding: [50, 50], animate: false });
                        } catch (_) {}
                    }, 150);
                }
            };

            try {
                this.map.invalidateSize(true);
            } catch (_) {}

            requestAnimationFrame(() => requestAnimationFrame(doFit));
        },

        _invalidateSoon() {
            [0, 100, 300, 600].forEach((ms) => {
                setTimeout(() => {
                    try {
                        this.map?.invalidateSize(true);
                    } catch (_) {}
                }, ms);
            });
        },

        _bindResize() {
            if (this.__resizeBound) return;
            this.__resizeBound = true;

            const handler = () => {
                const el = document.getElementById("windy");
                if (!el) return;

                const rect = el.getBoundingClientRect();
                if (rect.width > 0 && rect.height > 0) {
                    this._vw = rect.width;
                    this._vh = rect.height;
                    el.style.width = rect.width + "px";
                    el.style.height = rect.height + "px";
                }

                this._invalidateSoon();
            };

            window.addEventListener("resize", handler);
            setTimeout(handler, 50);
        },

        _watchHidden(vw, vh) {
            const windyEl = document.getElementById("windy");
            if (!windyEl || this._observer) return;

            this._observer = new MutationObserver(() => {
                const el = document.getElementById("windy");
                if (!el) return;

                if (el.classList.contains("hidden") || el.style.display === "none") {
                    el.classList.remove("hidden", "free-model");
                    el.style.removeProperty("display");
                }

                const w = vw ?? this._vw;
                const h = vh ?? this._vh;

                if (w && h) {
                    el.style.width = w + "px";
                    el.style.height = h + "px";
                }

                this._invalidateSoon();
            });

            this._observer.observe(windyEl, {
                attributes: true,
                attributeFilter: ["class", "style"],
            });
        },

        onRenderMarkers(event) {
            const devices = event?.detail?.devices || [];
            this.devices = devices;
            this.renderMarkers(devices);
            this._invalidateSoon();
            this.fitToDevices(devices);
        },

        renderMarkers(devices) {
            if (!this.map || !this.markersLayer) return;

            this.markersLayer.clearLayers();
            const LLeaflet = window.L;
            const bounds = [];

            (devices || []).forEach((d) => {
                const lat = parseFloat(d.lat);
                const lng = parseFloat(d.lng);
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

                bounds.push([lat, lng]);

                const status = (d.status || "offline").toLowerCase();
                const isOnline = status === "online";

                const icon = LLeaflet.divIcon({
                    className: "",
                    html: `
                <div style="
                    width:36px;height:36px;
                    background:${isOnline ? "#22c55e" : "#ef4444"};
                    border:3px solid white;border-radius:50%;
                    box-shadow:0 2px 8px rgba(0,0,0,0.3);
                    display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -36],
                });

                const marker = LLeaflet.marker([lat, lng], { icon }).addTo(this.markersLayer);
                const last = d.last_seen ? `<br/><small style="color:#888">Last: ${d.last_seen}</small>` : "";

                marker.bindPopup(`
            <div style="font-family:sans-serif;min-width:140px">
                <div style="font-weight:600;font-size:14px">${d.alias || d.name || "Device"}</div>
                <div style="font-size:12px;color:#888;margin-top:2px">${lat}, ${lng}</div>
                <div style="margin-top:6px;font-size:12px">
                    Status: <span style="color:${isOnline ? "#22c55e" : "#ef4444"};font-weight:600">${status}</span>
                </div>
                ${last}
            </div>
        `);
            });

            if (bounds.length > 0) {
                const latLngBounds = LLeaflet.latLngBounds(bounds);
                this.map.fitBounds(latLngBounds, { padding: [50, 50] });
            }
        },
<<<<<<< HEAD
=======
    }));
});

document.addEventListener("alpine:init", () => {
    Alpine.data("searchableDeviceSelect", (config = {}) => ({
        open: false,
        query: "",
        selected: config.selected || null,
        options: config.options || [],

        init() {
            this.$watch("selected", (value) => {
                if (config.onChange) {
                    config.onChange(value);
                }
            });
        },

        get filteredOptions() {
            const q = (this.query || "").toLowerCase().trim();
            if (!q) return this.options;

            return this.options.filter((item) => {
                const label = (item.label || item.alias || item.name || "").toLowerCase();
                const status = (item.statusLabel || "").toLowerCase();
                return label.includes(q) || status.includes(q);
            });
        },

        get selectedOption() {
            return this.options.find((item) => String(item.id) === String(this.selected)) || null;
        },

        select(item) {
            this.selected = item.id;
            this.query = "";
            this.open = false;
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    this.$refs.searchInput?.focus();
                });
            }
        },

        close() {
            this.open = false;
            this.query = "";
        },
>>>>>>> 0b28abe (updated)
    }));
});

document.addEventListener("alpine:init", () => {
    Alpine.data("searchableDeviceSelect", (config = {}) => ({
        open: false,
        query: "",
        selected: config.selected || null,
        options: config.options || [],

        init() {
            this.$watch("selected", (value) => {
                if (config.onChange) {
                    config.onChange(value);
                }
            });
        },

        get filteredOptions() {
            const q = (this.query || "").toLowerCase().trim();
            if (!q) return this.options;

            return this.options.filter((item) => {
                const label = (item.label || item.alias || item.name || "").toLowerCase();
                const status = (item.statusLabel || "").toLowerCase();
                return label.includes(q) || status.includes(q);
            });
        },

        get selectedOption() {
            return this.options.find((item) => String(item.id) === String(this.selected)) || null;
        },

        select(item) {
            this.selected = item.id;
            this.query = "";
            this.open = false;
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    this.$refs.searchInput?.focus();
                });
            }
        },

        close() {
            this.open = false;
            this.query = "";
        },
    }));
});

// =========================
// Leaflet default icon fix
// =========================
try {
    delete leafletFromNpm.Icon.Default.prototype._getIconUrl;
    leafletFromNpm.Icon.Default.mergeOptions({
        iconRetinaUrl: new URL("leaflet/dist/images/marker-icon-2x.png", import.meta.url).href,
        iconUrl: new URL("leaflet/dist/images/marker-icon.png", import.meta.url).href,
        shadowUrl: new URL("leaflet/dist/images/marker-shadow.png", import.meta.url).href,
    });
} catch (e) {}