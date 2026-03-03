<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $table->number }} | Digital Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #22c55e;
            --primary-soft: #ecfdf5;
            --bg-main: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        * { margin:0; padding:0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            padding-bottom: 80px;
        }

        .header {
            background: white;
            padding: 2.5rem 1.5rem;
            text-align: center;
            border-bottom-left-radius: 32px;
            border-bottom-right-radius: 32px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.03);
            position: sticky;
            top: -20px;
            z-index: 100;
        }

        .logo { 
            font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;
        }
        .logo span { color: var(--primary); }

        .table-chip {
            display: inline-block;
            background: var(--primary-soft);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .container { padding: 1.5rem; }

        .category-section { margin-bottom: 2.5rem; }

        .category-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-title::after {
            content: ''; height: 2px; flex: 1; background: #e2e8f0; border-radius: 2px;
        }

        .menu-items { display: grid; gap: 1.25rem; }

        .item-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 2px 15px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }

        .item-image {
            width: 100px;
            height: 100px;
            flex-shrink: 0;
            background: #f1f5f9;
        }

        .item-image img { width: 100%; height: 100%; object-fit: cover; }

        .item-content {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .item-name { 
            font-size: 0.95rem; 
            font-weight: 700; 
            color: #1e293b; 
            line-height: 1.3;
        }

        .item-price {
            font-weight: 800;
            color: var(--primary);
            font-size: 0.95rem;
        }

        .item-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .empty-visual { text-align: center; color: #cbd5e1; padding: 4rem 1rem; }

        .footer-action {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background: #1e293b;
            color: white;
            padding: 1rem;
            border-radius: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 200;
        }

        .fade-in { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Mlup <span>Dong</span></div>
        <div class="table-chip">Table {{ $table->number }}</div>
    </header>

    <div class="container fade-in">
        @forelse($menuItems as $category => $items)
            <section class="category-section">
                <h2 class="category-title">{{ $category }}</h2>
                <div class="menu-items">
                    @foreach($items as $item)
                        <div class="item-card">
                            @if($item->image_url)
                                <div class="item-image">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
                                </div>
                            @endif
                            <div class="item-content">
                                <div>
                                    <div class="item-header">
                                        <h3 class="item-name">{{ $item->name }}</h3>
                                        <span class="item-price">${{ number_format($item->price, 2) }}</span>
                                    </div>
                                    <p class="item-desc">{{ $item->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="empty-visual">
                <h3>Menu offline</h3>
                <p>We're currently updating our daily kitchen assets.</p>
            </div>
        @endforelse
    </div>

    <div class="footer-action">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        Call for Service
    </div>

    <script>
        // Smooth reveal on scroll logic can be added here
    </script>
</body>
</html>
