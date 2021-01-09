<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Order;
use App\Charts\UserChart;
use \DB;
use \Carbon\Carbon;

class ChartController extends Controller
{
    public function index()
    {         
		$users_monthly = User::select(\DB::raw("COUNT(*) as count"))
                    ->whereYear('created_at', date('Y'))
                    ->groupBy(\DB::raw("Month(created_at)"))
                    ->pluck('count');
        $users_monthly_chart = new UserChart;
        $users_monthly_chart->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
        $users_monthly_chart->dataset('New User Registeration during this year', 'line', $users_monthly)->options([
            'fill' => 'true',
            'borderColor' => '#51C1C0'
        ]);

        $users_daily = User::select(\DB::raw("COUNT(*) as count"))
                        ->whereMonth('created_at',date('m'))
                        ->groupBy(DB::raw("Day(created_at)"))
                        ->pluck('count');
        $days = array();                
        for($d = 1;$d <= date("d");$d++){
            array_push($days,$d);
        }
        $users_daily_chart = new UserChart();
        $users_daily_chart->labels($days);
        $users_daily_chart->dataset('New user registeration in this month','line',$users_daily)->options([
            'fill' => 'true',
            'borderColor' => '#51C1C0'
        ]);

        $orders_monthly = Order::select(\DB::raw("COUNT(*) as count"))
                    ->whereYear('created_at', date('Y'))
                    ->groupBy(\DB::raw("Month(created_at)"))
                    ->pluck('count');
        $orders_monthly_chart = new UserChart;
        $orders_monthly_chart->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
        $orders_monthly_chart->dataset('Orders received during this year', 'line', $orders_monthly)->options([
            'fill' => true,
            'borderColor' => 'rgba(15,91,255,1)',
            'backgroundColor' => 'rgba(15,91,255,0.7)',
            'offset'=>true
        ]);

        $orders_daily = Order::select(\DB::raw("COUNT(*) as count"))
                        ->whereMonth('created_at',date('m'))
                        ->groupBy(DB::raw("Day(created_at)"))
                        ->pluck('count');
        $orders_daily_chart = new UserChart();
        $orders_daily_chart->labels($days);
        $orders_daily_chart->dataset('Orders received this month','line',$orders_daily)->options([
            'fill' => true,
            'borderColor' => 'rgba(15,91,255,1)',
            'backgroundColor' => 'rgba(15,91,255,0.7)',
            'offset'=>true
        ]);

        $product_stock = new userChart();
        $products = \DB::table('products')->pluck('product');
        $stock = \DB::table('products')->pluck('stock');
        $product_stock->labels($products);
        $product_stock->dataset('Stocks available','bar',$stock)->options([
            'fill' => true,
            'borderColor' => 'rgba(15,91,255,1)',
            'backgroundColor' => 'rgba(15,91,255,0.7)',
            'offset'=>true
        ]);

//most sold monthly
        $most_sold_today = \DB::select('select day(created_at) day,product_id,sum(quantity) total from ordered_products where month(created_at)=month(now()) and year(created_at)=year(now()) and day(created_at)=day(now()) group by day,product_id order by total desc');
        if(count($most_sold_today)>0){
            $most_sold_today = \DB::table('products')->where('id','=',$most_sold_today[0]->product_id)->pluck('product');
        }
        return view('chart', compact('product_stock','users_monthly_chart','users_daily_chart','orders_monthly_chart','orders_daily_chart'))->with('most_sold_today',$most_sold_today);
    }

    public function daily_report(){
        $total_sale = \DB::table('ordered_products')
                    ->leftJoin('products','ordered_products.product_id','=','products.id')
                    ->whereDate('ordered_products.created_at',Carbon::today())
                    ->select('products.id','products.product','products.price','ordered_products.quantity','products.stock','ordered_products.created_at',DB::raw('products.price * ordered_products.quantity as total'))
                    ->get();
        $total_buisness_amount = $total_sale->pluck('total')->toArray();
        $orders_status = DB::table('orders')
                            ->whereIn('status',['Pending','Processing','Completed'])
                            ->whereDate('created_at',Carbon::today())
                            ->select(DB::raw('count(orders.id) as total,status'))
                            ->groupBy('status')
                            ->get();
        return view('daily_report',[
            'sales'=>$total_sale,
            'total_buisness_amount'=>array_sum($total_buisness_amount),
            'orders_status'=>$orders_status
            ]);
    }
}
