<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Config;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\PayWithIyzicoInitialize;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Options;
use Iyzipay\Request\CreatePayWithIyzicoInitializeRequest;

class IyzicoService implements PaymentGatewayInterface
{
    protected $options;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->setApiKey(Config::get('iyzico.api_key'));
        $this->options->setSecretKey(Config::get('iyzico.secret_key'));
        $this->options->setBaseUrl(Config::get('iyzico.base_url'));
    }

    /**
     * Get the Options object for use in other parts of the payment flow.
     *
     * @return Options
     */
    public function getOptions(): Options
    {
        return $this->options;
    }

    /**
     * Initialize the payment process for a given order.
     *
     * @param Order $order
     * @return PayWithIyzicoInitialize
     */
    public function initializePayment(Order $order): PayWithIyzicoInitialize
    {
        $request = new CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("CONVO-{$order->id}-" . uniqid());
        $request->setPrice($order->total_price); // Total price of the basket
        $request->setPaidPrice($order->total_price); // Amount to be paid
        $request->setCurrency(\Iyzipay\Model\Currency::TRY);
        $request->setBasketId((string)$order->id);
        $request->setPaymentGroup(PaymentGroup::PRODUCT);
        $request->setCallbackUrl(route('iyzico.callback'));

        // Set Buyer Information
        $buyer = new Buyer();
        $buyer->setId((string)$order->user->id);
        $buyer->setName($order->user->name);
        $buyer->setSurname($order->user->name); // Assuming full name is in 'name'
        $buyer->setGsmNumber("+905555555555"); // Placeholder
        $buyer->setEmail($order->user->email);
        $buyer->setIdentityNumber("74300864791"); // Placeholder, required by Iyzico
        $buyer->setLastLoginDate((new \DateTime())->format('Y-m-d H:i:s'));
        $buyer->setRegistrationDate($order->user->created_at->format('Y-m-d H:i:s'));
        $buyer->setRegistrationAddress($order->address);
        $buyer->setIp($order->user->last_login_ip ?? request()->ip()); // Get user's IP
        $buyer->setCity("Istanbul"); // Placeholder
        $buyer->setCountry("Turkey"); // Placeholder
        $buyer->setZipCode("34732"); // Placeholder
        $request->setBuyer($buyer);

        // Set Shipping and Billing Addresses
        $shippingAddress = new Address();
        $shippingAddress->setContactName($order->user->name);
        $shippingAddress->setCity("Istanbul"); // Placeholder
        $shippingAddress->setCountry("Turkey"); // Placeholder
        $shippingAddress->setAddress($order->address);
        $shippingAddress->setZipCode("34732"); // Placeholder
        $request->setShippingAddress($shippingAddress);
        $request->setBillingAddress($shippingAddress); // Using same for billing

        // Set Basket Items
        $basketItems = [];
        foreach ($order->items as $orderItem) {
            $item = new BasketItem();
            $item->setId((string)$orderItem->product_id);
            $item->setName($orderItem->product->name);
            $item->setCategory1($orderItem->product->category->name ?? 'Default Category');
            $item->setItemType(BasketItemType::PHYSICAL);
            $item->setPrice($orderItem->price * $orderItem->quantity);
            // For Marketplace, you need subMerchantKey and subMerchantPrice
            // This requires a more complex setup where each seller has a sub-merchant account.
            // For now, we'll proceed with a standard payment flow.
            $basketItems[] = $item;
        }
        $request->setBasketItems($basketItems);

        // Make the API call
        $payWithIyzicoInitialize = PayWithIyzicoInitialize::create($request, $this->options);

        return $payWithIyzicoInitialize;
    }

    /**
     * {@inheritdoc}
     */
    public function initiatePayment(Order $order, array $customerData): array
    {
        try {
            $result = $this->initializePayment($order);
            
            if ($result->getStatus() === 'success') {
                return [
                    'success' => true,
                    'data' => [
                        'payment_page_url' => $result->getPayWithIyzicoPageUrl(),
                        'token' => $result->getToken(),
                    ],
                    'error' => null,
                ];
            }
            
            return [
                'success' => false,
                'data' => null,
                'error' => $result->getErrorMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleCallback(array $request): array
    {
        // Handle Iyzico callback
        $token = $request['token'] ?? null;
        
        if (!$token) {
            return [
                'success' => false,
                'order_id' => null,
                'transaction_id' => null,
                'error' => 'Token not provided',
            ];
        }

        try {
            $retrieveRequest = new \Iyzipay\Request\RetrievePayWithIyzicoRequest();
            $retrieveRequest->setLocale(\Iyzipay\Model\Locale::TR);
            $retrieveRequest->setToken($token);

            $result = \Iyzipay\Model\PayWithIyzico::retrieve($retrieveRequest, $this->options);

            if ($result->getStatus() === 'success') {
                return [
                    'success' => true,
                    'order_id' => (int) $result->getBasketId(),
                    'transaction_id' => $result->getPaymentId(),
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'order_id' => null,
                'transaction_id' => null,
                'error' => $result->getErrorMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'order_id' => null,
                'transaction_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleWebhook(array $request): array
    {
        // Iyzico webhook handling
        return [
            'success' => true,
            'order_id' => (int) ($request['basketId'] ?? 0),
            'status' => $request['status'] ?? 'unknown',
            'error' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refund(string $transactionId, float $amount, ?string $reason = null): array
    {
        try {
            $request = new \Iyzipay\Request\CreateRefundRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId('REFUND-' . uniqid());
            $request->setPaymentTransactionId($transactionId);
            $request->setPrice($amount);
            $request->setIp(request()->ip());

            $refund = \Iyzipay\Model\Refund::create($request, $this->options);

            if ($refund->getStatus() === 'success') {
                return [
                    'success' => true,
                    'refund_id' => $refund->getPaymentId(),
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'refund_id' => null,
                'error' => $refund->getErrorMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'refund_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isTestMode(): bool
    {
        return Config::get('iyzico.base_url') === 'https://sandbox-api.iyzipay.com';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'iyzico';
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigured(): bool
    {
        return !empty(Config::get('iyzico.api_key')) && !empty(Config::get('iyzico.secret_key'));
    }
}
