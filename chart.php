<?php
$defaults = [
    'symbol1' => 'NASDAQ:TSLA',
    'symbol2' => 'NASDAQ:ORCL',
    'symbol3' => 'NASDAQ:AVGO',
    'symbol4' => 'NASDAQ:AMZN',
];

$symbols = [];
foreach ($defaults as $key => $default) {
    $value = $_GET[$key] ?? $default;
    $value = strtoupper(trim($value));
    $value = preg_replace('/[^A-Z0-9:\._-]/', '', $value);
    $symbols[$key] = $value !== '' ? $value : $default;
}

$interval = $_GET['interval'] ?? 'D';
$interval = preg_replace('/[^A-Z0-9]/', '', strtoupper($interval));
if ($interval === '') {
    $interval = 'D';
}

$theme = ($_GET['theme'] ?? 'light') === 'dark' ? 'dark' : 'light';

$indicatorSet = $_GET['set'] ?? 'clean';

$allowedSets = [
    'clean' => [],
    'trend' => [
        'MASimple@tv-basicstudies',
        'MACD@tv-basicstudies'
    ],
    'momentum' => [
        'RSI@tv-basicstudies',
        'MACD@tv-basicstudies'
    ],
    'swing' => [
        'MASimple@tv-basicstudies',
        'RSI@tv-basicstudies',
        'MACD@tv-basicstudies'
    ],
    'ma_custom' => [
        'MASimple@tv-basicstudies',
        'MASimple@tv-basicstudies'
    ],
];

$studies = $allowedSets[$indicatorSet] ?? $allowedSets['clean'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TradingView 2x2 Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
        }
        .dashboard-wrap {
            max-width: 1600px;
        }
        .chart-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .chart-header {
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .chart-box {
            height: 420px;
            background: #fff;
        }
        .tv-host,
        .tradingview-widget-container,
        .tradingview-widget-container__widget {
            height: 100%;
            width: 100%;
        }
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
        }
        @media (max-width: 991.98px) {
            .chart-box {
                height: 360px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid py-4 dashboard-wrap">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">TradingView 2x2 Dashboard</h1>
            <p class="text-muted mb-0">
                Four charts, one page. Use the <strong>2 Moving Averages</strong> preset, then edit each MA on the chart once to set:
                20/red and 200/blue.
            </p>
        </div>
        <div>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#controlPanel" aria-expanded="true" aria-controls="controlPanel">
                Toggle Controls
            </button>
        </div>
    </div>

    <div class="collapse show mb-4" id="controlPanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form method="get" class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <label class="form-label" for="symbol1">Chart 1</label>
                        <input class="form-control" type="text" id="symbol1" name="symbol1" value="<?= htmlspecialchars($symbols['symbol1'], ENT_QUOTES, 'UTF-8') ?>" placeholder="NASDAQ:AAPL">
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <label class="form-label" for="symbol2">Chart 2</label>
                        <input class="form-control" type="text" id="symbol2" name="symbol2" value="<?= htmlspecialchars($symbols['symbol2'], ENT_QUOTES, 'UTF-8') ?>" placeholder="NASDAQ:MSFT">
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <label class="form-label" for="symbol3">Chart 3</label>
                        <input class="form-control" type="text" id="symbol3" name="symbol3" value="<?= htmlspecialchars($symbols['symbol3'], ENT_QUOTES, 'UTF-8') ?>" placeholder="NASDAQ:NVDA">
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <label class="form-label" for="symbol4">Chart 4</label>
                        <input class="form-control" type="text" id="symbol4" name="symbol4" value="<?= htmlspecialchars($symbols['symbol4'], ENT_QUOTES, 'UTF-8') ?>" placeholder="AMEX:SPY">
                    </div>

                    <div class="col-md-4 col-xl-2">
                        <label class="form-label" for="interval">Interval</label>
                        <select class="form-select" id="interval" name="interval">
                            <?php
                            $intervals = ['1', '5', '15', '30', '60', '240', 'D', 'W'];
                            foreach ($intervals as $item):
                            ?>
                                <option value="<?= $item ?>" <?= $interval === $item ? 'selected' : '' ?>><?= $item ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 col-xl-3">
                        <label class="form-label" for="set">Indicator Set</label>
                        <select class="form-select" id="set" name="set">
                            <option value="clean" <?= $indicatorSet === 'clean' ? 'selected' : '' ?>>Clean</option>
                            <option value="trend" <?= $indicatorSet === 'trend' ? 'selected' : '' ?>>Trend</option>
                            <option value="momentum" <?= $indicatorSet === 'momentum' ? 'selected' : '' ?>>Momentum</option>
                            <option value="swing" <?= $indicatorSet === 'swing' ? 'selected' : '' ?>>Swing</option>
                            <option value="ma_custom" <?= $indicatorSet === 'ma_custom' ? 'selected' : '' ?>>2 Moving Averages</option>
                        </select>
                    </div>

                    <div class="col-md-4 col-xl-2">
                        <label class="form-label" for="theme">Theme</label>
                        <select class="form-select" id="theme" name="theme">
                            <option value="light" <?= $theme === 'light' ? 'selected' : '' ?>>Light</option>
                            <option value="dark" <?= $theme === 'dark' ? 'selected' : '' ?>>Dark</option>
                        </select>
                    </div>

                    <div class="col-xl-5 d-flex align-items-end gap-2 flex-wrap">
                        <button class="btn btn-primary" type="submit">Load Dashboard</button>
                        <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($symbols as $key => $symbol): ?>
            <div class="col-12 col-xl-6">
                <div class="card chart-card h-100">
                    <div class="card-header bg-white border-0 py-3 px-4 chart-header d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars($symbol, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="badge text-bg-light border"><?= htmlspecialchars($interval, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-box">
                            <div class="tv-host" id="tv_<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const chartConfigs = <?= json_encode([
    [
        'containerId' => 'tv_symbol1',
        'symbol' => $symbols['symbol1'],
    ],
    [
        'containerId' => 'tv_symbol2',
        'symbol' => $symbols['symbol2'],
    ],
    [
        'containerId' => 'tv_symbol3',
        'symbol' => $symbols['symbol3'],
    ],
    [
        'containerId' => 'tv_symbol4',
        'symbol' => $symbols['symbol4'],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

const sharedOptions = <?= json_encode([
    'interval' => $interval,
    'timezone' => 'America/Phoenix',
    'theme' => $theme,
    'style' => '1',
    'locale' => 'en',
    'allow_symbol_change' => true,
    'withdateranges' => true,
    'hide_side_toolbar' => false,
    'save_image' => false,
    'studies' => $studies,
    'calendar' => false,
    'support_host' => 'https://www.tradingview.com',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

function renderTradingViewCharts() {
    chartConfigs.forEach((config) => {
        const host = document.getElementById(config.containerId);
        if (!host) {
            return;
        }

        const container = document.createElement('div');
        container.className = 'tradingview-widget-container';

        const widget = document.createElement('div');
        widget.className = 'tradingview-widget-container__widget';

        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.async = true;
        script.src = 'https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js';
        script.text = JSON.stringify({
            ...sharedOptions,
            autosize: true,
            symbol: config.symbol,
        });

        container.appendChild(widget);
        container.appendChild(script);
        host.innerHTML = '';
        host.appendChild(container);
    });
}

renderTradingViewCharts();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>