@extends('statamic::layout')
@section('title', __('Orders'))
@section('wrapper_class', 'max-w-full')

@section('content')

    <div class="mb-3">
        <h1>Orders</h1>
    </div>

    <div class="flex flex-col">
        <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-md">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-3 py-2 text-sm font-bold leading-4 text-left text-gray-500">
                                Invoice
                            </th>
                            <th class="px-3 py-2 text-sm font-bold leading-4 text-left text-gray-500">
                                Date
                            </th>
                            <th class="px-3 py-2 text-sm font-bold leading-4 text-left text-gray-500">
                                Customer
                            </th>
                            <th class="px-3 py-2 text-sm font-bold leading-4 text-left text-gray-500">
                                Status
                            </th>
                            <th class="px-3 py-2 text-sm font-bold leading-4 text-left text-gray-500">
                                Payment Status
                            </th>
                            <th class="px-3 py-2 text-sm font-bold leading-4 text-left text-gray-500">
                                Shipping Method
                            </th>
                            <th class="px-3 py-2 text-sm font-bold leading-4 text-left text-gray-500">
                                Amount
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                    {{ $order['invoice'] }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                                    {{ $order['date'] }}    
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                                    {{ $order['customer'] }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                                    {{ $order['status'] }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                                    {{ $order['paymentStatus'] }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                                    {{ $order['shippingMethod'] }}
                                </td>
                                <td class="px-3 py-2 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                                    {{ $order['amount'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection