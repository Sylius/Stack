import ApexCharts from 'apexcharts';

let chart = null;
function renderChart() {
  // eslint-disable-next-line no-undef
  const statisticsChart = document.querySelector('#statistics-chart');

  if (!statisticsChart) {
    return;
  }

  const styles = getComputedStyle(document.documentElement);
  const labelColor = styles.getPropertyValue('--tblr-body-color').trim();
  const primaryColor = styles.getPropertyValue('--tblr-primary').trim();

  const options = {
    colors: [primaryColor],
    fill: {
      colors: [primaryColor]
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
      offsetY: -20,
      style: {
        fontSize: '12px',
        colors: [labelColor]
      }
    },
    xaxis: {
      categories: JSON.parse(statisticsChart.dataset.intervals),
      position: 'top',
      labels: {
        style: {
          colors: labelColor
        }
      },
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
            colorFrom: primaryColor,
            colorTo: primaryColor,
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
      }

    },
    title: {
      floating: true,
      offsetY: 330,
      align: 'center',
      style: {
        color: labelColor
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

  const themeObserver = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.attributeName === 'data-bs-theme') {
        chart.destroy();
        renderChart();
      }
    });
  });

  themeObserver.observe(document.documentElement, {
    attributes: true
  });
}
