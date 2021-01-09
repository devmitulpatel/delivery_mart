@extends('voyager::master')

@section('content')
<h1>Daily Report</h1>
<div class="card margin padding">
    
    <h4>Total Buisness Amount: Rs {{$total_buisness_amount}}</h4>
    <h4>Number of Pending orders:</h4>
    <table class="table">
        <thead>
            <td>
                <b>Status</b>
            </td>
            <td>
               <b>Count</b>
            </td>
        </thead>
        @foreach($orders_status as $status)
        <tr>
            <td>
                {{$status->status}}
            </td>
            <td>
                {{$status->total}}
            </td>
        </tr>
        @endforeach
    </table>
    
</div>
<div class="card margin padding">
    <h3>Number of items sold today:</h3>
    <table class="table">
        <thead>
            <td>
                Item
            </td>
            <td>
                Quantity
            </td>
        </thead>
        @foreach($sales as $sale)
        <tr>
            <td>
                {{$sale->product}}
            </td>
            <td>
                {{$sale->quantity}}
            </td>
        </tr>
        @endforeach
    </table>
</div>
<div class="card margin padding">
    <h3>Remaining Stock for items sold today:</h3>
    <table class="table">
        <thead>
            <td>
                Item
            </td>
            <td>
                Remaining Stock
            </td>
            <td>
                Time
            </td>
        </thead>
        @foreach($sales as $sale)
        <tr>
            <td>
                {{$sale->product}}
            </td>
            <td>
                {{$sale->stock}}
            </td>
            <td>
                {{$sale->created_at}}
            </td>
        </tr>
        @endforeach
    </table>
</div>
<style>
    .margin{
        margin:20px;
    }
    .padding{
        padding:30px 40px;
    }
</style>
@endsection