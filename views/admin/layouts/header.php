<?php
use Core\Auth;
use Core\Config;

$isAdmin = Auth::isAdmin();
$user = Auth::user();
$currentPage = basename($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 270px;
            --primary: #8b5cf6;
            --primary-dark: #7c3aed;
            --secondary: #a78bfa;
            --accent: #c4b5fd;
            --dark: #1e1b4b;
            --gray-50: #faf5ff;
            --gray-100: #f3e8ff;
            --gray-200: #e9d5ff;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * { font-family: 'Cairo', sans-serif; }

        body {
            background: var(--gray-50);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--dark) 0%, #312e81 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: -4px 0 30px rgba(139, 92, 246, 0.2);
        }

        .sidebar-header {
            padding: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h4 {
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .sidebar-header h4 i {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 14px 25px;
            border-radius: 12px;
            margin: 4px 15px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(-5px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        }

        .sidebar .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-left: 12px;
        }

        .sidebar .badge {
            margin-right: auto;
            background: var(--danger);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .main-content {
            margin-right: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-bar {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h2 {
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .page-content {
            padding: 30px;
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .table {
            margin: 0;
            border-radius: 20px;
            overflow: hidden;
        }

        .table thead {
            background: var(--gray-100);
        }

        .table th {
            font-weight: 700;
            color: var(--dark);
            padding: 18px 20px;
            border: none;
        }

        .table td {
            padding: 18px 20px;
            vertical-align: middle;
            border-color: var(--gray-200);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        .form-control, .form-select {
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 12px 18px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        }

        .modal-content {
            border: none;
            border-radius: 20px;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 20px 20px 0 0;
        }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-right: 0; }
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-speedometer2"></i> لوحة التحكم</h4>
            <small class="text-white-50"><?= Config::get('app.name', 'المتجر الرقمي') ?></small>
        </div>

        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="/admin" class="nav-link <?= $currentPage === '/admin' || $currentPage === 'admin' ? 'active' : '' ?>">
                        <i class="bi bi-house"></i> الرئيسية
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/orders" class="nav-link <?= strpos($currentPage, 'orders') !== false ? 'active' : '' ?>">
                        <i class="bi bi-bag"></i> الطلبات
                        <?php
                        $pendingCount = \App\Models\Order::countByStatus('pending');
                        if ($pendingCount > 0): ?>
                            <span class="badge"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/products" class="nav-link <?= strpos($currentPage, 'products') !== false ? 'active' : '' ?>">
                        <i class="bi bi-box-seam"></i> المنتجات
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/categories" class="nav-link <?= strpos($currentPage, 'categories') !== false ? 'active' : '' ?>">
                        <i class="bi bi-folder"></i> التصنيفات
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/types" class="nav-link <?= strpos($currentPage, 'types') !== false ? 'active' : '' ?>">
                        <i class="bi bi-tags"></i> أنواع المنتجات
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/wallet-requests" class="nav-link <?= strpos($currentPage, 'wallet') !== false ? 'active' : '' ?>">
                        <i class="bi bi-wallet2"></i> طلبات الشحن
                        <?php
                        $walletPending = count(\App\Models\Wallet::getPendingRequests());
                        if ($walletPending > 0): ?>
                            <span class="badge bg-warning text-dark"><?= $walletPending ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/users" class="nav-link <?= strpos($currentPage, 'users') !== false ? 'active' : '' ?>">
                        <i class="bi bi-people"></i> المستخدمين
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/settings" class="nav-link <?= strpos($currentPage, 'settings') !== false ? 'active' : '' ?>">
                        <i class="bi bi-gear"></i> الإعدادات
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer p-3 border-top border-secondary">
            <a href="/" class="nav-link" target="_blank">
                <i class="bi bi-box-arrow-up-left"></i> عرض الموقع
            </a>
            <a href="/auth/logout" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
            </a>
        </div>
    </nav>

    <div class="main-content">
        <div class="top-bar">
            <h2><?= isset($pageTitle) ? $pageTitle : 'لوحة التحكم' ?></h2>
            <div class="user-info">
                <div>
                    <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                    <small class="text-muted">مدير النظام</small>
                </div>
                <div class="user-avatar"><?= mb_substr($user['name'], 0, 1) ?></div>
            </div>
        </div>
        <div class="page-content">
