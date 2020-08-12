<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Facades\Currency;
use Aerni\SnipcartApi\Facades\SnipcartFacade as SnipcartApi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class OrdersRepository
{
    /**
     * Get the full orders API response.
     *
     * @return array
     */
    public function all(): array
    {
        return Cache::remember('snipcart.orders', 60, function () {
            return SnipcartApi::get()->orders()->send();
        });
    }

    /**
     * Get all the orders.
     *
     * @return array
     */
    public function orders(): array
    {
        return $this->all()['items'];
    }

    /**
     * Get the total number of orders.
     *
     * @return array
     */
    public function number(): string
    {
        return $this->all()['totalItems'];
    }

    /**
     * Get a sum of the grand total of all orders.
     *
     * @return string
     */
    public function sales(): string
    {
        $sum = collect($this->orders())->map(function ($item) {
            return $item['finalGrandTotal'];
        })->sum();

        return Currency::symbol() . $sum;
    }

    /**
     * Get the grant total of all orders.
     *
     * @return string
     */
    public function amounts(): array
    {
        return collect($this->orders())->map(function ($item) {
            return $item['finalGrandTotal'];
        })->toArray();
    }

    /**
     * Get all the invoice numbers.
     *
     * @return array
     */
    public function invoices(): array
    {
        return collect($this->orders())->map(function ($item) {
            return $item['invoiceNumber'];
        })->toArray();
    }

    /**
     * Get the dates of all the orders.
     *
     * @return array
     */
    public function dates(): array
    {
        return collect($this->orders())->map(function ($item) {
            return (new Carbon($item['completionDate']))->format('Y-m-d');
        })->toArray();
    }

    /**
     * Get the names of the customers.
     *
     * @return array
     */
    public function customers(): array
    {
        return collect($this->orders())->map(function ($item) {
            return $item['billingAddressName'];
        })->toArray();
    }

    /**
     * Get status of the orders.
     *
     * @return array
     */
    public function status(): array
    {
        return collect($this->orders())->map(function ($item) {
            return $item['status'];
        })->toArray();
    }

    /**
     * Get payment status of the orders.
     *
     * @return array
     */
    public function paymentStatus(): array
    {
        return collect($this->orders())->map(function ($item) {
            return $item['paymentStatus'];
        })->toArray();
    }

    /**
     * Get shipping methods of the orders.
     *
     * @return array
     */
    public function shippingMethods(): array
    {
        return collect($this->orders())->map(function ($item) {
            return $item['shippingMethod'];
        })->toArray();
    }

    public function overview(): array
    {
        return collect($this->number())->map(function ($item, $key) {
            return [
                'invoice' => $this->invoices()[$key],
                'date' => $this->dates()[$key],
                'customer' => $this->customers()[$key],
                'status' => $this->status()[$key],
                'paymentStatus' => $this->paymentStatus()[$key],
                'shippingMethod' => $this->shippingMethods()[$key],
                'amount' => $this->amounts()[$key],
            ];
        })->toArray();
    }
}