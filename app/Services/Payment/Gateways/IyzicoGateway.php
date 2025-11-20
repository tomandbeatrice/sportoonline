<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\AbstractPaymentGateway;

class IyzicoGateway extends AbstractPaymentGateway
{
    /**
     * Validate gateway configuration
     */
    public function isConfigured(): bool
    {
        return !empty($this->getCredential('api_key')) &&
               !empty($this->getCredential('secret_key'));
    }

    /**
     * Initialize payment and get payment form/redirect URL
     */
    public function initiatePayment(Order $order, array $customerData): array
    {
        try {
            if (!$this->isConfigured()) {
                return $this->errorResponse('Iyzico gateway is not configured properly');
            }

            // Initialize Iyzico options
            $options = new \Iyzipay\Options();
            $options->setApiKey($this->getCredential('api_key'));
            $options->setSecretKey($this->getCredential('secret_key'));
            $options->setBaseUrl($this->isTestMode() 
                ? 'https://sandbox-api.iyzipay.com' 
                : 'https://api.iyzipay.com');

            // Create payment request
            $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId($this->generateConversationId($order->id));
            $request->setPrice($this->formatAmount($order->total));
            $request->setPaidPrice($this->formatAmount($order->total));
            $request->setCurrency(\Iyzipay\Model\Currency::TL);
            $request->setBasketId($order->id);
            $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
            
            // Set callback URL
            $request->setCallbackUrl(route('payment.callback.iyzico'));
            
            // Set customer information
            $buyer = new \Iyzipay\Model\Buyer();
            $buyer->setId($order->user_id);
            $buyer->setName($customerData['first_name'] ?? $order->user->name);
            $buyer->setSurname($customerData['last_name'] ?? '');
            $buyer->setEmail($customerData['email'] ?? $order->user->email);
            $buyer->setIdentityNumber($customerData['identity_number'] ?? '11111111111');
            $buyer->setRegistrationAddress($customerData['address'] ?? $order->shipping_address);
            $buyer->setCity($customerData['city'] ?? 'Istanbul');
            $buyer->setCountry($customerData['country'] ?? 'Turkey');
            $buyer->setZipCode($customerData['zip_code'] ?? '34000');
            $buyer->setIp($customerData['ip'] ?? request()->ip());
            $request->setBuyer($buyer);

            // Set shipping address
            $shippingAddress = new \Iyzipay\Model\Address();
            $shippingAddress->setContactName($order->user->name);
            $shippingAddress->setCity($customerData['city'] ?? 'Istanbul');
            $shippingAddress->setCountry($customerData['country'] ?? 'Turkey');
            $shippingAddress->setAddress($order->shipping_address);
            $shippingAddress->setZipCode($customerData['zip_code'] ?? '34000');
            $request->setShippingAddress($shippingAddress);

            // Set billing address
            $billingAddress = new \Iyzipay\Model\Address();
            $billingAddress->setContactName($order->user->name);
            $billingAddress->setCity($customerData['city'] ?? 'Istanbul');
            $billingAddress->setCountry($customerData['country'] ?? 'Turkey');
            $billingAddress->setAddress($order->billing_address ?? $order->shipping_address);
            $billingAddress->setZipCode($customerData['zip_code'] ?? '34000');
            $request->setBillingAddress($billingAddress);

            // Set basket items
            $basketItems = [];
            foreach ($order->items as $item) {
                $basketItem = new \Iyzipay\Model\BasketItem();
                $basketItem->setId($item->product_id);
                $basketItem->setName($item->product_name);
                $basketItem->setCategory1($item->product->category->name ?? 'Genel');
                $basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                $basketItem->setPrice($this->formatAmount($item->total_price));
                $basketItems[] = $basketItem;
            }
            $request->setBasketItems($basketItems);

            // Make API request
            $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);

            $this->logActivity('initiate_payment', [
                'order_id' => $order->id,
                'status' => $checkoutFormInitialize->getStatus(),
                'token' => $checkoutFormInitialize->getToken(),
            ]);

            if ($checkoutFormInitialize->getStatus() === 'success') {
                return $this->successResponse([
                    'data' => [
                        'payment_page_url' => $checkoutFormInitialize->getPaymentPageUrl(),
                        'token' => $checkoutFormInitialize->getToken(),
                        'conversation_id' => $request->getConversationId(),
                    ],
                ]);
            }

            return $this->errorResponse(
                $checkoutFormInitialize->getErrorMessage() ?? 'Payment initialization failed',
                ['error_code' => $checkoutFormInitialize->getErrorCode()]
            );

        } catch (\Exception $e) {
            $this->logActivity('initiate_payment_error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ], 'error');

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Handle payment callback from gateway
     */
    public function handleCallback(array $request): array
    {
        try {
            $token = $request['token'] ?? null;
            
            if (!$token) {
                return $this->errorResponse('Invalid callback: missing token');
            }

            // Initialize Iyzico options
            $options = new \Iyzipay\Options();
            $options->setApiKey($this->getCredential('api_key'));
            $options->setSecretKey($this->getCredential('secret_key'));
            $options->setBaseUrl($this->isTestMode() 
                ? 'https://sandbox-api.iyzipay.com' 
                : 'https://api.iyzipay.com');

            // Retrieve checkout form result
            $retrieveRequest = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
            $retrieveRequest->setLocale(\Iyzipay\Model\Locale::TR);
            $retrieveRequest->setToken($token);

            $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($retrieveRequest, $options);

            $this->logActivity('handle_callback', [
                'token' => $token,
                'status' => $checkoutForm->getStatus(),
                'payment_status' => $checkoutForm->getPaymentStatus(),
            ]);

            if ($checkoutForm->getStatus() === 'success' && $checkoutForm->getPaymentStatus() === 'SUCCESS') {
                return $this->successResponse([
                    'order_id' => (int) $checkoutForm->getBasketId(),
                    'transaction_id' => $checkoutForm->getPaymentId(),
                    'data' => [
                        'conversation_id' => $checkoutForm->getConversationId(),
                        'paid_price' => $checkoutForm->getPaidPrice(),
                        'currency' => $checkoutForm->getCurrency(),
                    ],
                ]);
            }

            return $this->errorResponse(
                $checkoutForm->getErrorMessage() ?? 'Payment failed',
                [
                    'error_code' => $checkoutForm->getErrorCode(),
                    'order_id' => (int) $checkoutForm->getBasketId(),
                ]
            );

        } catch (\Exception $e) {
            $this->logActivity('handle_callback_error', [
                'error' => $e->getMessage(),
            ], 'error');

            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Handle webhook notification from gateway
     */
    public function handleWebhook(array $request): array
    {
        // Iyzico doesn't use webhooks, all handled via callback
        return $this->handleCallback($request);
    }

    /**
     * Refund payment
     */
    public function refund(string $transactionId, float $amount, ?string $reason = null): array
    {
        try {
            if (!$this->isConfigured()) {
                return $this->errorResponse('Iyzico gateway is not configured properly');
            }

            // Initialize Iyzico options
            $options = new \Iyzipay\Options();
            $options->setApiKey($this->getCredential('api_key'));
            $options->setSecretKey($this->getCredential('secret_key'));
            $options->setBaseUrl($this->isTestMode() 
                ? 'https://sandbox-api.iyzipay.com' 
                : 'https://api.iyzipay.com');

            // Create refund request
            $request = new \Iyzipay\Request\CreateRefundRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId('REFUND-' . time());
            $request->setPaymentTransactionId($transactionId);
            $request->setPrice($this->formatAmount($amount));
            $request->setCurrency(\Iyzipay\Model\Currency::TL);
            $request->setIp(request()->ip());

            // Make API request
            $refund = \Iyzipay\Model\Refund::create($request, $options);

            $this->logActivity('refund', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'status' => $refund->getStatus(),
            ]);

            if ($refund->getStatus() === 'success') {
                return $this->successResponse([
                    'refund_id' => $refund->getPaymentId(),
                ]);
            }

            return $this->errorResponse(
                $refund->getErrorMessage() ?? 'Refund failed',
                ['error_code' => $refund->getErrorCode()]
            );

        } catch (\Exception $e) {
            $this->logActivity('refund_error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ], 'error');

            return $this->errorResponse($e->getMessage());
        }
    }
}
