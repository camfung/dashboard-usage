/**
 * User Activity Dashboard - Chart.js Integration
 */

(function() {
    'use strict';

    // Color palette (can be dynamically changed)
    let colors = {
        babyPowder: '#F8F9FA',
        bleuDeFrance: '#3083DC',
        jet: '#2D2D2A',
        selectiveYellow: '#2DD4BF',
        steel: '#6B7280',
        poppy: '#DF2935'
    };

    // Store reference to chart instance
    let chartInstance = null;

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

        // Destroy existing chart if it exists
        if (chartInstance) {
            chartInstance.destroy();
        }

        // Create chart
        chartInstance = new Chart(ctx, {
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

    /**
     * Color Randomizer - For testing different color schemes
     */
    function initColorRandomizer() {
        // Create color tester container
        const testerContainer = document.createElement('div');
        testerContainer.id = 'uad-color-tester';
        testerContainer.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border: 2px solid #3083DC;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        `;

        testerContainer.innerHTML = `
            <div style="margin-bottom: 12px; font-weight: 600; color: #2D2D2A;">Color Tester</div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button id="uad-randomize-colors" style="
                    padding: 10px 16px;
                    background: #3083DC;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 14px;
                ">🎲 Randomize Colors</button>
                <button id="uad-export-colors" style="
                    padding: 10px 16px;
                    background: #28a745;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 14px;
                ">💾 Export Colors</button>
                <button id="uad-close-tester" style="
                    padding: 6px 12px;
                    background: #dc3545;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-weight: 500;
                    font-size: 12px;
                ">Close</button>
            </div>
            <div id="uad-current-colors" style="
                margin-top: 12px;
                padding: 8px;
                background: #f8f9fa;
                border-radius: 4px;
                font-size: 11px;
                font-family: monospace;
                max-height: 150px;
                overflow-y: auto;
            "></div>
        `;

        document.body.appendChild(testerContainer);

        // Display current colors
        updateColorDisplay();

        // Randomize button
        document.getElementById('uad-randomize-colors').addEventListener('click', randomizeColors);

        // Export button
        document.getElementById('uad-export-colors').addEventListener('click', exportColors);

        // Close button
        document.getElementById('uad-close-tester').addEventListener('click', function() {
            testerContainer.remove();
        });
    }

    /**
     * Generate random hex color
     */
    function randomColor() {
        const hue = Math.floor(Math.random() * 360);
        const saturation = 60 + Math.floor(Math.random() * 30); // 60-90%
        const lightness = 50 + Math.floor(Math.random() * 20); // 50-70%

        return hslToHex(hue, saturation, lightness);
    }

    /**
     * Generate light background color
     */
    function randomLightColor() {
        const hue = Math.floor(Math.random() * 360);
        const saturation = 10 + Math.floor(Math.random() * 20); // 10-30%
        const lightness = 92 + Math.floor(Math.random() * 6); // 92-98%

        return hslToHex(hue, saturation, lightness);
    }

    /**
     * Convert HSL to Hex
     */
    function hslToHex(h, s, l) {
        s /= 100;
        l /= 100;
        const c = (1 - Math.abs(2 * l - 1)) * s;
        const x = c * (1 - Math.abs((h / 60) % 2 - 1));
        const m = l - c / 2;
        let r = 0, g = 0, b = 0;

        if (0 <= h && h < 60) {
            r = c; g = x; b = 0;
        } else if (60 <= h && h < 120) {
            r = x; g = c; b = 0;
        } else if (120 <= h && h < 180) {
            r = 0; g = c; b = x;
        } else if (180 <= h && h < 240) {
            r = 0; g = x; b = c;
        } else if (240 <= h && h < 300) {
            r = x; g = 0; b = c;
        } else if (300 <= h && h < 360) {
            r = c; g = 0; b = x;
        }

        const toHex = (val) => {
            const hex = Math.round((val + m) * 255).toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        };

        return '#' + toHex(r) + toHex(g) + toHex(b);
    }

    /**
     * Randomize colors
     */
    function randomizeColors() {
        // Randomize the colors we changed
        colors.babyPowder = randomLightColor();
        colors.selectiveYellow = randomColor();

        // Update CSS variables
        document.documentElement.style.setProperty('--baby-powder', colors.babyPowder);
        document.documentElement.style.setProperty('--selective-yellow', colors.selectiveYellow);

        // Update table header gradient
        const tableHeader = document.querySelector('.uad-table thead');
        if (tableHeader) {
            const color1 = randomColor();
            const color2 = randomColor();
            tableHeader.style.background = `linear-gradient(135deg, ${color1}, ${color2})`;
        }

        // Reinitialize chart with new colors
        initChart();

        // Update color display
        updateColorDisplay();
    }

    /**
     * Update color display
     */
    function updateColorDisplay() {
        const display = document.getElementById('uad-current-colors');
        if (display) {
            const tableHeader = document.querySelector('.uad-table thead');
            const gradient = tableHeader ? window.getComputedStyle(tableHeader).background : 'N/A';

            display.innerHTML = `
                <div><strong>Background:</strong><br>${colors.babyPowder}</div>
                <div style="margin-top: 4px;"><strong>Accent:</strong><br>${colors.selectiveYellow}</div>
                <div style="margin-top: 4px;"><strong>Table Header:</strong><br>${gradient.includes('gradient') ? gradient.substring(0, 50) + '...' : gradient}</div>
            `;
        }
    }

    /**
     * Export colors to a downloadable file
     */
    function exportColors() {
        const tableHeader = document.querySelector('.uad-table thead');
        const gradient = tableHeader ? window.getComputedStyle(tableHeader).background : '';

        // Extract gradient colors if possible
        const gradientMatch = gradient.match(/rgb\([^)]+\)/g);
        const gradientColors = gradientMatch ? gradientMatch.map(rgb => rgbToHex(rgb)) : [];

        const colorData = {
            timestamp: new Date().toISOString(),
            colors: {
                background: colors.babyPowder,
                accent: colors.selectiveYellow,
                blue: colors.bleuDeFrance,
                jet: colors.jet,
                poppy: colors.poppy
            },
            tableHeaderGradient: gradientColors.length >= 2 ? {
                color1: gradientColors[0],
                color2: gradientColors[1]
            } : null,
            css: {
                '--baby-powder': colors.babyPowder,
                '--bleu-de-france': colors.bleuDeFrance,
                '--jet': colors.jet,
                '--selective-yellow': colors.selectiveYellow,
                '--poppy': colors.poppy
            }
        };

        // Create blob and download
        const blob = new Blob([JSON.stringify(colorData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `uad-colors-${Date.now()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        alert('Colors exported successfully!');
    }

    /**
     * Convert RGB to Hex
     */
    function rgbToHex(rgb) {
        const values = rgb.match(/\d+/g);
        if (!values || values.length < 3) return '#000000';

        const r = parseInt(values[0]);
        const g = parseInt(values[1]);
        const b = parseInt(values[2]);

        return '#' + [r, g, b].map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    }

    // Initialize when DOM is ready
    function init() {
        initChart();
        initDatePicker();
        initPagination();
        initColorRandomizer();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
