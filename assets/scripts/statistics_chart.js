import ApexCharts from 'apexcharts';

let chart = null;
function renderChart() {
  // eslint-disable-next-line no-undef
  const statisticsChart = document.querySelector('#statistics-chart');

  if (!statisticsChart) {
    return;
  }

  const options = {
    colors: ['#32be9f'],
    fill: {
      colors: ['#32be9f']
    },
    series: [{
      name: 'talks',
      data: JSON.parse(statisticsChart.dataset.talks)
    }],
    chart: {
      toolbar: {
        show: false
      },
      height: 350,
      type: 'bar'
    },
    plotOptions: {
      bar: {
        borderRadius: 4,
        dataLabels: {
          position: 'top' // top, center, bottom
        }
      }
    },
    dataLabels: {
      enabled: true,
      formatter(val) {
        return `${val}`;
      },
      offsetY: -20,
      style: {
        fontSize: '12px',
        colors: ['#304758']
      }
    },
    xaxis: {
      categories: JSON.parse(statisticsChart.dataset.intervals),
      position: 'top',
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      crosshairs: {
        fill: {
          type: 'gradient',
          gradient: {
            colorFrom: '#32be9f',
            colorTo: '#2a9f83',
            stops: [0, 100],
            opacityFrom: 0.4,
            opacityTo: 0.5
          }
        }
      },
      tooltip: {
        enabled: true
      }
    },
    yaxis: {
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      labels: {
        show: false,
        formatter(val) {
          return `${val}`;
        }
      }

    },
    title: {
      floating: true,
      offsetY: 330,
      align: 'center',
      style: {
        color: '#444'
      }
    }
  };

  chart = new ApexCharts(statisticsChart, options);
  chart.render();
}

renderChart();

const element = document.querySelector('#statistics-chart');

if (element) {
  const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === 'data-talks' || mutation.attributeName === 'data-intervals') {
        chart.destroy();
        renderChart();
      }
    });
  });

  observer.observe(element, {
    attributes: true
  });
}
