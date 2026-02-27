import { Chart } from "chart.js/auto";
import L from "leaflet";

window.Chart = Chart;
window.L = L;

// =======================================================
// Global state (HMR-safe)
// =======================================================
window.__robMainChart = window.__robMainChart || null;
window.__robChartPending = window.__robChartPending || null;
window.__robChartListenersAdded = window.__robChartListenersAdded || false;

window.__robMetricChart = window.__robMetricChart || null;
window.__robMetricPending = window.__robMetricPending || null;
window.__robMetricListenersAdded = window.__robMetricListenersAdded || false;

// =======================================================
// Helpers
// =======================================================
function normalizePayload(payload) {
  if (!payload) return {};
  if (payload.labels) return payload;
  if (payload[0]?.labels) return payload[0];
  if (payload?.detail?.labels) return payload.detail;
  return payload;
}

function metricLabel(metric) {
  const map = {
    ketinggian_air: "Water Level",
    suhu: "Temperature",
    kelembapan: "Humidity",
    tekanan_udara: "Air Pressure",
    kecepatan_angin: "Wind Speed",
    arah_angin: "Wind Direction",
  };
  return map[metric] || "Trend";
}

// =======================================================
// MAIN CHART
// =======================================================
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

  if (!chart.data.datasets || chart.data.datasets.length === 0) {
    chart.data.datasets = [{ label: title, data: [], tension: 0.4, fill: true }];
  }

  chart.data.datasets[0].label = title;
  chart.data.datasets[0].data = values;
  chart.update();
}

function tryFlushMainPending() {
  if (window.__robChartPending && window.__robMainChart) {
    applyChartPayload(window.__robChartPending);
    window.__robChartPending = null;
  }
}

if (!window.__robChartListenersAdded) {
  window.__robChartListenersAdded = true;

  window.addEventListener("refreshChart", (event) => {
    applyChartPayload(event.detail || {});
  });

  document.addEventListener("livewire:load", tryFlushMainPending);
  document.addEventListener("livewire:navigated", tryFlushMainPending);
  document.addEventListener("livewire:updated", tryFlushMainPending);
  document.addEventListener("livewire:message.processed", tryFlushMainPending);
}

// =======================================================
// MODAL CHART
// =======================================================
function renderMetricChart(payload) {
  const p = normalizePayload(payload);
  const canvas = document.getElementById("metricChart");

  if (!canvas) {
    window.__robMetricPending = p;
    return;
  }

  const labels = p.labels ?? [];
  const values = p.values ?? [];
  const title = p.title ?? "Trend";

  if (window.__robMetricChart && window.__robMetricChart.canvas !== canvas) {
    window.__robMetricChart.destroy();
    window.__robMetricChart = null;
  }

  if (!window.__robMetricChart) {
    window.__robMetricChart = new Chart(canvas.getContext("2d"), {
      type: "line",
      data: {
        labels,
        datasets: [{ label: title, data: values, tension: 0.4, fill: true }],
      },
      options: { responsive: true, maintainAspectRatio: false, animation: false },
    });
  } else {
    window.__robMetricChart.data.labels = labels;
    window.__robMetricChart.data.datasets[0].label = title;
    window.__robMetricChart.data.datasets[0].data = values;
    window.__robMetricChart.update();
  }
}

function tryFlushMetricPending() {
  if (window.__robMetricPending && document.getElementById("metricChart")) {
    renderMetricChart(window.__robMetricPending);
    window.__robMetricPending = null;
  }
}

if (!window.__robMetricListenersAdded) {
  window.__robMetricListenersAdded = true;

  window.addEventListener("modalChart", (event) => {
    renderMetricChart(event.detail || {});
  });

  document.addEventListener("livewire:load", tryFlushMetricPending);
  document.addEventListener("livewire:navigated", tryFlushMetricPending);
  document.addEventListener("livewire:updated", tryFlushMetricPending);
  document.addEventListener("livewire:message.processed", tryFlushMetricPending);
}

// =======================================================
// Alpine component: Dashboard
// =======================================================
document.addEventListener("alpine:init", () => {
  Alpine.data("dashboard", () => ({
    data: {},
    risk: "AMAN",
    riskScore: 1,
    riskStyles: {},

    theme:
      localStorage.getItem("theme") ||
      (document.documentElement.classList.contains("dark") ? "dark" : "light"),

    init() {
      this.applyTheme(this.theme);

      this.$nextTick(() => {
        const canvas = this.$refs.waterChart;
        if (!canvas) {
          console.warn('canvas waterChart tidak ditemukan. Pastikan pakai x-ref="waterChart"');
          return;
        }

        if (!window.__robMainChart) {
          window.__robMainChart = new Chart(canvas.getContext("2d"), {
            type: "line",
            data: {
              labels: [],
              datasets: [{ label: "Water Level", data: [], tension: 0.4, fill: true }],
            },
            options: { responsive: true, maintainAspectRatio: false, animation: false },
          });
        }

        tryFlushMainPending();
        tryFlushMetricPending();
      });

      window.addEventListener("dashboard-updated", (event) => {
        const payload = event.detail || {};
        this.data = payload.data || {};
        this.risk = payload.risk || "AMAN";
        this.riskScore = payload.riskScore ?? 1;
        this.riskStyles = payload.riskStyles || {};
      });

      window.addEventListener("theme-sync", (event) => {
        const t = event.detail?.theme;
        if (t === "dark" || t === "light") this.applyTheme(t);
      });
    },

    applyTheme(theme) {
      this.theme = theme === "dark" ? "dark" : "light";
      document.documentElement.classList.toggle("dark", this.theme === "dark");
      localStorage.setItem("theme", this.theme);
    },

    toggleTheme() {
      const next = this.theme === "dark" ? "light" : "dark";
      this.applyTheme(next);

      // sync ke Livewire
      if (this.$wire) {
        this.$wire.set("theme", next);
      } else if (window.Livewire) {
        const root = document.querySelector('[wire\\:key="dashboard-root"]');
        if (root) {
          const id = root.getAttribute("wire:id");
          const comp = window.Livewire.find(id);
          if (comp) comp.set("theme", next);
        }
      }
    },
  }));
});

// =======================================================
// Alpine component: Searchable Select (REUSABLE)
// =======================================================
document.addEventListener("alpine:init", () => {
  Alpine.data("searchSelect", (cfg) => ({
    open: false,
    query: "",
    highlighted: 0,

    options: cfg?.options || [],
    value: cfg?.value, // bisa entangle livewire
    placeholder: cfg?.placeholder || "Pilih...",
    searchPlaceholder: cfg?.searchPlaceholder || "Cari...",
    disabled: !!cfg?.disabled,

    toggle() {
      if (this.disabled) return;
      this.open ? this.close() : this.openAndFocus();
    },

    openAndFocus() {
      if (this.disabled) return;
      this.open = true;
      this.query = "";
      this.highlighted = 0;
      this.$nextTick(() => this.$refs.search?.focus());
    },

    close() {
      this.open = false;
      this.query = "";
      this.highlighted = 0;
    },

    filteredOptions() {
      const q = (this.query || "").toLowerCase().trim();
      if (!q) return this.options;
      return this.options.filter((o) =>
        String(o.label || "").toLowerCase().includes(q)
      );
    },

    selectedLabel() {
      const found = this.options.find(
        (o) => String(o.value) === String(this.value)
      );
      return found?.label ?? this.placeholder;
    },

    select(val) {
      this.value = val; // <- kalau di-entangle, auto update Livewire
      this.close();
    },

    highlightNext() {
      const len = this.filteredOptions().length;
      if (!len) return;
      this.highlighted = Math.min(this.highlighted + 1, len - 1);
      this.scrollToHighlighted();
    },

    highlightPrev() {
      const len = this.filteredOptions().length;
      if (!len) return;
      this.highlighted = Math.max(this.highlighted - 1, 0);
      this.scrollToHighlighted();
    },

    chooseHighlighted() {
      const list = this.filteredOptions();
      const opt = list[this.highlighted];
      if (opt) this.select(opt.value);
    },

    scrollToHighlighted() {
      this.$nextTick(() => {
        const items = this.$refs.list?.querySelectorAll("[data-opt]");
        const el = items?.[this.highlighted];
        el?.scrollIntoView({ block: "nearest" });
      });
    },

    setOptions(newOptions) {
      this.options = Array.isArray(newOptions) ? newOptions : [];
    },
  }));
});

// =======================================================
// Leaflet marker fix (Vite)
// =======================================================
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
  iconRetinaUrl: new URL("leaflet/dist/images/marker-icon-2x.png", import.meta.url).href,
  iconUrl: new URL("leaflet/dist/images/marker-icon.png", import.meta.url).href,
  shadowUrl: new URL("leaflet/dist/images/marker-shadow.png", import.meta.url).href,
});
