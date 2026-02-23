// resources/js/app.js

import './bootstrap'
import { Chart } from 'chart.js/auto'
import L from 'leaflet'

window.Chart = Chart
window.L = L

// =======================================================
// Global chart state (anti race-condition + HMR safe)
// =======================================================
window.__robMainChart = window.__robMainChart || null
window.__robMainCanvas = window.__robMainCanvas || null
window.__robChartPending = window.__robChartPending || null
window.__robChartListenersAdded = window.__robChartListenersAdded || false

window.__robMetricChart = window.__robMetricChart || null
window.__robMetricCanvas = window.__robMetricCanvas || null
window.__robMetricPending = window.__robMetricPending || null
window.__robMetricListenersAdded = window.__robMetricListenersAdded || false

// =======================================================
// Helpers
// =======================================================
function normalizePayload(payload) {
  if (payload?.labels) return payload
  if (payload?.[0]?.labels) return payload[0]
  return payload || {}
}

function onLW(eventName, handler) {
  // ✅ listen di window + document biar aman di berbagai setup Livewire
  window.addEventListener(eventName, handler)
  document.addEventListener(eventName, handler)
}

// =======================================================
// MAIN CHART (Water Level Trend)
// =======================================================
function ensureMainChart() {
  const canvas = document.querySelector('canvas[x-ref="waterChart"]') || null
  if (!canvas) return null

  // kalau canvas berubah (Livewire rerender), destroy chart lama
  if (window.__robMainChart && window.__robMainCanvas && window.__robMainCanvas !== canvas) {
    window.__robMainChart.destroy()
    window.__robMainChart = null
    window.__robMainCanvas = null
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
    window.__robMainCanvas = canvas
  }

  return window.__robMainChart
}

function applyChartPayload(payload) {
  const p = normalizePayload(payload)

  const chart = ensureMainChart()
  if (!chart) {
    window.__robChartPending = p
    return
  }

  const labels = p?.labels ?? []
  const values = p?.values ?? []

  chart.data.labels = labels

  if (!chart.data.datasets || chart.data.datasets.length === 0) {
    chart.data.datasets = [{ label: 'Water Level', data: [], tension: 0.4, fill: true }]
  }

  chart.data.datasets[0].data = values
  chart.update()
}

function tryFlushMainPending() {
  ensureMainChart()
  if (window.__robChartPending && window.__robMainChart) {
    applyChartPayload(window.__robChartPending)
    window.__robChartPending = null
  }
}

// =======================================================
// MODAL CHART (Metric chart)
// =======================================================
function ensureMetricChart() {
  const canvas = document.getElementById('metricChart')
  if (!canvas) return null

  if (window.__robMetricChart && window.__robMetricCanvas && window.__robMetricCanvas !== canvas) {
    window.__robMetricChart.destroy()
    window.__robMetricChart = null
    window.__robMetricCanvas = null
  }

  if (!window.__robMetricChart) {
    window.__robMetricChart = new Chart(canvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: [],
        datasets: [
          { label: 'Trend', data: [], tension: 0.4, fill: true },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
      },
    })
    window.__robMetricCanvas = canvas
  }

  return window.__robMetricChart
}

function renderMetricChart(payload) {
  const p = normalizePayload(payload)

  const chart = ensureMetricChart()
  if (!chart) {
    window.__robMetricPending = p
    return
  }

  const labels = p?.labels ?? []
  const values = p?.values ?? []
  const title = p?.title ?? 'Trend'

  chart.data.labels = labels
  chart.data.datasets[0].label = title
  chart.data.datasets[0].data = values
  chart.update()
}

function tryFlushMetricPending() {
  ensureMetricChart()
  if (window.__robMetricPending && window.__robMetricChart) {
    renderMetricChart(window.__robMetricPending)
    window.__robMetricPending = null
  }
}

// =======================================================
// Attach global listeners once
// =======================================================
if (!window.__robChartListenersAdded) {
  window.__robChartListenersAdded = true

  onLW('refreshChart', (event) => {
    console.log('[refreshChart]', event.detail) // ✅ debug
    applyChartPayload(event.detail || {})
  })

  document.addEventListener('livewire:load', tryFlushMainPending)
  document.addEventListener('livewire:navigated', tryFlushMainPending)
  document.addEventListener('livewire:updated', tryFlushMainPending)
  document.addEventListener('livewire:message.processed', tryFlushMainPending)
}

if (!window.__robMetricListenersAdded) {
  window.__robMetricListenersAdded = true

  onLW('modalChart', (event) => {
    console.log('[modalChart]', event.detail) // ✅ debug
    renderMetricChart(event.detail || {})
  })

  document.addEventListener('livewire:load', tryFlushMetricPending)
  document.addEventListener('livewire:navigated', tryFlushMetricPending)
  document.addEventListener('livewire:updated', tryFlushMetricPending)
  document.addEventListener('livewire:message.processed', tryFlushMetricPending)
}

// =======================================================
// Alpine component (state only; chart handled globally)
// =======================================================
document.addEventListener('alpine:init', () => {
  Alpine.data('dashboard', () => ({
    data: {},
    risk: 'AMAN',
    riskScore: 1,
    riskStyles: {},

    init() {
      this.$nextTick(() => {
        // ensure chart exists once DOM ready
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
// Leaflet default marker fix (Vite)
// =======================================================
delete L.Icon.Default.prototype._getIconUrl

L.Icon.Default.mergeOptions({
  iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
  iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
  shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
})
