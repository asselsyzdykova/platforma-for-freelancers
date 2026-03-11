<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function createMilestoneCheckout(Request $request, $id)
    {
        $milestone = Milestone::findOrFail($id);
        $user = $request->user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => "Payment for milestone: " . $milestone->title,
                    ],
                    'unit_amount' => $milestone->amount * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'milestone_id' => $milestone->id,
                'type' => 'milestone_payment'
            ],
            'success_url' => env('FRONTEND_URL') . '/milestone/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('FRONTEND_URL') . '/projects/' . $milestone->project_id,
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
        ]);

        $milestone = Milestone::create([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        return response()->json($milestone);
    }
}
