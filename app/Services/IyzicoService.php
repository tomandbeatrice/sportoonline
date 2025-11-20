<?php

namespace App\Services;

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

class IyzicoService
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
}
