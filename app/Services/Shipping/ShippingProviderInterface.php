<?php

namespace App\Services\Shipping;

interface ShippingProviderInterface
{
    /**
     * Get shipping quote/price
     *
     * @param array $shipmentData
     * @return array
     */
    public function getQuote(array $shipmentData): array;

    /**
     * Create a shipment
     *
     * @param array $shipmentData
     * @return array
     */
    public function createShipment(array $shipmentData): array;

    /**
     * Track a shipment
     *
     * @param string $trackingCode
     * @return array
     */
    public function trackShipment(string $trackingCode): array;

    /**
     * Cancel a shipment
     *
     * @param string $trackingCode
     * @return array
     */
    public function cancelShipment(string $trackingCode): array;
}
