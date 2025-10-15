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
                                label += context.parsed.y.toLocaleString();
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

    /**
     * Initialize table pagination
     */
    function initPagination() {
        const table = document.getElementById('uad-activity-table');
        if (!table) return;

        const tbody = document.getElementById('uad-table-body');
        const rowsPerPageSelect = document.getElementById('uad-rows-per-page');
        const pageNumbers = document.getElementById('uad-page-numbers');
        const firstPageBtn = document.getElementById('uad-first-page');
        const prevPageBtn = document.getElementById('uad-prev-page');
        const nextPageBtn = document.getElementById('uad-next-page');
        const lastPageBtn = document.getElementById('uad-last-page');
        const showingStart = document.getElementById('uad-showing-start');
        const showingEnd = document.getElementById('uad-showing-end');
        const totalEntries = document.getElementById('uad-total-entries');

        if (!tbody || !rowsPerPageSelect) return;

        const allRows = Array.from(tbody.querySelectorAll('.uad-table-row'));
        const totalRows = allRows.length;

        let currentPage = 1;
        let rowsPerPage = 10;

        /**
         * Calculate total pages
         */
        function getTotalPages() {
            return Math.ceil(totalRows / rowsPerPage);
        }

        /**
         * Show rows for current page
         */
        function showPage(page) {
            const totalPages = getTotalPages();

            // Validate page number
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;

            currentPage = page;

            // Calculate row range
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            // Show/hide rows
            allRows.forEach((row, index) => {
                if (index >= start && index < end) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Update showing text
            const showStart = totalRows > 0 ? start + 1 : 0;
            const showEnd = Math.min(end, totalRows);
            showingStart.textContent = showStart;
            showingEnd.textContent = showEnd;

            // Update pagination buttons
            updatePaginationButtons();
        }

        /**
         * Update pagination button states and page numbers
         */
        function updatePaginationButtons() {
            const totalPages = getTotalPages();

            // Update button states
            firstPageBtn.disabled = currentPage === 1;
            prevPageBtn.disabled = currentPage === 1;
            nextPageBtn.disabled = currentPage === totalPages;
            lastPageBtn.disabled = currentPage === totalPages;

            // Generate page numbers
            generatePageNumbers(totalPages);
        }

        /**
         * Generate page number buttons
         */
        function generatePageNumbers(totalPages) {
            pageNumbers.innerHTML = '';

            if (totalPages <= 1) return;

            // Show max 7 page numbers
            let startPage = Math.max(1, currentPage - 3);
            let endPage = Math.min(totalPages, currentPage + 3);

            // Adjust if at beginning or end
            if (currentPage <= 4) {
                endPage = Math.min(7, totalPages);
            }
            if (currentPage >= totalPages - 3) {
                startPage = Math.max(1, totalPages - 6);
            }

            // Add first page + ellipsis if needed
            if (startPage > 1) {
                addPageButton(1);
                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'uad-page-ellipsis';
                    ellipsis.textContent = '...';
                    pageNumbers.appendChild(ellipsis);
                }
            }

            // Add page number buttons
            for (let i = startPage; i <= endPage; i++) {
                addPageButton(i);
            }

            // Add ellipsis + last page if needed
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'uad-page-ellipsis';
                    ellipsis.textContent = '...';
                    pageNumbers.appendChild(ellipsis);
                }
                addPageButton(totalPages);
            }
        }

        /**
         * Add a page number button
         */
        function addPageButton(pageNum) {
            const btn = document.createElement('button');
            btn.className = 'uad-page-num';
            btn.textContent = pageNum;

            if (pageNum === currentPage) {
                btn.classList.add('active');
            }

            btn.addEventListener('click', function() {
                showPage(pageNum);
            });

            pageNumbers.appendChild(btn);
        }

        /**
         * Event listeners
         */

        // Rows per page change
        rowsPerPageSelect.addEventListener('change', function() {
            rowsPerPage = parseInt(this.value);
            currentPage = 1; // Reset to first page
            showPage(1);
        });

        // First page
        firstPageBtn.addEventListener('click', function() {
            showPage(1);
        });

        // Previous page
        prevPageBtn.addEventListener('click', function() {
            showPage(currentPage - 1);
        });

        // Next page
        nextPageBtn.addEventListener('click', function() {
            showPage(currentPage + 1);
        });

        // Last page
        lastPageBtn.addEventListener('click', function() {
            showPage(getTotalPages());
        });

        // Initialize first page
        showPage(1);
    }

    // Initialize when DOM is ready
    function init() {
        initChart();
        initDatePicker();
        initPagination();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
