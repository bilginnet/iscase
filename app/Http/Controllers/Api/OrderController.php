<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\Validation\Validator as ValidatorContracts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $orders = Order::query()
            ->with('items')
            ->where('user_id', request()->user()->id)
            ->orderBy('id', 'DESC')
            ->paginate(24);

        if (! $orders) {
            return response()->json([
                'status' => 'fails',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status'=> 'ok',
            'data' => $orders
        ]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fails',
                'errors' => $validator->getMessageBag(),
            ], 400);
        }

        DB::beginTransaction();

        try {

            $items = [];
            $total = 0;

            foreach ($request->items as $item) {

                $product = Product::find($item['product_id']);

                // Ürün stoğu yetersiz ise
                if ($product->quantity <= $item['quantity']) {
                    return response()->json([
                        'status' => 'fails',
                        'data' => ['message' => $product->name . ' stoğu yetersizdir.'],
                    ], 400);
                }

                $item['unitPrice'] = $product->price;

                $item['total'] = $item['quantity'] * $item['unitPrice'];

                $total += $item['total'];

                $items[] = new OrderItem($item);
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => $total
            ]);

            $order->items()->saveMany($items);

            DB::commit();

            return response()->json([
                'status' => 'ok',
                'data' => $order
            ]);

        } catch (\Exception $exception) {
            DB::rollback();

            return response()->json([
                'status' => 'fails',
                'data' => ['message' =>  $exception->getMessage()]
            ], 500);
        }
    }

    public function discount(int $id)
    {
        $order = Order::find($id);

        if (! $order) {
            return response()->json([
                'status' => 'fails',
                'data' => null,
            ], 404);
        }

        $totalDiscount = 0;
        $discountedTotal = $order->total;
        $discounts = [];

        foreach ($order->items as $item) {
            if ($discount = $this->itemDiscount($item)) {
                $discounts[] = $discount;

                $totalDiscount += $discount['discountAmount'];
                $discountedTotal -= $discount['discountAmount'];
            }
        }

        if ($discount = $this->orderDiscount($order)) {
            $discounts[] = $discount;

            $totalDiscount += $discount['discountAmount'];
            $discountedTotal -= $discount['discountAmount'];
        }

        return response()->json([
            'status' => 'ok',
            'data' => [
                'orderId' => $order->id,
                'discounts' => $discounts,
                'totalDiscount' => $totalDiscount,
                'discountedTotal' => $discountedTotal,
            ]
        ]);
    }

    public function delete(int $id): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($id);

        if (! $order) {
            return response()->json([
                'status' => 'fails',
                'data' => null,
            ], 404);
        }

        $order->delete();

        return response()->json([
            'status' => 'ok',
            'data' => null,
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     *
     * Açıklama: Order toplam tutarı ve item toplam tutarı zorunlu mu değil mi bilinmediği için
     *      hesaplama sipariş oluştururken yapılmıştır.
     */
    private function validator(array $data): ValidatorContracts
    {
        return Validator::make($data, [
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer'],
        ]);
    }

    private function itemDiscount($item): ?array
    {
        $product = Product::find($item->product_id);
        if ($product->category == 2 && $item->quantity >= 6) {
            return [
                'discountReason' => 'BUY_6_GET_1',
                'discountAmount' => $item->unitPrice,
                'subtotal' => $item->total - $item->unitPrice
            ];
        }

        return null;
    }

    private function orderDiscount($order): ?array
    {
        if ($order->total >= 1000) {
            return [
                'discountReason' => '10_PERCENT_OVER_1000',
                'discountAmount' => $order->total * 0.10,
                'subtotal' => $order->total - ($order->total * 0.10),
            ];
        }

        return null;
    }
}
