<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Payment;
use Stripe\StripeClient;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function initiate(Request $request, Application $application)
    {
        if ($application->job->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Unlock Candidate: ' . $application->candidate->name],
                    'unit_amount' => 1000, // Amount in cents ($10.00)
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => env('FRONTEND_URL') . '/dashboard?payment=success',
            'cancel_url' => env('FRONTEND_URL') . '/dashboard?payment=cancelled',
            'payment_intent_data' => [
                'metadata' => [
                    'application_id' => $application->id,
                    'employer_id' => $request->user()->id,
                ],
            ],
        ]);

        return response()->json(['stripe_checkout_url' => $session->url]);
    }

    public function webhook(Request $request)
    {
        $endpointSecret = config('services.stripe.webhook');

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $endpointSecret
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid Stripe Signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;

            // Get the IDs we attached in the initiate() method
            $applicationId = $paymentIntent->metadata->application_id ?? null;
            $employerId = $paymentIntent->metadata->employer_id ?? null;

            if ($applicationId) {
                $application = Application::find($applicationId);
                $application->update(['is_paid' => true]);

                Payment::create([
                    'employer_id' => $employerId,
                    'application_id' => $applicationId,
                    'amount' => $paymentIntent->amount / 100, // Convert cents back to dollars
                    'currency' => $paymentIntent->currency,
                    'provider' => 'stripe',
                    'provider_payment_id' => $paymentIntent->id,
                    'status' => 'successful',
                    'paid_at' => now(),
                ]);
            }
        }

        // Always return a 200 OK to Stripe so they know we received it
        return response()->json(['status' => 'success']);
    }

    public function status(Request $request, Application $application)
    {
        return response()->json([
            'is_paid' => $application->is_paid,
            'payment' => $application->payment
        ]);
    }
}
