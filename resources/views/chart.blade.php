@extends('voyager::master')

@section('page_header')
<div class="container-fluid">
    <h1 class="page-title">
        <i class="voyager-pie-chart"></i>Reports
    </h1>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div>
        <div class="row">
        
        <b>Most sold product today : <?php if(count($most_sold_today) > 0) echo($most_sold_today[0]); ?></b>
        </div>
            <div class="row">
                <h2>Product stock</h2>
                <div class="col-12 card">
                    {!! $product_stock->container() !!}
                </div>
            </div>

        <div class="row">
            <div class="col-12">
                <h2>User registeration data</h2>
            </div>
            <div class="col-md-6 col-12 card">
                {!! $users_monthly_chart->container() !!}
            </div>
            <div class="col-md-6 col-12 card">
                {!! $users_daily_chart->container() !!}
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h2>Orders data</h2>
            </div>
            <div class="col-md-6 col-12 card">
                {!! $orders_monthly_chart->container() !!}
            </div>
            <div class="col-md-6 col-12 card">
                {!! $orders_daily_chart->container() !!}
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
{!! $users_monthly_chart->script() !!}
{!! $users_daily_chart->script() !!}
{!! $orders_daily_chart->script() !!}
{!! $orders_monthly_chart->script() !!}
{!! $product_stock->script() !!}
@endsection