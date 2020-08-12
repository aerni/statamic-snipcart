@extends('statamic::layout')

@section('content')

    <div class="mb-3">
        <h1>Overview</h1>
    </div>
    
    <div class="grid grid-cols-2 gap-4">

        <div class="flex flex-col items-center p-4 bg-white rounded-md shadow">
            <span class="text-sm font-bold">Total Sales</span>
            <span class="pt-1 text-2xl">{{ $sales }}</span>
        </div>

        <div class="flex flex-col items-center p-4 bg-white rounded-md shadow">
            <span class="text-sm font-bold">Total Orders</span>
            <span class="pt-1 text-2xl">{{ $number }}</span>
        </div>

    </div>

@endsection