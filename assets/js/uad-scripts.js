/**
 * User Activity Dashboard - Chart.js Integration
 */

(function() {
    'use strict';

    // Color palette
    const colors = {
        babyPowder: '#FFFFFA',
        bleuDeFrance: '#3083DC',
        jet: '#2D2D2A',
        selectiveYellow: '#FFB30F',
        poppy: '#DF2935'
    };

    /**
     * Initialize chart when DOM is ready
     */
    function initChart() {
        const canvas = document.getElementById('uad-activity-chart');

        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        // Check if chart data is available
        if (typeof window.uadChartData === 'undefined') {
            console.error('Chart data not found');
            return;
        }

        const chartData = window.uadChartData;

        // Create gradient for hits area
        const ctx = canvas.getContext('2d');
        const hitsGradient = ctx.createLinearGradient(0, 0, 0, 400);
        hitsGradient.addColorStop(0, colors.selectiveYellow + 'CC'); // 80% opacity
        hitsGradient.addColorStop(1, colors.selectiveYellow + '33'); // 20% opacity

        const balanceGradient = ctx.createLinearGradient(0, 0, 0, 400);
        balanceGradient.addColorStop(0, colors.bleuDeFrance + 'CC');
        balanceGradient.addColorStop(1, colors.bleuDeFrance + '33');

        // Create chart
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Total Hits',
                        data: chartData.hits,
                        borderColor: colors.selectiveYellow,
                        backgroundColor: hitsGradient,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y-hits',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.selectiveYellow,
                        pointBorderColor: colors.babyPowder,
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Balance',
                        data: chartData.balance,
                        borderColor: colors.bleuDeFrance,
                        backgroundColor: balanceGradient,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y-balance',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: colors.bleuDeFrance,
                        pointBorderColor: colors.babyPowder,
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: colors.jet,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: colors.jet,
                        titleColor: colors.babyPowder,
                        bodyColor: colors.babyPowder,
                        borderColor: colors.bleuDeFrance,
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 0) {
                                    // Hits - no dollar sign
                                    label += context.parsed.y.toLocaleString();
                                } else {
                                    // Balance - with dollar sign
                                    label += '$' + context.parsed.y.toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: colors.jet + '15',
                            drawBorder: false
                        },
                        ticks: {
                            color: colors.jet,
                            font: {
                                size: 11
                            },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    'y-hits': {
                        type: 'linear',
                        position: 'left',
                        grid: {
                            color: colors.jet + '15',
                            drawBorder: false
                        },
                        ticks: {
                            color: colors.selectiveYellow,
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        },
                        title: {
                            display: true,
                            text: 'Hits',
                            color: colors.selectiveYellow,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    'y-balance': {
                        type: 'linear',
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: colors.bleuDeFrance,
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        },
                        title: {
                            display: true,
                            text: 'Balance',
                            color: colors.bleuDeFrance,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Initialize date picker functionality
     */
    function initDatePicker() {
        const dashboard = document.querySelector('.uad-dashboard');
        if (!dashboard) return;

        const startDateInput = document.getElementById('uad-start-date');
        const endDateInput = document.getElementById('uad-end-date');
        const updateButton = document.getElementById('uad-update-dates');
        const resetButton = document.getElementById('uad-reset-dates');

        if (!startDateInput || !endDateInput || !updateButton) return;

        // Store original dates for reset
        const originalStartDate = dashboard.dataset.startDate;
        const originalEndDate = dashboard.dataset.endDate;

        // Update button click
        updateButton.addEventListener('click', function() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;

            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            if (startDate > endDate) {
                alert('Start date must be before end date');
                return;
            }

            // Show loading state
            updateButton.disabled = true;
            updateButton.textContent = 'Loading...';
            dashboard.classList.add('uad-loading');

            // Reload page with new date parameters
            const url = new URL(window.location.href);
            url.searchParams.set('uad_start_date', startDate);
            url.searchParams.set('uad_end_date', endDate);
            window.location.href = url.toString();
        });

        // Reset button click
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                // Remove date parameters from URL
                const url = new URL(window.location.href);
                url.searchParams.delete('uad_start_date');
                url.searchParams.delete('uad_end_date');
                window.location.href = url.toString();
            });
        }

        // Set max date for both inputs (today)
        const today = new Date().toISOString().split('T')[0];
        startDateInput.max = today;
        endDateInput.max = today;

        // Update end date min when start date changes
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
        });

        // Update start date max when end date changes
        endDateInput.addEventListener('change', function() {
            startDateInput.max = this.value;
        });
    }

    // Initialize when DOM is ready
    function init() {
        initChart();
        initDatePicker();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
