<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cryptocurrency Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
</head>
<body style="background-color: #f5f7fa;">

<div class="container py-5">
    <h1 class="text-center mb-4">Cryptocurrency Dashboard</h1>

    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="form-group">
                <label for="sortSelect" class="font-weight-bold">Sort by:</label>
                <select id="sortSelect" class="form-control">
                    <option value="name">Name</option>
                    <option value="price">Price</option>
                    <option value="marketCap">Market Cap</option>
                    <option value="change">24h Change</option>
                    <option value="volume">24h Volume</option>
                    <option value="rank">Rank</option>
                </select>
            </div>
        </div>
    </div>

    <div id="cryptoList" class="list-group"></div>
</div>

<div id="errorContainer" class="container mt-3"></div>

<script src="{{ asset('js/index.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
