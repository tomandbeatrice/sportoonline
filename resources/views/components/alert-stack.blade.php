<div id="alert-stack" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    @if($errors->any())
        <x-alert type="error" :message="$errors->first()" />
    @endif

    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" />
    @endif

    @if(session('info'))
        <x-alert type="info" :message="session('info')" />
    @endif
</div>

<script>
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => el.classList.remove('show'));
    }, 4000);
</script>