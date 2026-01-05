<footer class="bg-dark text-light mt-5">
    <div class="container py-4">

        <div class="row text-center text-md-start align-items-center g-3">

            <div class="col-12 col-md">
                <span class="small">
                    © EcoRide 2025
                </span>
            </div>

            <div class="col-12 col-md-auto">
                <?php require __DIR__ . '/partials/footer-links.php'; ?>
            </div>

        </div>

    </div>
</footer>

<!-- Toast global (PASSIF) -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="app-toast" class="toast align-items-center" role="alert">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button"
                    class="btn-close me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
