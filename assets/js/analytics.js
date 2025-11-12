/**
 * Analytics Dashboard JavaScript
 * Handles chart rendering and data visualization
 */

// Chart instances
let notesOverTimeChart = null;
let notesByDayChart = null;
let notesByHourChart = null;
let interactionsChart = null;
let activityTimelineChart = null;

// Load analytics data
document.addEventListener('DOMContentLoaded', function() {
    // Track page view if function exists
    if (typeof trackInteraction === 'function') {
        trackInteraction('page_view', 'Analytics page viewed', window.location.href);
    }
    loadAnalyticsData();
});

function loadAnalyticsData() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) loadingIndicator.style.display = 'block';

    fetch('get_analytics_data.php')
        .then(response => response.json())
        .then(data => {
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            
            // Update statistics
            updateStatistics(data);
            
            // Render charts
            renderCharts(data);
        })
        .catch(error => {
            console.error('Error loading analytics:', error);
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            showAlert('Error loading analytics data. Please refresh the page.', 'danger');
        });
}

function updateStatistics(data) {
    // Update total notes
    const totalNotesEl = document.getElementById('totalNotes');
    if (totalNotesEl) {
        animateValue(totalNotesEl, 0, data.total_notes || 0, 1000);
    }

    // Update total interactions
    const totalInteractionsEl = document.getElementById('totalInteractions');
    if (totalInteractionsEl) {
        animateValue(totalInteractionsEl, 0, data.total_interactions || 0, 1000);
    }

    // Update average note length
    const avgNoteLengthEl = document.getElementById('avgNoteLength');
    if (avgNoteLengthEl) {
        const avgLength = Math.round((data.avg_note_length || 0) / 100); // Convert to readable format
        animateValue(avgNoteLengthEl, 0, avgLength, 1000);
        avgNoteLengthEl.textContent = avgLength + ' chars';
    }

    // Update recent notes count
    const recentNotesCountEl = document.getElementById('recentNotesCount');
    if (recentNotesCountEl) {
        const recentCount = data.recent_notes ? data.recent_notes.reduce((sum, item) => sum + item.count, 0) : 0;
        animateValue(recentNotesCountEl, 0, recentCount, 1000);
    }
}

function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const current = Math.floor(progress * (end - start) + start);
        element.textContent = current;
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            element.textContent = end;
        }
    };
    window.requestAnimationFrame(step);
}

function renderCharts(data) {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        const loadingIndicator = document.getElementById('loadingIndicator');
        if (loadingIndicator) {
            loadingIndicator.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Chart library failed to load. Please refresh the page.</div>';
        }
        return;
    }

    // Chart.js default configuration
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = document.body.classList.contains('dark-mode') ? '#e2e8f0' : '#4a5568';

    // 1. Notes Over Time Chart
    const notesOverTimeCtx = document.getElementById('notesOverTimeChart');
    if (notesOverTimeCtx) {
        if (data.notes_over_time && data.notes_over_time.length > 0) {
            const labels = data.notes_over_time.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });
            const values = data.notes_over_time.map(item => item.count);

            if (notesOverTimeChart) notesOverTimeChart.destroy();
            notesOverTimeChart = new Chart(notesOverTimeCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Notes Created',
                    data: values,
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
            });
        } else {
            notesOverTimeCtx.parentElement.innerHTML = '<div class="text-center p-4"><i class="fas fa-info-circle fa-3x text-muted mb-3"></i><p class="text-muted">No data available yet. Start creating notes to see analytics!</p></div>';
        }
    }

    // 2. Notes by Day of Week Chart
    const notesByDayCtx = document.getElementById('notesByDayChart');
    if (notesByDayCtx) {
        if (data.notes_by_day && data.notes_by_day.length > 0) {
            const dayLabels = data.notes_by_day.map(item => item.day.substring(0, 3));
            const dayValues = data.notes_by_day.map(item => item.count);
            const dayColors = [
                'rgba(102, 126, 234, 0.8)',
                'rgba(240, 147, 251, 0.8)',
                'rgba(79, 172, 254, 0.8)',
                'rgba(67, 233, 123, 0.8)',
                'rgba(237, 137, 54, 0.8)',
                'rgba(245, 101, 108, 0.8)',
                'rgba(118, 75, 162, 0.8)'
            ];

            if (notesByDayChart) notesByDayChart.destroy();
            notesByDayChart = new Chart(notesByDayCtx, {
                type: 'doughnut',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        data: dayValues,
                        backgroundColor: dayColors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right'
                        }
                    }
                }
            });
        } else {
            notesByDayCtx.parentElement.innerHTML = '<div class="text-center p-4"><i class="fas fa-info-circle fa-3x text-muted mb-3"></i><p class="text-muted">No data available yet.</p></div>';
        }
    }

    // 3. Notes by Hour of Day Chart
    const notesByHourCtx = document.getElementById('notesByHourChart');
    if (notesByHourCtx) {
        if (data.notes_by_hour && data.notes_by_hour.length > 0) {
            // Create array for all 24 hours
            const hourData = Array(24).fill(0);
            data.notes_by_hour.forEach(item => {
                hourData[item.hour] = item.count;
            });
            const hourLabels = Array.from({length: 24}, (_, i) => i + ':00');

            if (notesByHourChart) notesByHourChart.destroy();
            notesByHourChart = new Chart(notesByHourCtx, {
            type: 'bar',
            data: {
                labels: hourLabels,
                datasets: [{
                    label: 'Notes Created',
                    data: hourData,
                    backgroundColor: 'rgba(79, 172, 254, 0.6)',
                    borderColor: 'rgba(79, 172, 254, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
            });
        } else {
            notesByHourCtx.parentElement.innerHTML = '<div class="text-center p-4"><i class="fas fa-info-circle fa-3x text-muted mb-3"></i><p class="text-muted">No data available yet.</p></div>';
        }
    }

    // 4. Interactions Distribution Chart
    const interactionsCtx = document.getElementById('interactionsChart');
    if (interactionsCtx && data.interactions && data.interactions.length > 0) {
        const interactionLabels = data.interactions.map(item => 
            item.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
        );
        const interactionValues = data.interactions.map(item => item.count);

        if (interactionsChart) interactionsChart.destroy();
        interactionsChart = new Chart(interactionsCtx, {
            type: 'bar',
            data: {
                labels: interactionLabels,
                datasets: [{
                    label: 'Count',
                    data: interactionValues,
                    backgroundColor: 'rgba(240, 147, 251, 0.6)',
                    borderColor: 'rgba(240, 147, 251, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                indexAxis: 'y'
            }
        });
    } else if (interactionsCtx) {
        // Show message if no interactions
        interactionsCtx.parentElement.innerHTML = '<div class="text-center p-4"><i class="fas fa-info-circle fa-3x text-muted mb-3"></i><p class="text-muted">No interaction data available yet. Start using the system to see analytics!</p></div>';
    }

    // 5. Activity Timeline Chart
    const activityTimelineCtx = document.getElementById('activityTimelineChart');
    if (activityTimelineCtx) {
        // Combine notes and interactions data
        const timelineLabels = [];
        const notesData = [];
        const interactionsData = [];

        // Get all unique dates from both datasets
        const allDates = new Set();
        if (data.recent_notes && data.recent_notes.length > 0) {
            data.recent_notes.forEach(item => allDates.add(item.date));
        }
        if (data.interactions_over_time && data.interactions_over_time.length > 0) {
            data.interactions_over_time.forEach(item => allDates.add(item.date));
        }

        if (allDates.size > 0) {
            const sortedDates = Array.from(allDates).sort();

            sortedDates.forEach(date => {
                const dateObj = new Date(date);
                timelineLabels.push(dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                
                const noteItem = data.recent_notes ? data.recent_notes.find(item => item.date === date) : null;
                notesData.push(noteItem ? noteItem.count : 0);
                
                const interactionItem = data.interactions_over_time ? 
                    data.interactions_over_time.find(item => item.date === date) : null;
                interactionsData.push(interactionItem ? interactionItem.count : 0);
            });

            if (activityTimelineChart) activityTimelineChart.destroy();
            activityTimelineChart = new Chart(activityTimelineCtx, {
                type: 'line',
                data: {
                    labels: timelineLabels,
                    datasets: [
                        {
                            label: 'Notes Created',
                            data: notesData,
                            borderColor: 'rgb(102, 126, 234)',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'User Interactions',
                            data: interactionsData,
                            borderColor: 'rgb(240, 147, 251)',
                            backgroundColor: 'rgba(240, 147, 251, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } else {
            activityTimelineCtx.parentElement.innerHTML = '<div class="text-center p-4"><i class="fas fa-info-circle fa-3x text-muted mb-3"></i><p class="text-muted">No activity data available yet.</p></div>';
        }
    }
}

// Update charts on window resize
window.addEventListener('resize', function() {
    if (notesOverTimeChart) notesOverTimeChart.resize();
    if (notesByDayChart) notesByDayChart.resize();
    if (notesByHourChart) notesByHourChart.resize();
    if (interactionsChart) interactionsChart.resize();
    if (activityTimelineChart) activityTimelineChart.resize();
});

