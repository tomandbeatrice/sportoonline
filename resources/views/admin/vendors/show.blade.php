<!-- Token Bilgisi -->
<div class="mb-3">
    <label class="form-label">Token</label>
    <div class="input-group">
        <input type="text" class="form-control" id="vendor-token" value="{{ $vendor->token }}" readonly>
        <button class="btn btn-outline-secondary" id="refresh-token-btn">Yenile</button>
    </div>
    <small class="text-muted">Geçerlilik: <span id="token-expiry">{{ $vendor->token_expiry }}</span></small>
</div>

<!-- JS Sabitleri -->
<script>
    const VENDOR_ID = {{ $vendor->id }};
    let CURRENT_TOKEN = "{{ $vendor->token }}";
</script>
<script src="{{ asset('js/vendor-token.js') }}"></script>