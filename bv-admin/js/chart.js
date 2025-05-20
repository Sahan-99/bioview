let currentStart = new Date();
currentStart.setDate(currentStart.getDate() - 6); // Start of current week
let currentEnd = new Date(); // End today

function formatDate(d) {
  return d.toISOString().split('T')[0];
}

function loadChart(startDate, endDate) {
  fetch(`get_usage_data.php?start=${startDate}&end=${endDate}`)
    .then(res => res.json())
    .then(data => {
      const labels = data.map(row => row.usage_date);
      const counts = data.map(row => row.usage_count);

      usageChart.data.labels = labels;
      usageChart.data.datasets[0].data = counts;
      usageChart.update();
    });
}

const ctx = document.getElementById("usageChart").getContext("2d");
const usageChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: [],
    datasets: [{
      label: 'App Usage',
      data: [],
      borderColor: 'rgba(0, 138, 30, 0.8)',
      backgroundColor: 'rgba(0, 138, 30, 0.1)',
      fill: true,
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: { beginAtZero: true },
      x: { title: { display: true, text: 'Date' } }
    }
  }
});

function updateChartRange() {
  const start = formatDate(currentStart);
  const end = formatDate(currentEnd);
  loadChart(start, end);
}

document.getElementById("prevWeek").onclick = () => {
  currentStart.setDate(currentStart.getDate() - 7);
  currentEnd.setDate(currentEnd.getDate() - 7);
  updateChartRange();
};

document.getElementById("nextWeek").onclick = () => {
  currentStart.setDate(currentStart.getDate() + 7);
  currentEnd.setDate(currentEnd.getDate() + 7);
  updateChartRange();
};

// Load current week on first load
updateChartRange();