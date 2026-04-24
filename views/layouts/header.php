<?php
use Core\Auth;
use Core\Helper;
use Core\Notification;
use Core\Config;

$isLoggedIn = Auth::isLoggedIn();
$isAdmin = Auth::isAdmin();
$user = Auth::user();
$unreadCount = $isLoggedIn ? Notification::getUnreadCount($user['id'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= Config::get('app.name', 'المتجر الرقمي') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/custom.css">
    <style>
        :root {
            --primary: #8b5cf6;
            --primary-dark: #7c3aed;
            --secondary: #a78bfa;
            --accent: #c4b5fd;
            --dark: #1e1b4b;
            --gray-50: #faf5ff;
            --gray-100: #f3e8ff;
            --gray-200: #e9d5ff;
            --gray-300: #d8b4fe;
            --gray-400: #c084fc;
            --gray-500: #a855f7;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --white: #ffffff;
        }

        * {
            font-family: 'Cairo', 'Tajawal', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            color: var(--dark);
        }

        /* === Modern Header === */
        .navbar {
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary-dark) 50%, var(--primary) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(139, 92, 246, 0.3);
            padding: 12px 0;
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(167, 139, 250, 0.2));
            pointer-events: none;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--white) !important;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand i {
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-weight: 600;
            font-size: 1rem;
            padding: 10px 18px !important;
            border-radius: 10px;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .nav-link:hover {
            color: var(--white) !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-link i {
            transition: transform 0.3s ease;
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        /* Dropdown */
        .dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            padding: 12px;
            background: var(--white);
            backdrop-filter: blur(20px);
        }

        .dropdown-item {
            border-radius: 10px;
            padding: 12px 16px;
            font-weight: 600;
            color: var(--dark);
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary);
            transform: translateX(-5px);
        }

        /* Navbar Buttons */
        .nav-item .btn {
            border-radius: 25px;
            padding: 10px 24px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .nav-item .btn-light {
            background: var(--white);
            color: var(--primary);
        }

        .nav-item .btn-light:hover {
            background: var(--gray-100);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .nav-item .nav-link:not(.btn) {
            margin-right: 5px;
        }

        /* Cart & Notification Icons */
        .icon-link {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            color: var(--white) !important;
            transition: all 0.3s ease;
            position: relative;
        }

        .icon-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        .icon-link .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--danger);
            color: white;
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 20px;
            font-weight: 700;
            min-width: 20px;
        }

        /* User Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* === Hero Section === */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
            min-height: 500px;
            display: flex;
            align-items: center;
        }

        .hero-bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
        }

        .hero-section .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .hero-section .shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            right: -50px;
            animation: pulse-slow 4s ease-in-out infinite;
        }

        .hero-section .shape-2 {
            width: 200px;
            height: 200px;
            bottom: -50px;
            left: 10%;
            animation: pulse-slow 4s ease-in-out infinite 1s;
        }

        .hero-section .shape-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            right: 30%;
            animation: pulse-slow 4s ease-in-out infinite 2s;
        }

        @keyframes pulse-slow {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        .hero-section .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 50px;
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
            animation: slideDown 0.6s ease;
        }

        .hero-badge i {
            color: #ffd700;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 20px;
            animation: slideDown 0.6s ease 0.1s both;
            line-height: 1.3;
        }

        .hero-section .text-gradient {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-section .hero-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
            animation: slideDown 0.6s ease 0.2s both;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
            animation: slideDown 0.6s ease 0.3s both;
        }

        .hero-section .btn-cta {
            background: white;
            color: #667eea;
            padding: 15px 35px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.05rem;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .hero-section .btn-cta:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            background: white;
            color: #764ba2;
        }

        .hero-section .btn-outline {
            background: transparent;
            color: white;
            padding: 15px 35px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            border: 2px solid rgba(255, 255, 255, 0.5);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .hero-section .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-3px);
            color: white;
        }

        .hero-section .hero-image {
            position: relative;
            z-index: 2;
        }

        .hero-img-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            animation: zoomIn 1s ease 0.3s both;
        }

        .hero-img-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3));
            z-index: 1;
        }

        .hero-section .hero-image img {
            width: 100%;
            display: block;
            transition: transform 0.5s ease;
        }

        .hero-img-wrapper:hover img {
            transform: scale(1.05);
        }

        .hero-section .floating-card {
            position: absolute;
            background: white;
            padding: 15px 20px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        .hero-section .floating-card.card-1 {
            top: 15%;
            right: -30px;
            animation-delay: 0.8s;
        }

        .hero-section .floating-card.card-2 {
            bottom: 20%;
            left: -30px;
            animation-delay: 1s;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 991px) {
            .hero-section {
                text-align: center;
                padding: 100px 0 60px;
            }
            .hero-section h1 {
                font-size: 2.2rem;
            }
            .hero-section .hero-subtitle {
                font-size: 1rem;
            }
            .hero-buttons {
                justify-content: center;
                flex-wrap: wrap;
            }
            .hero-stats {
                justify-content: center;
                gap: 20px;
            }
            .hero-section .stat-number {
                font-size: 1.5rem;
            }
            .hero-section .hero-image {
                margin-top: 40px;
            }
            .hero-section .floating-card {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .hero-section h1 {
                font-size: 1.8rem;
            }
            .hero-buttons {
                flex-direction: column;
            }
            .hero-section .btn-cta,
            .hero-section .btn-outline {
                width: 100%;
                justify-content: center;
            }
            .hero-stats {
                flex-wrap: wrap;
            }
        }

        /* Hero Search */
        .hero-search {
            margin-bottom: 25px;
            animation: slideDown 0.6s ease 0.35s both;
        }

        .hero-search .input-group {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 5px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero-search .form-control {
            background: transparent;
            border: none;
            color: white;
            padding: 12px 18px;
            font-size: 1rem;
        }

        .hero-search .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .hero-search .form-control:focus {
            box-shadow: none;
            background: transparent;
        }

        .hero-search .btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .hero-search .btn:hover {
            background: #f0f0f0;
            color: #764ba2;
        }


        }

        /* === Modern Cards === */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            background: var(--white);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        /* Category Cards */
        .category-card {
            border-radius: 20px;
            padding: 50px 35px;
            text-align: center;
            background: white;
            transition: all 0.3s ease;
            border: 2px solid var(--gray-200);
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .category-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.15);
        }

        .category-card i {
            font-size: 4rem;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .category-card:hover i {
            transform: scale(1.15);
        }

        .category-card h5 {
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 12px;
        }

        .category-card p {
            font-size: 1rem;
            color: var(--gray-500);
        }

        /* Product Cards */
        .product-card {
            border-radius: 20px;
            overflow: hidden;
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .product-card .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: all 0.4s ease;
        }

        .product-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .product-card .card-body {
            padding: 20px;
        }

        .product-card .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1a1a2e;
        }

        .product-card .card-text {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .product-card .product-footer {
            padding: 15px 20px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-card .price-tag {
            font-size: 1.3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .product-card .btn-group-custom {
            display: flex;
            gap: 8px;
        }

        .product-card .btn-action {
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .product-card .btn-buy {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
        }

        .product-card .btn-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .product-card .btn-view {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .product-card .btn-view:hover {
            background: #667eea;
            color: white;
        }

        /* Modern Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        /* Section Titles */
        .section-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 30px;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            right: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }

        .section-title i {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            padding: 60px 0 30px;
            margin-top: 80px;
        }

        .footer h5, .footer h6 {
            color: var(--white);
            font-weight: 700;
        }

        .footer a {
            color: var(--gray-400);
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: var(--accent);
            padding-right: 5px;
        }

        .social-links a {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-5px);
        }

        /* Alert Messages */
        .alert {
            border-radius: 16px;
            border: none;
            padding: 16px 24px;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            color: var(--success);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            color: var(--danger);
        }

        .alert-warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
            color: var(--warning);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }
            
            .hero-section .lead {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-shop"></i> <?= Config::get('app.name', 'المتجر الرقمي') ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/"><i class="bi bi-house me-1"></i> الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products"><i class="bi bi-grid-3x3-gap me-1"></i> المنتجات</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-folder me-1"></i> التصنيفات
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/products?type=code"><i class="bi bi-key me-2"></i> أكواد</a></li>
                            <li><a class="dropdown-item" href="/products?type=subscription"><i class="bi bi-repeat me-2"></i> اشتراكات</a></li>
                            <li><a class="dropdown-item" href="/products?type=game_id"><i class="bi bi-controller me-2"></i> أرقام ألعاب</a></li>
                            <li><a class="dropdown-item" href="/products?type=credit"><i class="bi bi-phone me-2"></i> رصيد</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav align-items-center gap-2">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="icon-link" href="/cart">
                                <i class="bi bi-cart3 fs-5"></i>
                                <span class="badge" id="cartBadge"><?= count($_SESSION['cart'] ?? []) ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="icon-link" href="/notifications">
                                <i class="bi bi-bell fs-5"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="badge"><?= $unreadCount ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                                <div class="user-avatar"><?= mb_substr($user['name'], 0, 1) ?></div>
                                <span class="d-none d-md-inline"><?= htmlspecialchars($user['name']) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($isAdmin): ?>
                                    <li><a class="dropdown-item" href="/admin"><i class="bi bi-speedometer2 me-2"></i> لوحة التحكم</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="/orders"><i class="bi bi-bag me-2"></i> مشترياتي</a></li>
                                <li><a class="dropdown-item" href="/wallet"><i class="bi bi-wallet2 me-2"></i> المحفظة</a></li>
                                <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i> الملف الشخصي</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/auth/logout"><i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth/login"><i class="bi bi-box-arrow-in-right me-1"></i> تسجيل الدخول</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light" href="/auth/register"><i class="bi bi-person-plus me-1"></i> إنشاء حساب</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (\Core\Session::hasFlash('success')): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> <?= \Core\Session::getFlash('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (\Core\Session::hasFlash('error')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i> <?= \Core\Session::getFlash('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (\Core\Session::hasFlash('warning')): ?>
        <div class="container mt-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i> <?= \Core\Session::getFlash('warning') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
