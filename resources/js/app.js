// import './bootstrap'
import { Chart } from 'chart.js/auto'
import L from 'leaflet'

window.Chart = Chart
window.L = L

// =======================================================
// Global state (HMR-safe)
// =======================================================
window.__robMainChart = window.__robMainChart || null
window.__robChartPending = window.__robChartPending || null
window.__robChartListenersAdded = window.__robChartListenersAdded || false

window.__robMetricChart = window.__robMetricChart || null
window.__robMetricPending = window.__robMetricPending || null
window.__robMetricListenersAdded = window.__robMetricListenersAdded || false

// =======================================================
// Helpers
// =======================================================
function normalizePayload(payload) {
  if (!payload) return {}
  if (payload.labels) return payload
  if (payload[0]?.labels) return payload[0]
  if (payload?.detail?.labels) return payload.detail
  return payload
}

// =======================================================
// MAIN CHART
// =======================================================
function applyChartPayload(payload) {
  const chart = window.__robMainChart
  const p = normalizePayload(payload)

  if (!chart) {
    window.__robChartPending = p
    return
  }

  const labels = p.labels ?? []
  const values = p.values ?? []

  chart.data.labels = labels

  if (!chart.data.datasets || chart.data.datasets.length === 0) {
    chart.data.datasets = [{ label: 'Water Level', data: [], tension: 0.4, fill: true }]
  }

  chart.data.datasets[0].data = values
  chart.update()
}

function tryFlushMainPending() {
  if (window.__robChartPending && window.__robMainChart) {
    applyChartPayload(window.__robChartPending)
    window.__robChartPending = null
  }
}

if (!window.__robChartListenersAdded) {
  window.__robChartListenersAdded = true

  window.addEventListener('refreshChart', (event) => {
    applyChartPayload(event.detail || {})
  })

  // Livewire v3 lifecycle (safe)
  document.addEventListener('livewire:load', tryFlushMainPending)
  document.addEventListener('livewire:navigated', tryFlushMainPending)
  document.addEventListener('livewire:updated', tryFlushMainPending)

  // fallback beberapa versi
  document.addEventListener('livewire:message.processed', tryFlushMainPending)
}

// =======================================================
// MODAL CHART
// =======================================================
function renderMetricChart(payload) {
  const p = normalizePayload(payload)
  const canvas = document.getElementById('metricChart')

  if (!canvas) {
    window.__robMetricPending = p
    return
  }

  const labels = p.labels ?? []
  const values = p.values ?? []
  const title = p.title ?? 'Trend'

  if (window.__robMetricChart && window.__robMetricChart.canvas !== canvas) {
    window.__robMetricChart.destroy()
    window.__robMetricChart = null
  }

  if (!window.__robMetricChart) {
    window.__robMetricChart = new Chart(canvas.getContext('2d'), {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: title,
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
    })
  } else {
    window.__robMetricChart.data.labels = labels
    window.__robMetricChart.data.datasets[0].label = title
    window.__robMetricChart.data.datasets[0].data = values
    window.__robMetricChart.update()
  }
}

function tryFlushMetricPending() {
  if (window.__robMetricPending && document.getElementById('metricChart')) {
    renderMetricChart(window.__robMetricPending)
    window.__robMetricPending = null
  }
}

if (!window.__robMetricListenersAdded) {
  window.__robMetricListenersAdded = true

  window.addEventListener('modalChart', (event) => {
    renderMetricChart(event.detail || {})
  })

  document.addEventListener('livewire:load', tryFlushMetricPending)
  document.addEventListener('livewire:navigated', tryFlushMetricPending)
  document.addEventListener('livewire:updated', tryFlushMetricPending)
  document.addEventListener('livewire:message.processed', tryFlushMetricPending)
}

// =======================================================
// Alpine component
// =======================================================
document.addEventListener('alpine:init', () => {
  Alpine.data('dashboard', () => ({
    data: {},
    risk: 'AMAN',
    riskScore: 1,
    riskStyles: {},

    init() {
      this.$nextTick(() => {
        const canvas = this.$refs.waterChart
        if (!canvas) {
          console.warn('canvas waterChart tidak ditemukan. Pastikan pakai x-ref="waterChart"')
          return
        }

        if (!window.__robMainChart) {
          window.__robMainChart = new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
              labels: [],
              datasets: [
                {
                  label: 'Water Level',
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
          })
        }

        tryFlushMainPending()
        tryFlushMetricPending()
      })

      window.addEventListener('dashboard-updated', (event) => {
        const payload = event.detail || {}
        this.data = payload.data || {}
        this.risk = payload.risk || 'AMAN'
        this.riskScore = payload.riskScore ?? 1
        this.riskStyles = payload.riskStyles || {}
      })
    },
  }))
})

// =======================================================
// Leaflet marker fix (Vite)
// =======================================================
delete L.Icon.Default.prototype._getIconUrl

L.Icon.Default.mergeOptions({
  iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
  iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
  shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
})
