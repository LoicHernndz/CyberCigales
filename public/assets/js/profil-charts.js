/**
 * Graphiques du profil utilisateur (Chart.js)
 * Les données sont injectées via des balises <script type="application/json">
 */
document.addEventListener('DOMContentLoaded', function () {
    var activityEl = document.getElementById('profil-activity-data');
    var progressionEl = document.getElementById('profil-progression-data');

    var activityData = activityEl ? JSON.parse(activityEl.textContent) : [];
    var progressionData = progressionEl ? JSON.parse(progressionEl.textContent) : null;

    // Configuration de Chart.js
    Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Graphique d'activité récente
    var activityCtx = document.getElementById('activityChart');
    if (activityCtx) {
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: activityData.map(function (d) { return d.date; }),
                datasets: [
                    {
                        label: 'Formation RGPD',
                        data: activityData.map(function (d) { return d.rgpd; }),
                        borderColor: '#00ff41',
                        backgroundColor: 'rgba(0, 255, 65, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#00ff41',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Cypher Rush',
                        data: activityData.map(function (d) { return d.cypher; }),
                        borderColor: '#00ffff',
                        backgroundColor: 'rgba(0, 255, 255, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#00ffff',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 13, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(10, 14, 39, 0.95)',
                        titleColor: '#00ff41',
                        bodyColor: '#fff',
                        borderColor: '#00ff41',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' activité(s)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 12 } },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 12 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Graphique de progression (Doughnut)
    var progressionCtx = document.getElementById('progressionChart');
    if (progressionCtx && progressionData) {
        var modules = Object.values(progressionData);
        var labels = modules.map(function (m) { return m.name; });
        var data = modules.map(function (m) { return m.completion; });
        var colors = ['#00ff41', '#00ffff', '#ff0040', '#ffaa00'];

        new Chart(progressionCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.map(function (c) { return c + '40'; }),
                    borderColor: colors,
                    borderWidth: 3,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(10, 14, 39, 0.95)',
                        titleColor: '#00ff41',
                        bodyColor: '#fff',
                        borderColor: '#00ff41',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
