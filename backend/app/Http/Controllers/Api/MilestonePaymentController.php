<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Milestone;
use Barryvdh\DomPDF\Facade\Pdf;

class MilestonePaymentController extends Controller
{
    public function pay($id)
    {
        $milestone = Milestone::findOrFail($id);

        if ($milestone->payment_status === 'paid') {
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

        $pdf = Pdf::loadView('invoices.milestone_invoice', [
            'milestone' => $milestone,
            'paymentUrl' => $session->url,
            'date' => now()->format('Y-m-d')
        ]);

        return $pdf->stream('Invoice-' . $milestone->title . '.pdf');
    }

    public function createMilestoneCheckout($id)
    {
        $milestone = Milestone::findOrFail($id);
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
            'project_id' => 'required|exists:freelancer_projects,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
        ]);

        $milestone = Milestone::create([
            'freelancer_project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'price' => $validated['amount'],
            'payment_status' => 'pending',
        ]);

        return response()->json($milestone);
    }
}
