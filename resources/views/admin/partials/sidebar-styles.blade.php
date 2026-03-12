        .sidebar {
            background: #fff;
            border-right: 1px solid var(--border, #e5e8ed);
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 8px 0 30px rgba(17, 23, 32, 0.04);
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-brand .icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(
                135deg,
                var(--sidebar-accent, var(--accent, var(--primary-strong, #f25c3c))),
                var(--sidebar-accent-secondary, var(--primary, #ff8a65))
            );
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.04em;
            box-shadow: 0 14px 28px rgba(17, 23, 32, 0.12);
        }
        .sidebar-brand .label {
            display: grid;
            gap: 2px;
        }
        .sidebar-brand .eyebrow {
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted, #6b6b7a);
            font-weight: 800;
        }
        .sidebar-brand .title {
            font-size: 26px;
            line-height: 1;
            color: var(--dark, #1c1c28);
            font-weight: 800;
        }
        .sidebar-nav {
            display: grid;
            gap: 12px;
            align-content: start;
        }
        .nav-group {
            display: grid;
            gap: 10px;
            padding: 14px;
            border: 1px solid var(--border, #e5e8ed);
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff, #fafbff);
            align-content: start;
        }
        .nav-title {
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted, #6b6b7a);
            font-weight: 800;
        }
        .nav-links {
            display: grid;
            gap: 6px;
        }
        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 44px;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid transparent;
            background: transparent;
            color: #2f3236;
            font-weight: 800;
            text-decoration: none;
            transition: background .15s ease, border-color .15s ease, color .15s ease, transform .15s ease;
        }
        .nav-link span:first-child {
            min-width: 0;
        }
        .nav-link.active,
        .nav-link:hover {
            background: var(--sidebar-soft, var(--primary-soft, #fff2ec));
            border-color: rgba(17, 23, 32, 0.06);
            color: var(--sidebar-accent, var(--accent, var(--primary-strong, #f25c3c)));
            transform: translateX(1px);
        }
        .nav-count {
            flex-shrink: 0;
            min-width: 28px;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid var(--border, #e5e8ed);
            background: #fff;
            color: var(--sidebar-accent, var(--accent, var(--primary-strong, #f25c3c)));
            font-size: 12px;
            font-weight: 800;
            text-align: center;
        }
        @media (max-width: 1100px) {
            .sidebar {
                padding: 20px;
                gap: 14px;
                flex-direction: column;
                align-items: stretch;
                flex-wrap: nowrap;
                border-right: none;
                border-bottom: 1px solid var(--border, #e5e8ed);
                box-shadow: none;
            }
            .sidebar-brand .title {
                font-size: 22px;
            }
            .nav-group {
                padding: 12px;
                grid-template-columns: 1fr;
                width: auto;
            }
            .nav-links {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 8px;
            }
            .nav-link {
                background: #fff;
                border-color: var(--border, #e5e8ed);
            }
        }
        @media (max-width: 540px) {
            .nav-links {
                grid-template-columns: 1fr;
            }
        }
