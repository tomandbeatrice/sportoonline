document.getElementById('refresh-token-btn').addEventListener('click', function () {
    fetch("/admin/token/refresh", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            vendor_id: VENDOR_ID,
            token: CURRENT_TOKEN
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById("vendor-token").value = data.data.token;
            document.getElementById("token-expiry").textContent = data.data.token_expiry;
            CURRENT_TOKEN = data.data.token;
            toastr.success("Token başarıyla yenilendi");
        } else {
            toastr.error(data.message || "Token yenileme başarısız");
        }
    })
    .catch(() => toastr.error("Sunucu hatası"));
});