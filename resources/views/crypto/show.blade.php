<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $coin['name'] }} - Crypto Portfolio</title>
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <!-- Coin Header -->
    <div class="coin-header">
        <img src="{{ $coin['image']['large'] ?? $coin['image'] }}" alt="{{ $coin['name'] }}" class="coin-logo">
        <div class="coin-info">
            <h1>{{ $coin['name'] }} ({{ strtoupper($coin['symbol']) }})</h1>
            <p>Current Price: ${{ number_format($coin['market_data']['current_price']['usd'], 2) }}</p>
            <p>Market Cap: ${{ number_format($coin['market_data']['market_cap']['usd']) }}</p>
            <p>24h Change: 
                <span class="{{ $coin['market_data']['price_change_percentage_24h'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($coin['market_data']['price_change_percentage_24h'], 2) }}%
                </span>
            </p>
        </div>
    </div>

    <!-- Timeframe Buttons -->
    <div class="timeframe-buttons">
        <button data-days="1" class="active">1D</button>
        <button data-days="7">7D</button>
        <button data-days="30">1M</button>
        <button data-days="90">3M</button>
    </div>

    <!-- Chart -->
    <canvas id="priceChart"></canvas>

    <script>
        const ctx = document.getElementById('priceChart').getContext('2d');
        let chart = new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [{ label: '{{ $coin["name"] }} ({{ strtoupper($coin["symbol"]) }})', data: [], borderColor: '#007bff', backgroundColor: 'rgba(0,123,255,0.2)', fill: true }]},
            options: { responsive: true, maintainAspectRatio: false, scales: { x: { display: true, title: { display: true, text: 'Date' } }, y: { display: true, title: { display: true, text: 'Price (USD)' } } } }
        });

        function fetchChart(days = 7) {
            axios.get(`https://api.coingecko.com/api/v3/coins/{{ strtolower($coin['id']) }}/market_chart?vs_currency=usd&days=${days}`)
                .then(response => {
                    const data = response.data;
                    chart.data.labels = data.prices.map(item => new Date(item[0]).toLocaleDateString());
                    chart.data.datasets[0].data = data.prices.map(item => item[1]);
                    chart.update();
                })
                .catch(err => console.error(err));
        }

        fetchChart();

        document.querySelectorAll('.timeframe-buttons button').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.timeframe-buttons button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                fetchChart(btn.dataset.days);
            });
        });
    </script>
</body>
</html>
