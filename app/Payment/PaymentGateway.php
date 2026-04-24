<?php

namespace App\Payment;

use Core\Config;
use Core\Helper;

class PaymentGateway {
    
    public static function process($method, $amount, $orderData = []) {
        return match($method) {
            'wallet' => self::processWalletPayment($amount, $orderData),
            'visa' => self::processVisaPayment($amount, $orderData),
            'paypal' => self::processPayPalPayment($amount, $orderData),
            'brimoob' => self::processBrimoobPayment($amount, $orderData),
            default => ['success' => false, 'message' => 'طريقة الدفع غير مدعومة']
        };
    }

    private static function processWalletPayment($amount, $orderData) {
        $userId = $orderData['user_id'] ?? 0;
        
        if ($userId <= 0) {
            return ['success' => false, 'message' => 'يرجى تسجيل الدخول أولاً'];
        }

        $balance = \Core\Wallet::getBalance($userId);

        if ($balance < $amount) {
            return [
                'success' => false,
                'message' => 'الرصيد غير كافٍ. رصيدك: ' . number_format($balance, 2) . ' ر.س'
            ];
        }

        $result = \Core\Wallet::deductFunds($userId, $amount, 'دفع طلب');

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'تم الدفع بنجاح من المحفظة',
                'transaction_id' => 'WALLET-' . time() . '-' . $userId
            ];
        }

        return ['success' => false, 'message' => $result['message'] ?? 'فشل في الدفع'];
    }

    private static function processVisaPayment($amount, $orderData) {
        try {
            $cardNumber = $orderData['card_number'] ?? '';
            $expiry = $orderData['card_expiry'] ?? '';
            $cvc = $orderData['card_cvc'] ?? '';
            $cardHolder = $orderData['card_holder'] ?? '';

            if (empty($cardNumber) || empty($expiry) || empty($cvc)) {
                return ['success' => false, 'message' => 'يرجى إدخال بيانات البطاقة كاملة'];
            }

            $cardNumber = str_replace(' ', '', $cardNumber);
            
            if (!self::validateCard($cardNumber, $expiry, $cvc)) {
                return ['success' => false, 'message' => 'بيانات البطاقة غير صحيحة'];
            }

            return [
                'success' => true,
                'message' => 'تم الدفع عبر البطاقة بنجاح',
                'transaction_id' => 'VISA-' . time() . '-' . substr($cardNumber, -4),
                'card_last4' => substr($cardNumber, -4)
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'خطأ في معالجة الدفع'];
        }
    }

    private static function processPayPalPayment($amount, $orderData) {
        $paypalEmail = $orderData['paypal_email'] ?? '';
        
        if (empty($paypalEmail)) {
            return [
                'success' => false,
                'message' => 'يرجى إدخال بريد PayPal'
            ];
        }

        if (!filter_var($paypalEmail, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'بريد PayPal غير صحيح'
            ];
        }

        return [
            'success' => true,
            'message' => "تم إنشاء طلب PayPal - سيتم الدفع خلال دقائق",
            'transaction_id' => 'PAYPAL-' . time(),
            'pending' => true,
            'paypal_email' => $paypalEmail,
            'amount_usd' => round($amount / 3.75, 2)
        ];
    }

    private static function processBrimoobPayment($amount, $orderData) {
        $phone = $orderData['brimoob_phone'] ?? '';
        
        if (empty($phone)) {
            return [
                'success' => false,
                'message' => 'يرجى إدخال رقم الهاتف'
            ];
        }

        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) < 9 || strlen($phone) > 12) {
            return [
                'success' => false,
                'message' => 'رقم الهاتف غير صحيح'
            ];
        }

        return [
            'success' => true,
            'message' => "تم إرسال طلب Brimoob - أكد الدفع من تطبيق Brimoob",
            'transaction_id' => 'BRIMOOB-' . time(),
            'pending' => true,
            'phone' => $phone
        ];
    }

    private static function processCryptoPayment($amount, $orderData) {
        $cryptoType = $orderData['crypto_type'] ?? 'BTC';
        $walletAddress = $orderData['wallet_address'] ?? '';

        if (empty($walletAddress)) {
            return [
                'success' => false,
                'message' => 'يرجى إدخال عنوان المحفظة',
                'pending' => true
            ];
        }

        $conversionRates = [
            'BTC' => 0.000025,
            'ETH' => 0.0004,
            'USDT' => 1
        ];

        $cryptoAmount = $amount * ($conversionRates[$cryptoType] ?? 1);

        return [
            'success' => true,
            'message' => "تم إنشاء طلب الدفع - أرسل {$cryptoAmount} {$cryptoType} إلى العنوان المتوقع",
            'transaction_id' => 'CRYPTO-' . time(),
            'crypto_amount' => $cryptoAmount,
            'crypto_type' => $cryptoType,
            'pending' => true
        ];
    }

    private static function processBankTransfer($amount, $orderData) {
        return [
            'success' => true,
            'message' => 'تم إنشاء طلب التحويل البنكي. سيتم التحويل خلال 24 ساعة.',
            'transaction_id' => 'BANK-' . time(),
            'pending' => true,
            'instructions' => [
                'bank_name' => 'البنك الأهلي',
                'account_number' => '1234567890',
                'iban' => 'SA12 3456 7890 1234 5678 9012'
            ]
        ];
    }

    private static function createPendingPayment($method, $amount, $orderData) {
        return [
            'success' => true,
            'message' => 'سيتم معالجة الدفع قريباً',
            'transaction_id' => strtoupper($method) . '-' . time(),
            'pending' => true
        ];
    }

    public static function validateCard($number, $expiry, $cvc) {
        $number = preg_replace('/\D/', '', $number);
        
        if (strlen($number) < 13 || strlen($number) > 19) {
            return false;
        }

        $sum = 0;
        $isAlternate = false;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int)$number[$i];

            if ($isAlternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $isAlternate = !$isAlternate;
        }

        if ($sum % 10 !== 0) {
            return false;
        }

        $expParts = explode('/', $expiry);
        if (count($expParts) !== 2) {
            return false;
        }

        $month = (int)$expParts[0];
        $year = (int)('20' . $expParts[1]);

        if ($month < 1 || $month > 12) {
            return false;
        }

        if ($year < date('Y') || ($year == date('Y') && $month < date('n'))) {
            return false;
        }

        if (strlen($cvc) < 3 || strlen($cvc) > 4) {
            return false;
        }

        return true;
    }

    public static function getSupportedMethods() {
        return [
            'wallet' => [
                'name' => 'المحفظة',
                'icon' => 'bi-wallet2',
                'color' => '#667eea'
            ],
            'visa' => [
                'name' => 'Visa / Mastercard',
                'icon' => 'bi-credit-card',
                'color' => '#1a1f71'
            ],
            'paypal' => [
                'name' => 'PayPal',
                'icon' => 'bi-paypal',
                'color' => '#003087'
            ],
            'brimoob' => [
                'name' => 'Brimoob',
                'icon' => 'bi-phone',
                'color' => '#e91e63'
            ]
        ];
    }
}
