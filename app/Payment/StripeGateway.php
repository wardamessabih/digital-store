<?php

namespace App\Payment;

use Core\Config;
use Core\Database;

class StripeGateway {
    private $apiKey;
    private $publicKey;

    public function __construct() {
        $this->publicKey = Config::get('payment.stripe_key', '');
        $this->apiKey = Config::get('payment.stripe_secret', '');
    }

    public function isConfigured() {
        return !empty($this->apiKey) && !empty($this->publicKey);
    }

    public function getPublicKey() {
        return $this->publicKey;
    }

    public function createCheckoutSession($orderId, $amount, $currency = 'sar') {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Stripe غير مهيأ'];
        }

        $db = Database::getInstance();
        $order = $db->fetch('SELECT * FROM orders WHERE id = ?', [$orderId]);

        if (!$order) {
            return ['success' => false, 'message' => 'الطلب غير موجود'];
        }

        $payload = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'product_data' => [
                        'name' => $order['product_name'] ?? 'طلب #' . $orderId
                    ],
                    'unit_amount' => (int)($amount * 100)
                ],
                'quantity' => $order['quantity']
            ]],
            'mode' => 'payment',
            'success_url' => Config::get('app.url') . '/payment/success?session_id={CHECKOUT_SESSION_ID}&order_id=' . $orderId,
            'cancel_url' => Config::get('app.url') . '/payment/cancel?order_id=' . $orderId,
            'metadata' => [
                'order_id' => $orderId
            ]
        ];

        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode === 200 && isset($result['id'])) {
            return [
                'success' => true,
                'session_id' => $result['id'],
                'url' => $result['url']
            ];
        }

        return [
            'success' => false,
            'message' => $result['error']['message'] ?? 'فشل في إنشاء جلسة الدفع'
        ];
    }

    public function verifyPayment($sessionId) {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Stripe غير مهيأ'];
        }

        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->apiKey],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['payment_status']) && $result['payment_status'] === 'paid') {
            return [
                'success' => true,
                'order_id' => $result['metadata']['order_id'] ?? null,
                'amount' => $result['amount_total'] / 100
            ];
        }

        return ['success' => false, 'message' => 'الدفع غير مكتمل'];
    }
}
