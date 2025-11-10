<?php

namespace App\Services\Subscription;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionActivated;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Activa la suscripción de un usuario
     */
    public function activate(User $user): User
    {
        $user->start_date = now();
        $user->end_date = now()->addMonth();
        $user->subscribed = true;
        $user->save();

        // Lógica adicional
        Log::info('Subscription activated', [
            'user_id' => $user->id,
            'end_date' => $user->end_date
        ]);

        // Enviar email de confirmación
        // Mail::to($user->email)->send(new SubscriptionActivated($user));

        return $user;
    }

    /**
     * Renueva la suscripción mensual
     */
    public function renew(User $user): User
    {
        if ($user->end_date && now()->lessThanOrEqualTo($user->end_date)) {
            $user->end_date = Carbon::parse($user->end_date)->addMonth();
        } else {
            $user->start_date = now();
            $user->end_date = now()->addMonth();
        }

        $user->subscribed = true;
        $user->save();

        Log::info('Subscription renewed', [
            'user_id' => $user->id,
            'new_end_date' => $user->end_date
        ]);

        return $user;
    }

    /**
     * Cancela la suscripción
     */
    public function cancel(User $user): User
    {
        $user->subscribed = false;
        $user->save();

        Log::info('Subscription cancelled', [
            'user_id' => $user->id,
            'end_date' => $user->end_date
        ]);

        // Mail::to($user->email)->send(new SubscriptionCancelled($user));

        return $user;
    }

    /**
     * Procesa el pago y activa la suscripción
     */
    public function processPaymentAndActivate(User $user, array $paymentData): array
    {
        // Aquí iría la integración con Stripe/PayPal
        // $charge = Stripe::charge($paymentData);

        // Si el pago es exitoso
        $this->activate($user);

        return [
            'success' => true,
            'message' => 'Subscription activated successfully',
            'end_date' => $user->end_date
        ];
    }

    /**
     * Verifica si la suscripción está activa
     */
    public function isActive(User $user): bool
    {
        return $user->subscribed
            && $user->end_date
            && now()->lessThanOrEqualTo($user->end_date);
    }
}
