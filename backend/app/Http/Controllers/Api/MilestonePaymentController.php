<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Milestone;

class MilestonePaymentController extends Controller
{
    public function pay($id)
    {
        $milestone = Milestone::findOrFail($id);

        if ($milestone->status === 'paid') {
            return response()->json(['message' => 'This stage has already been paid for.'], 400);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Milestone payment: ' . $milestone->title,
                    ],
                    'unit_amount' => (int)($milestone->price * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => env('FRONTEND_URL') . '/projects/' . $milestone->freelancer_project_id . '?payment=success',
            'cancel_url' => env('FRONTEND_URL') . '/projects/' . $milestone->freelancer_project_id . '?payment=cancelled',
            'metadata' => [
                'milestone_id' => $milestone->id
            ]
        ]);

        $milestone->update([
            'stripe_session_id' => $session->id
        ]);

        return response()->json([
            'url' => $session->url
        ]);
    }
}
