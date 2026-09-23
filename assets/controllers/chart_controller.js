import { Controller } from '@hotwired/stimulus';
import ApexCharts from 'apexcharts';

export default class extends Controller {
    static values = {
        intervals: Array,
        talks: Array
    }

    chart = null;
    isInitialized = false;

    connect() {
        this.renderChart();
        this.observeThemeChanges();
        this.isInitialized = true;
    }

    disconnect() {
        if (this.chart) {
            this.chart.destroy();
        }
        if (this.themeObserver) {
            this.themeObserver.disconnect();
        }
    }

    intervalsValueChanged() {
        if (this.isInitialized) {
            this.updateChart();
        }
    }

    talksValueChanged() {
        if (this.isInitialized) {
            this.updateChart();
        }
    }

    updateChart() {
        if (this.chart) {
            this.chart.destroy();
        }
        this.renderChart();
    }

    renderChart() {
        const styles = getComputedStyle(document.documentElement);
        const labelColor = styles.getPropertyValue('--tblr-body-color').trim();
        const primaryColor = styles.getPropertyValue('--tblr-primary').trim();
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        const options = {
            colors: [primaryColor],
            fill: {
                colors: [primaryColor]
            },
            series: [{
                name: 'talks',
                data: this.talksValue
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
                        position: 'top'
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
                categories: this.intervalsValue,
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
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light'
            }
        };

        this.chart = new ApexCharts(this.element, options);
        this.chart.render();
    }

    observeThemeChanges() {
        this.themeObserver = new MutationObserver(() => {
            this.updateChart();
        });

        this.themeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-bs-theme']
        });
    }
}
