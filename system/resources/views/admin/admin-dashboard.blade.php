@extends('layouts.app')

@section('title', 'Admin Dashboard | Kamatage Hearing Aid')

@section('page-title', 'Dashboard')

@section('content')
    <div class="greeting" style="margin-top:-80px; margin-bottom:14px; min-height:40px; max-width:1200px; width:100%; display:flex; align-items:center; justify-content:flex-start; background:#90A1B9; color:#000; border-radius:18px; padding:12px 24px 12px 18px; font-size:1.08em; position:relative; overflow:visible;">
        <span class="icon" style="font-size:2.2em; margin-right:16px; margin-left:10px;">&#9728;&#65039;</span>
        <div style="flex:1;">
            <span style="font-weight:bold; font-size:1.6em; display:block; margin-bottom:3px;">Good day, Admin!</span>
            <span style="font-size:1em; display:block;">A new day to lead with clarity and confidence.<br>Your dashboard is ready for action.</span>
            <span style="font-weight:bold; font-size:1.08em; display:block; margin-top:7px;">Today is {{ date('l, d M. Y') }}</span>
        </div>
    <img src="/images/analytics-3d.png" alt="Analytics Illustration" style="height:150px; position:absolute; right:8px; top:50%; transform:translateY(-55%); z-index:2;">
    </div>
    
    <div class="dashboard-row" style="margin-top:14px; display:flex; align-items:flex-start; justify-content:center;">
        <div class="pie-chart-box" style="background:#fff; border:2px solid #bbb; border-radius:12px; min-width:460px; max-width:400px; width:100%; margin-right:32px; box-shadow:0 2px 8px #eee; display:flex; align-items:center; justify-content:center;">
            <canvas id="appointmentPieChart" width="330" height="160"></canvas>
        </div>
        <div class="report-box" style="background:#fff; border:2px solid #bbb; border-radius:12px; padding:24px 32px; min-width:260px; box-shadow:0 2px 8px #eee;">
            <h5 style="font-weight:bold; margin-bottom:8px;">Monthly Appointment Report</h5>
            <div style="margin-bottom:8px;">September 2025</div>
            <ul style="list-style:none; padding-left:0; margin-top:20px;">
                <li style="margin-bottom:12px;"><span style="color:#800080; font-weight:bold; margin-right:8px;">&#9632;</span> <span style="font-weight:bold;">Branch 3 Davao</span><br><span style="margin-left:24px;">25 Patients (25%)</span></li>
                <li style="margin-bottom:12px;"><span style="color:#6c3483; font-weight:bold; margin-right:8px;">&#9632;</span> <span style="font-weight:bold;">Branch 2 Butuan</span><br><span style="margin-left:24px;">30 Patients (30%)</span></li>
                <li><span style="color:#f39200; font-weight:bold; margin-right:8px;">&#9632;</span> <span style="font-weight:bold;">Branch 1 CDO</span><br><span style="margin-left:24px;">45 Patients (45%)</span></li>
            </ul>
        </div>
    </div>


    


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Helper to render monthly report list
        function renderMonthlyReport(container, data) {
            // data shape: { month, total_patients, branches: [ { branch_name, patient_count, percentage } ] }
            try {
                const monthDiv = container.querySelector('[data-month]');
                const list = container.querySelector('ul');
                if (!data || !Array.isArray(data.branches) || data.branches.length === 0) return;
                if (monthDiv) {
                    monthDiv.textContent = data.month || monthDiv.textContent;
                }
                // Clear existing items
                list.innerHTML = '';
                const colors = ['#800080', '#6c3483', '#f39200', '#17a2b8', '#28a745', '#ffc107'];
                data.branches.forEach((b, i) => {
                    const li = document.createElement('li');
                    li.style.marginBottom = '12px';
                    const color = colors[i % colors.length];
                    li.innerHTML = `<span style="color:${color}; font-weight:bold; margin-right:8px;">&#9632;</span>
                        <span style="font-weight:bold;">${b.branch_name}</span><br>
                        <span style="margin-left:24px;">${b.patient_count} Patients (${b.percentage}%)</span>`;
                    list.appendChild(li);
                });
            } catch (e) {
                // Keep fallback
            }
        }

        // Chart rendering with dynamic data, fallback to static when needed
        const chartCanvas = document.getElementById('appointmentPieChart');
        let chartInstance = null;
        function renderChart(labels, values, colors) {
            if (!chartCanvas) return;
            const ctx = chartCanvas.getContext('2d');
            if (chartInstance) { chartInstance.destroy(); }
            chartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold', size: 16 },
                            formatter: function(value, context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percent = total > 0 ? Math.round((value / total) * 100) : 0;
                                return percent + '%';
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }

        const fallbackLabels = ['Butuan', 'Davao', 'CDO'];
        const fallbackValues = [25, 30, 45];
        const fallbackColors = ['#6c3483', '#800080', '#f39200'];
        renderChart(fallbackLabels, fallbackValues, fallbackColors);

        // Fetch dynamic data from backend APIs
        // 1) Chart data
        fetch('/admin/api/chart-data', { headers: { 'Accept': 'application/json' } })
            .then(r => r.ok ? r.json() : null)
            .then(json => {
                if (!json || !Array.isArray(json) || json.length === 0) return; // keep fallback
                const labels = json.map(i => i.name);
                const values = json.map(i => i.count);
                // build colors list long enough
                const baseColors = ['#6c3483', '#800080', '#f39200', '#17a2b8', '#28a745', '#ffc107'];
                const colors = labels.map((_, idx) => baseColors[idx % baseColors.length]);
                renderChart(labels, values, colors);
            })
            .catch(() => { /* keep fallback */ });

        // 2) Monthly report list
        const reportBox = document.querySelector('.report-box');
        if (reportBox) {
            // mark the month div so we can update it
            let monthDiv = reportBox.querySelector('div');
            if (monthDiv) { monthDiv.setAttribute('data-month', '1'); }
            fetch('/admin/api/monthly-report', { headers: { 'Accept': 'application/json' } })
                .then(r => r.ok ? r.json() : null)
                .then(json => {
                    if (!json || !json.branches) return; // keep fallback
                    renderMonthlyReport(reportBox, json);
                })
                .catch(() => { /* keep fallback */ });
        }
    });
    </script>
@endpush
@endsection
