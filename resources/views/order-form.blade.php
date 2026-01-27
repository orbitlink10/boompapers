<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place an Order | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #0f5951;
            --green-soft: #0b4841;
            --dark: #1c1c1c;
            --muted: #565e64;
            --border: #dce3e6;
            --bg: #f8fafb;
            --card: #ffffff;
            --yellow: #ffb300;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
        }
        header {
            padding: 26px 30px 10px;
        }
        h1 { margin: 0; font-size: 36px; letter-spacing: -0.3px; }
        .lead { margin: 6px 0 0 0; color: var(--muted); font-weight: 600; }
        .layout {
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 0 20px 40px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 22px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 18px 40px rgba(17, 42, 72, 0.08);
        }
        .form-grid { display: grid; gap: 16px; }
        label { font-weight: 800; margin-bottom: 8px; display: block; color: #2c2f33; }
        select, input, textarea {
            width: 100%;
            padding: 13px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fdfefe;
            font-weight: 600;
            font-size: 15px;
            outline: none;
            transition: border .12s ease, box-shadow .12s ease;
        }
        select:focus, input:focus, textarea:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(15, 89, 81, 0.12);
        }
        textarea { min-height: 140px; resize: vertical; }
        .choices {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
        }
        .pill {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            text-align: center;
            font-weight: 800;
            cursor: pointer;
            background: #f5f7f8;
            transition: all .12s ease;
        }
        .pill.active {
            background: var(--green);
            color: #fff;
            border-color: var(--green);
        }
        .flex-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; align-items: center; }
        .quantity { display: grid; grid-template-columns: 60px 1fr 60px; align-items: center; }
        .quantity button {
            height: 44px;
            border: 1px solid var(--border);
            background: #fff;
            font-weight: 900;
            font-size: 18px;
            cursor: pointer;
        }
        .quantity input { text-align: center; font-weight: 800; }
        .btn {
            border: none;
            border-radius: 12px;
            padding: 14px 16px;
            font-weight: 900;
            font-size: 16px;
            cursor: pointer;
            transition: transform .1s ease, box-shadow .15s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-green {
            background: var(--green);
            color: #fff;
            box-shadow: 0 14px 30px rgba(15, 89, 81, 0.3);
            width: 100%;
        }
        .coupon {
            background: #ffa400;
            border-radius: 12px;
            padding: 12px;
            display: grid;
            grid-template-columns: 1fr 140px;
            gap: 10px;
            align-items: center;
        }
        .coupon input {
            background: #fff6df;
            border: 1px solid #f7d26b;
        }
        .coupon button {
            background: #fff;
            color: #d17a00;
            border: none;
            height: 44px;
            border-radius: 10px;
            font-weight: 900;
            cursor: pointer;
        }
        .sidebar {
            position: sticky;
            top: 20px;
            display: grid;
            gap: 12px;
        }
        .summary-row { display: flex; justify-content: space-between; align-items: center; margin: 6px 0; }
        .summary-label { color: var(--muted); font-weight: 700; }
        .summary-value { font-weight: 800; color: var(--dark); }
        .total {
            font-size: 24px;
            font-weight: 900;
            color: var(--green);
        }
        .cards { display: flex; gap: 10px; flex-wrap: wrap; }
        .cards img { height: 26px; }
        .section-title { font-weight: 900; margin: 10px 0 4px; }
        @media (max-width: 950px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { position: static; }
        }
    </style>
</head>
<body>
    <header>
        <h1>Place an Order</h1>
        <p class="lead">It’s fast, secure, and confidential.</p>
    </header>

    <div class="layout">
        <div class="card">
            <form class="form-grid" action="/order/submit" method="POST">
                @csrf
                <div>
                    <label>Type of Paper</label>
                    <select name="type">
                        <option>Essay (Any)</option>
                        <option>Research Paper</option>
                        <option>Business Plan</option>
                        <option>Case Study</option>
                        <option>Presentation</option>
                    </select>
                </div>

                <div>
                    <label>Academic Level</label>
                    <input type="hidden" name="level" id="levelInput" value="High School">
                    <div class="choices" data-group="level">
                        <div class="pill active">High School</div>
                        <div class="pill">College</div>
                        <div class="pill">Masters</div>
                        <div class="pill">PhD</div>
                    </div>
                </div>

                <div class="flex-row">
                    <div>
                        <label>Subject</label>
                        <select name="subject">
                            <option>Other</option>
                            <option>Business</option>
                            <option>Nursing</option>
                            <option>Technology</option>
                            <option>Literature</option>
                        </select>
                    </div>
                    <div>
                        <label>Title</label>
                        <input type="text" name="title" placeholder="Enter paper title">
                    </div>
                </div>

                <div>
                    <label>Instructions</label>
                    <textarea name="instructions" placeholder="Please paste all your paper instructions here"></textarea>
                </div>

                <div class="flex-row">
                    <div>
                        <label>Paper format</label>
                        <input type="hidden" name="format" id="formatInput" value="APA">
                        <div class="choices" data-group="format">
                            <div class="pill">MLA</div>
                            <div class="pill active">APA</div>
                            <div class="pill">Harvard</div>
                            <div class="pill">Chicago</div>
                            <div class="pill">Other</div>
                        </div>
                    </div>
                </div>

                <div class="flex-row">
                    <div>
                        <label>Number of Pages</label>
                        <div class="quantity">
                            <button type="button" onclick="step('pages', -1)">−</button>
                            <input id="pages" name="pages" type="number" value="1" min="1">
                            <button type="button" onclick="step('pages', 1)">+</button>
                        </div>
                        <div class="helper">Approx 275 words</div>
                    </div>
                    <div>
                        <label>Spacing</label>
                        <input type="hidden" name="spacing" id="spacingInput" value="Double">
                        <div class="choices" data-group="spacing">
                            <div class="pill active">Double</div>
                            <div class="pill">Single</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label>Currency</label>
                    <input type="hidden" name="currency" id="currencyInput" value="USD">
                    <div class="choices" data-group="currency">
                        <div class="pill active">USD</div>
                        <div class="pill">GBP</div>
                        <div class="pill">EUR</div>
                        <div class="pill">AUD</div>
                    </div>
                </div>

                <div class="coupon">
                    <input type="text" placeholder="Have coupon? Enter here">
                    <button type="button">APPLY</button>
                </div>

                <div class="flex-row">
                    <div>
                        <label>Number of Sources</label>
                        <div class="quantity">
                            <button type="button" onclick="step('sources', -1)">−</button>
                            <input id="sources" name="sources" type="number" value="0" min="0">
                            <button type="button" onclick="step('sources', 1)">+</button>
                        </div>
                    </div>
                    <div>
                        <label>PowerPoint Slides</label>
                        <div class="quantity">
                            <button type="button" onclick="step('slides', -1)">−</button>
                            <input id="slides" name="slides" type="number" value="0" min="0">
                            <button type="button" onclick="step('slides', 1)">+</button>
                        </div>
                    </div>
                    <div>
                        <label>Charts</label>
                        <div class="quantity">
                            <button type="button" onclick="step('charts', -1)">−</button>
                            <input id="charts" name="charts" type="number" value="0" min="0">
                            <button type="button" onclick="step('charts', 1)">+</button>
                        </div>
                    </div>
                </div>

                <div>
                    <label>Deadline</label>
                    <input type="hidden" name="deadline" id="deadlineInput" value="48 Hours">
                    <div class="choices" data-group="deadline">
                        <div class="pill">8 Hours</div>
                        <div class="pill">24 Hours</div>
                        <div class="pill active">48 Hours</div>
                        <div class="pill">3 Days</div>
                        <div class="pill">5 Days</div>
                        <div class="pill">7 Days</div>
                        <div class="pill">14 Days</div>
                    </div>
                </div>

                <div>
                    <label>Category</label>
                    <input type="hidden" name="category" id="categoryInput" value="Standard">
                    <div class="choices" data-group="category">
                        <div class="pill active">Standard</div>
                        <div class="pill">Premium</div>
                        <div class="pill">Platinum</div>
                    </div>
                </div>

                <div>
                    <label>Additional Services</label>
                    <div class="card" style="border:dashed 1px var(--border); box-shadow:none;">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <input type="checkbox" id="vip">
                            <div>
                                <div class="section-title">VIP support <span style="color:var(--green);">$25</span></div>
                                <div class="helper">24/7 VIP manager and priority communication.</div>
                            </div>
                        </div>
                        <hr style="margin:16px -18px; border:none; border-top:1px solid var(--border);">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <input type="checkbox" id="draft">
                            <div>
                                <div class="section-title">Draft/outline <span style="color:var(--green);">$20</span></div>
                                <div class="helper">Receive a draft outlining structure before final paper.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-green">Submit Order</button>
            </form>
        </div>

        <aside class="sidebar">
            <div class="card">
                <div class="section-title">Summary</div>
                <div class="summary-row"><span class="summary-label">Level</span><span class="summary-value">College</span></div>
                <div class="summary-row"><span class="summary-label">Type</span><span class="summary-value">Essay (Any)</span></div>
                <div class="summary-row"><span class="summary-label">Pages</span><span class="summary-value">1 page x $21.60</span></div>
                <hr style="border:none; border-top:1px solid var(--border); margin:12px 0;">
                <div class="summary-row"><span class="section-title">Total Price</span><span class="total">$21.60</span></div>
                <div class="cards">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/0e/Mastercard-logo.png" alt="Mastercard">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/30/American_Express_logo_%282018%29.svg" alt="Amex">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5a/Discover_Card_logo.svg" alt="Discover">
                </div>
            </div>
        </aside>
    </div>

    <script>
        function step(id, delta) {
            const input = document.getElementById(id);
            const next = Math.max(parseInt(input.value || 0) + delta, parseInt(input.min || 0));
            input.value = next;
        }
        // pill toggles within each data-group container
        document.querySelectorAll('[data-group]').forEach(group => {
            group.addEventListener('click', e => {
                const pill = e.target.closest('.pill');
                if (!pill) return;
                [...group.children].forEach(el => el.classList.toggle('active', el === pill));
                const hidden = document.getElementById(group.dataset.group + 'Input');
                if (hidden) hidden.value = pill.textContent.trim();
            });
        });
        // set initial hidden values based on default actives
        document.querySelectorAll('[data-group]').forEach(group => {
            const active = group.querySelector('.pill.active') || group.querySelector('.pill');
            if (active) {
                const hidden = document.getElementById(group.dataset.group + 'Input');
                if (hidden) hidden.value = active.textContent.trim();
            }
        });
    </script>
</body>
</html>
