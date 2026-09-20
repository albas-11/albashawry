<div class="card">
    <div class="card-header">
        <h4>Méthode de Paiement</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('payment.telr.initiate', $order) }}" method="POST">
            @csrf
            <div class="mb-3">
                <h5>Résumé de la Commande</h5>
                <p><strong>Numéro:</strong> #{{ $order->id }}</p>
                <p><strong>Total:</strong> {{ number_format($order->total, 2) }} {{ settings('currency') }}</p>
            </div>

            <div class="mb-3">
                <img src="https://www.telr.com/images/telr-logo.png" alt="Telr" height="40">
                <p class="text-muted small mt-2">
                    Paiement sécurisé par Telr. Toutes les cartes de crédit et débit acceptées.
                </p>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-lock-fill me-2"></i>
                Payer Maintenant
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Paiement sécurisé SSL 256-bit
            </small>
        </div>
    </div>
</div>
