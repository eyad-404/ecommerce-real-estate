$(document).ready(function() {
    const pieCtx = document.getElementById('pieChart');
    const config = window.dashboardConfig || {};

    const normalizeSales = (data) => {
        if (Array.isArray(data)) return data;
        if (data && typeof data === 'object') return Object.values(data);
        return [];
    };

    const renderSalesTable = (data) => {
        const $body = $('#salesTableBody');
        const $empty = $('#salesTableEmpty');
        $body.empty();

        if (!data.length) {
            $empty.removeClass('d-none');
            return;
        }

        $empty.addClass('d-none');

        data.forEach((point, idx) => {
            const label = point?.label ?? `Month ${idx + 1}`;
            const listings = point?.listings ?? 0;
            const reservations = point?.reservations ?? 0;

            $body.append(`
                <tr>
                    <td>${label}</td>
                    <td>${listings}</td>
                    <td>${reservations}</td>
                </tr>
            `);
        });
    };

    let pieData = config.initialPie || {};
    let salesDataSafe = normalizeSales(config.salesData);
    const clientDataUrl = config.clientDataUrl || $('#clientSelect').data('endpoint') || '/dashboard/client-data';

    const getPieLabels = (pie) => Object.keys(pie || {});

    let pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: getPieLabels(pieData),
            datasets: [{
                data: getPieLabels(pieData).map(l => pieData[l] || 0),
                backgroundColor: ['#a3c3d6','#f4b787','#cfd6e3','#d6a3c3','#87f4b7'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { position: 'bottom' } },
            animation: { duration: 800 }
        }
    });

    renderSalesTable(salesDataSafe);

    const $clientSelect = $('#clientSelect');
    if ($clientSelect.length) {
        $clientSelect.change(function() {
            if (!clientDataUrl) {
                return;
            }
            const clientId = $(this).val();

            $.ajax({
                url: clientDataUrl,
                method: 'GET',
                data: { id: clientId },
                success: function(res) {
                    if(res.error) return alert(res.error);

                    const newLabels = Object.keys(res.pie || {});
                    pieChart.data.labels = newLabels;
                    pieChart.data.datasets[0].data = newLabels.map(l => res.pie[l] || 0);
                    pieChart.update();

                    const salesPayload = normalizeSales(res.sales);
                    renderSalesTable(salesPayload);
                },
                error: function(err) { console.error('AJAX error:', err); }
            });
        });
    }

    $('#randomizeBtn').click(function() {
        const labels = pieChart.data.labels;
        pieChart.data.datasets[0].data = labels.map(() => Math.floor(Math.random()*30)+10);
        pieChart.update();
    });
});
