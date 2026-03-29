import { Chart } from "chart.js/auto";
import L from "leaflet";
import './preload';

window.Chart = Chart;

const SENSOR_COLORS = {
    suhu: {
        border: "#fb923c",
        bg: "rgba(251,146,60,0.18)",
    },

    kelembapan: {
        border: "#22d3ee",
        bg: "rgba(43,211,238,0.18)",
    },

    tekanan_udara: {
        border: "#34d399",
        bg: "rgba(52,211,153,0.18)",
    },

    kecepatan_angin: {
        border: "#fbbf24",
        bg: "rgba(251,191,36,0.18)",
    },

    arah_angin: {
        border: "#a78bfa",
        bg: "rgba(167,139,250,0.18)",
    },

    ketinggian_air: {
        border: "#38bdf8",
        bg: "rgba(56,189,248,0.18)",
    },
};

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

document.addEventListener("DOMContentLoaded", () => {
    const password = document.getElementById("password");
    const toggle = document.getElementById("togglePassword");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClose = document.getElementById("eyeSlash");

    if (password && toggle) {
        toggle.addEventListener("click", () => {
            const isHidden = password.type === "password";
            password.type = isHidden ? "text" : "password";

            if (eyeOpen && eyeClose) {
                eyeOpen.classList.toggle("hidden", isHidden);
                eyeClose.classList.toggle("hidden", !isHidden);
            }

            toggle.setAttribute(
                "aria-label",
                isHidden ? "Sembunyikan password" : "Tampilkan password",
            );
        });
    }

    const password2 = document.getElementById("password_confirmation");
    const toggle2   = document.getElementById("togglePassword2");
    const eyeOpen2  = document.getElementById("eyeOpen2");
    const eyeClose2 = document.getElementById("eyeSlash2");

    if (password2 && toggle2) {
        toggle2.addEventListener("click", () => {
            const isHidden = password2.type === "password";
            password2.type = isHidden ? "text" : "password";
            if (eyeOpen2 && eyeClose2) {
                eyeOpen2.classList.toggle("hidden", isHidden);
                eyeClose2.classList.toggle("hidden", !isHidden);
            }
        });
    }
});

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
    const metric = p.metric || "ketinggian_air";
    const color = SENSOR_COLORS[metric] || SENSOR_COLORS.ketinggian_air;

    chart.data.labels = labels;

    if (!chart.data.datasets.length) {
        chart.data.datasets = [
            {
                label: title,
                data: [],
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                borderColor: color.border,
                backgroundColor: color.bg,
                pointRadius: 3,
                pointHoverRadius: 4,
                pointBackgroundColor: color.border,
                pointBorderColor: color.border,
                pointHoverBackgroundColor: color.border,
                pointHoverBorderColor: color.border,
            },
        ];
    }

    const ds = chart.data.datasets[0];
    ds.label = title;
    ds.data = values;
    ds.borderColor = color.border;
    ds.backgroundColor = color.bg;
    ds.pointRadius = 3;
    ds.pointHoverRadius = 4;
    ds.pointBackgroundColor = color.border;
    ds.pointBorderColor = color.border;
    ds.pointHoverBackgroundColor = color.border;
    ds.pointHoverBorderColor = color.border;

    chart.update("none");
}

window.addEventListener("refreshChart", (e) =>
    applyChartPayload(e.detail || {}),
);

function flushMetricChartPending() {
    if (!window.__robMetricPending) return;

    const payload = window.__robMetricPending;
    window.__robMetricPending = null;

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            renderMetricChart(payload);
            window.__robMetricChart?.resize();
        });
    });
}

function renderMetricChart(payload) {
    const canvas = document.getElementById("metricChart");

    if (!canvas) {
        window.__robMetricPending = payload;
        return;
    }

    const p = normalizePayload(payload);
    const labels = p.labels ?? [];
    const values = p.values ?? [];
    const title = p.title ?? "Metric";
    const metric = p.metric || "ketinggian_air";
    const color = SENSOR_COLORS[metric] || SENSOR_COLORS.ketinggian_air;

    // Destroy dulu kalau sudah ada
    if (window.__robMetricChart) {
        window.__robMetricChart.destroy();
        window.__robMetricChart = null;
    }

    window.__robMetricChart = new Chart(canvas.getContext("2d"), {
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label: title,
                    data: values,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    borderColor: color.border,
                    backgroundColor: color.bg,
                    pointRadius: 3,
                    pointHoverRadius: 4,
                    pointBackgroundColor: color.border,
                    pointBorderColor: color.border,
                    pointHoverBackgroundColor: color.border,
                    pointHoverBorderColor: "#ffffff",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    ticks: { maxTicksLimit: 8, font: { size: 11 } },
                    grid: { color: "rgba(255,255,255,0.05)" },
                },
                y: {
                    ticks: { font: { size: 11 } },
                    grid: { color: "rgba(255,255,255,0.05)" },
                },
            },
        },
    });

    window.__robMetricCanvas = canvas;
}

window.flushMetricChartPending = flushMetricChartPending;
window.addEventListener("modalChart", (e) => renderMetricChart(e.detail || {}));
window.addEventListener("destroyModalChart", () => {
    if (window.__robMetricChart) {
        window.__robMetricChart.destroy();
        window.__robMetricChart = null;
    }

    window.__robMetricCanvas = null;
    window.__robMetricPending = null;
});

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
                                    label: "Grafik Sensor",
                                    data: [],
                                    tension: 0.4,
                                    fill: true,
                                    borderWidth: 2,
                                    pointRadius: 3,
                                    pointHoverRadius: 4,
                                    borderColor:
                                        SENSOR_COLORS.ketinggian_air.border,
                                    backgroundColor:
                                        SENSOR_COLORS.ketinggian_air.bg,
                                    pointBackgroundColor:
                                        SENSOR_COLORS.ketinggian_air.border,
                                    pointBorderColor:
                                        SENSOR_COLORS.ketinggian_air.border,
                                    pointHoverBackgroundColor:
                                        SENSOR_COLORS.ketinggian_air.border,
                                    pointHoverBorderColor: "#ffffff",
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
            document.documentElement.classList.toggle(
                "dark",
                nextTheme === "dark",
            );
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
            await new Promise((r) =>
                requestAnimationFrame(() => requestAnimationFrame(r)),
            );

            const container = document.getElementById("windy");
            if (!container) {
                this.error = "Container #windy tidak ditemukan";
                return;
            }

            try {
                await loadScriptOnce(
                    "https://api.windy.com/assets/map-forecast/libBoot.js",
                );
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

            const vw =
                (rect.width > 0 ? rect.width : prect.width) ||
                window.innerWidth;
            const vh =
                (rect.height > 0 ? rect.height : prect.height) ||
                window.innerHeight - 200;

            this._vw = vw;
            this._vh = vh;

            container.style.width = vw + "px";
            container.style.height = vh + "px";
            container.style.display = "block";

            await new Promise((r) =>
                requestAnimationFrame(() => requestAnimationFrame(r)),
            );

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
                },
            );
        },

        fitToDevices(devices) {
            if (!this.map) return;

            const LLeaflet = window.L;
            const pts = (devices || [])
                .map((d) => [parseFloat(d.lat), parseFloat(d.lng)])
                .filter(
                    ([lat, lng]) =>
                        Number.isFinite(lat) && Number.isFinite(lng),
                );

            if (!pts.length) return;

            const doFit = () => {
                try {
                    const b = LLeaflet.latLngBounds(pts);

                    if (pts.length === 1) {
                        this.map.setView(pts[0], 13, { animate: false });
                        return;
                    }

                    this.map.fitBounds(b, {
                        padding: [50, 50],
                        animate: false,
                    });
                } catch (e) {
                    setTimeout(() => {
                        try {
                            const b = LLeaflet.latLngBounds(pts);
                            this.map.fitBounds(b, {
                                padding: [50, 50],
                                animate: false,
                            });
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

                if (
                    el.classList.contains("hidden") ||
                    el.style.display === "none"
                ) {
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

                const isOnline =
                    (d.status || "offline").toLowerCase() === "online";
                const risiko = d.status_risiko ?? "UNKNOWN";

                const risikoColor =
                    {
                        AMAN: "#22c55e",
                        WASPADA: "#f59e0b",
                        SIAGA: "#f97316",
                        BAHAYA: "#ef4444",
                        UNKNOWN: "#94a3b8",
                    }[risiko] ?? "#94a3b8";

                const risikoBg =
                    {
                        AMAN: "#f0fdf4",
                        WASPADA: "#fefce8",
                        SIAGA: "#fff7ed",
                        BAHAYA: "#fef2f2",
                        UNKNOWN: "#f8fafc",
                    }[risiko] ?? "#f8fafc";

                // Icon marker warna ikut status risiko
                const icon = LLeaflet.divIcon({
                    className: "",
                    html: `
                <div style="
                    width:38px;height:38px;
                    background:${risikoColor};
                    border:3px solid white;
                    border-radius:50%;
                    box-shadow:0 2px 12px rgba(0,0,0,0.25);
                    display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>`,
                    iconSize: [38, 38],
                    iconAnchor: [19, 38],
                    popupAnchor: [0, -42],
                });

                const marker = LLeaflet.marker([lat, lng], { icon }).addTo(
                    this.markersLayer,
                );

                // Sensor grid HTML
                const s = d.sensor;
                const sensorHtml = s
                    ? `
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-top:10px;">
                <div style="background:#f0f9ff;border-radius:10px;padding:8px;text-align:center;">
                    <div style="font-size:9px;color:#0284c7;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Ketinggian</div>
                    <div style="font-size:16px;font-weight:800;color:#0f172a;margin-top:2px;">${s.ketinggian_air ?? "-"}</div>
                    <div style="font-size:9px;color:#94a3b8;">cm</div>
                </div>
                <div style="background:#fff7ed;border-radius:10px;padding:8px;text-align:center;">
                    <div style="font-size:9px;color:#ea580c;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Suhu</div>
                    <div style="font-size:16px;font-weight:800;color:#0f172a;margin-top:2px;">${s.suhu ?? "-"}</div>
                    <div style="font-size:9px;color:#94a3b8;">°C</div>
                </div>
                <div style="background:#ecfeff;border-radius:10px;padding:8px;text-align:center;">
                    <div style="font-size:9px;color:#0891b2;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Kelembapan</div>
                    <div style="font-size:16px;font-weight:800;color:#0f172a;margin-top:2px;">${s.kelembapan ?? "-"}</div>
                    <div style="font-size:9px;color:#94a3b8;">%</div>
                </div>
                <div style="background:#f0fdf4;border-radius:10px;padding:8px;text-align:center;">
                    <div style="font-size:9px;color:#16a34a;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Tekanan</div>
                    <div style="font-size:16px;font-weight:800;color:#0f172a;margin-top:2px;">${s.tekanan_udara ?? "-"}</div>
                    <div style="font-size:9px;color:#94a3b8;">hPa</div>
                </div>
                <div style="background:#fffbeb;border-radius:10px;padding:8px;text-align:center;">
                    <div style="font-size:9px;color:#d97706;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Angin</div>
                    <div style="font-size:16px;font-weight:800;color:#0f172a;margin-top:2px;">${s.kecepatan_angin ?? "-"}</div>
                    <div style="font-size:9px;color:#94a3b8;">m/s</div>
                </div>
                <div style="background:#faf5ff;border-radius:10px;padding:8px;text-align:center;">
                    <div style="font-size:9px;color:#9333ea;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Arah Angin</div>
                    <div style="font-size:16px;font-weight:800;color:#0f172a;margin-top:2px;">${s.arah_angin ?? "-"}</div>
                    <div style="font-size:9px;color:#94a3b8;">°</div>
                </div>
            </div>
            <div style="margin-top:8px;padding-top:8px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:10px;color:#94a3b8;">${s.timestamp ?? "-"}</span>
                <span style="font-size:10px;color:${isOnline ? "#22c55e" : "#ef4444"};font-weight:600;">● ${isOnline ? "Online" : "Offline"}</span>
            </div>
        `
                    : `
            <div style="text-align:center;padding:16px 0;color:#94a3b8;font-size:12px;">
                Tidak ada data sensor
            </div>
        `;

                marker.bindPopup(
                    `
            <div style="font-family:'Bricolage Grotesque',ui-sans-serif,sans-serif;min-width:240px;max-width:280px;">

                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;">
                    <div>
                        <div style="font-size:11px;color:#94a3b8;font-weight:500;">${d.name}</div>
                        <div style="font-size:15px;font-weight:700;color:#0f172a;line-height:1.2;margin-top:1px;">${d.alias ?? d.name}</div>
                    </div>
                    <span style="
                        background:${risikoBg};
                        color:${risikoColor};
                        font-size:10px;
                        font-weight:700;
                        padding:3px 10px;
                        border-radius:999px;
                        border:1px solid ${risikoColor}30;
                        white-space:nowrap;
                        margin-left:8px;
                        flex-shrink:0;
                    ">${risiko}</span>
                </div>

                <div style="margin-bottom:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:10px;color:#94a3b8;">Skor Risiko Fuzzy</span>
                        <span style="font-size:10px;font-weight:700;color:${risikoColor};">${d.score ?? 0}</span>
                    </div>
                    <div style="background:#f1f5f9;border-radius:999px;height:5px;overflow:hidden;">
                        <div style="background:${risikoColor};height:100%;width:${Math.min(d.score ?? 0, 100)}%;border-radius:999px;"></div>
                    </div>
                </div>

                ${sensorHtml}
            </div>
        `,
                    { maxWidth: 300, className: "rob-popup" },
                );
            });

            if (bounds.length > 0) {
                const latLngBounds = LLeaflet.latLngBounds(bounds);
                this.map.fitBounds(latLngBounds, { padding: [50, 50] });
            }
        },
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
                const label = (
                    item.label ||
                    item.alias ||
                    item.name ||
                    ""
                ).toLowerCase();
                const status = (item.statusLabel || "").toLowerCase();
                return label.includes(q) || status.includes(q);
            });
        },

        get selectedOption() {
            return (
                this.options.find(
                    (item) => String(item.id) === String(this.selected),
                ) || null
            );
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

document.addEventListener("alpine:init", () => {
    Alpine.data("searchSelect", (config = {}) => ({
        isOpen: false,
        query: "",
        value: config.value ?? null,
        placeholder: config.placeholder ?? "Pilih...",
        searchPlaceholder: config.searchPlaceholder ?? "Cari...",
        items: config.options ?? [],
        getOptions: config.getOptions ?? null,

        init() {
            this.$watch("isOpen", (open) => {
                if (open) {
                    this.$nextTick(() => {
                        this.$refs.search?.focus();
                    });
                }
            });
        },

        options() {
            if (typeof this.getOptions === "function") {
                const result = this.getOptions();
                return Array.isArray(result) ? result : [];
            }

            return Array.isArray(this.items) ? this.items : [];
        },

        filteredOptions() {
            const q = this.query.toLowerCase().trim();
            const opts = this.options();

            if (!q) return opts;

            return opts.filter(
                (opt) =>
                    String(opt.label ?? "")
                        .toLowerCase()
                        .includes(q) ||
                    String(opt.name ?? "")
                        .toLowerCase()
                        .includes(q) ||
                    String(opt.alias ?? "")
                        .toLowerCase()
                        .includes(q) ||
                    String(opt.statusLabel ?? "")
                        .toLowerCase()
                        .includes(q),
            );
        },

        selectedOption() {
            return (
                this.options().find(
                    (opt) => String(opt.value) === String(this.value),
                ) || null
            );
        },

        selectedLabel() {
            return this.selectedOption()?.label ?? this.placeholder;
        },

        toggle() {
            this.isOpen = !this.isOpen;
        },

        close() {
            this.isOpen = false;
            this.query = "";
        },

        select(val) {
            this.value = val;
            this.isOpen = false;
            this.query = "";
        },
    }));
});

// =========================
// Analisis Chart
// =========================
document.addEventListener("alpine:init", () => {
    Alpine.data("analisisChart", () => ({
        charts: {},

        init() {
            this.$nextTick(() => this.renderCharts());

            window.addEventListener("livewire:navigated", () => {
                this.$nextTick(() => this.renderCharts());
            });

            document.addEventListener("livewire:update", () => {
                this.$nextTick(() => this.renderCharts());
            });
        },

        destroyAll() {
            Object.values(this.charts).forEach((c) => {
                try { c.destroy(); } catch (_) {}
            });
            this.charts = {};
        },

        renderCharts() {
            this.destroyAll();

            const sensorEl = document.getElementById("analisisSensorData");
            const bmkgEl   = document.getElementById("analisisBmkgData");

            if (!sensorEl || !bmkgEl) return;

            const sensorData = JSON.parse(sensorEl.textContent || "[]");
            const bmkgData   = JSON.parse(bmkgEl.textContent   || "[]");

            const isDark    = document.documentElement.classList.contains("dark");
            const gridColor = isDark ? "rgba(255,255,255,0.05)" : "rgba(0,0,0,0.05)";
            const tickColor = isDark ? "#71717a" : "#a1a1aa";

            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        labels: { color: tickColor, font: { size: 11 }, boxWidth: 12 },
                    },
                },
                scales: {
                    x: {
                        ticks: { color: tickColor, maxTicksLimit: 6, font: { size: 10 } },
                        grid:  { color: gridColor },
                    },
                    y: {
                        ticks: { color: tickColor, font: { size: 10 } },
                        grid:  { color: gridColor },
                    },
                },
            };

            const sensorLabels = sensorData.map((d) => d.local_datetime.substring(11, 16));
            const bmkgLabels   = bmkgData.map((d) => d.local_datetime.substring(11, 16));
            const labels       = sensorLabels.length ? sensorLabels : bmkgLabels;

            const makeDataset = (label, data, color, dashed = false) => ({
                label,
                data,
                borderColor: color,
                backgroundColor: color + "22",
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                borderDash: dashed ? [5, 5] : [],
                pointRadius: 3,
                pointHoverRadius: 5,
            });

            [
                { id: "chartSuhu",       key: "suhu",            color: "#fb923c" },
                { id: "chartKelembapan", key: "kelembapan",      color: "#22d3ee" },
                { id: "chartAngin",      key: "kecepatan_angin", color: "#fbbf24" },
            ].forEach(({ id, key, color }) => {
                const canvas = document.getElementById(id);
                if (!canvas || !window.Chart) return;

                this.charts[id] = new Chart(canvas.getContext("2d"), {
                    type: "line",
                    data: {
                        labels,
                        datasets: [
                            makeDataset("Sensor", sensorData.map((d) => d[key]), color),
                            makeDataset("BMKG",   bmkgData.map((d) => d[key]),   "#94a3b8", true),
                        ],
                    },
                    options: baseOptions,
                });
            });
        },
    }));
});

// =========================
// Leaflet default icon fix
// =========================
try {
    delete leafletFromNpm.Icon.Default.prototype._getIconUrl;
    leafletFromNpm.Icon.Default.mergeOptions({
        iconRetinaUrl: new URL(
            "leaflet/dist/images/marker-icon-2x.png",
            import.meta.url,
        ).href,
        iconUrl: new URL("leaflet/dist/images/marker-icon.png", import.meta.url)
            .href,
        shadowUrl: new URL(
            "leaflet/dist/images/marker-shadow.png",
            import.meta.url,
        ).href,
    });
} catch (e) {}
