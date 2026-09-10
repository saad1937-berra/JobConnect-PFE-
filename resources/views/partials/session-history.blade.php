<script>
    // Hide the saved document before history caching, then request fresh session state.
    window.addEventListener('pagehide', function (event) {
        if (event.persisted) document.documentElement.style.visibility = 'hidden';
    });
    window.addEventListener('pageshow', function (event) {
        const navigation = performance.getEntriesByType('navigation')[0];
        if (event.persisted || navigation?.type === 'back_forward') {
            document.documentElement.style.visibility = 'hidden';
            window.location.reload();
        }
    });
</script>
