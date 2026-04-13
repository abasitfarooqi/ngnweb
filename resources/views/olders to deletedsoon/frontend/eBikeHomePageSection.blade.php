<style>
  <style>
    .ebike-title-responsive {
      font-size: 2.2rem;
      font-weight: 800;
      line-height: 1.1;
      letter-spacing: 0.5px;
    }
    .ebike-lead-responsive {
      font-size: 1.2rem;
      font-weight: 700;
      line-height: 1.3;
    }
    @media (max-width: 991.98px) {
      .ebike-title-responsive { font-size: 1.8rem; }
      .ebike-lead-responsive { font-size: 1rem; }
    }
    @media (max-width: 575.98px) {
      .ebike-title-responsive { font-size: 1.3rem; padding: 0 0.2rem; }
      .ebike-lead-responsive { font-size: 0.9rem; padding: 0 0.1rem; }
    }
  </style>
  <section class="parallax-section position-relative text-white text-center" style="">
    <div class="container position-relative z-2">
      <div class="row align-items-center">
        <div class="col-md-4">
          <img loading="lazy" src="{{ asset('assets/images/ebikes-rental-buy-london-banner.jpg') }}" class="ebike-banner-image" data-bs-toggle="modal" data-bs-target="#eBikeImageModal" style="cursor: pointer;" alt="E-Bikes in London">
        </div>
        <div class="col-md-8 align-items-center text-center">
          <h1 class="mb-3 ebike-title-responsive"> E-Bikes Revolution </h1>
          <p class="lead active-color fw-800 blinking-text ebike-lead-responsive" style="z-index:2001;">
            <b>Our first E-bike launches in <strong>6 weeks</strong>, eco-friendly and smart</b>
          </p>
        </div>
    </div>
    <style>
      .modal-backdrop {
        z-index: 1040;
      }
      .ebike-modal .modal-content {
        border: none;
        background: transparent !important;
        background-color: transparent !important;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
      }
      .ebike-modal .modal-header {
        background: linear-gradient(135deg, #1a2a6c, #b21f1f);
        border-bottom: none;
        padding: 15px 20px;
      }
      .ebike-modal .modal-title {
        color: #fff;
        font-weight: 600;
        letter-spacing: 0.5px;
      }
      .ebike-modal .btn-close {
        background-color: #fff;
        opacity: 0.8;
        padding: 10px;
        border-radius: 50%;
      }
      .ebike-modal .btn-close:hover {
        opacity: 1;
      }
      .ebike-modal .modal-body {
        padding: 0;
      }
      .ebike-modal .img-fluid {
        width: 100%;
        transition: transform 0.3s ease;
      }
      .ebike-modal .img-fluid:hover {
        transform: scale(1.02);
      }
    </style>
    <!-- Modal -->
    <div class="modal fade ebike-modal" id="eBikeImageModal" tabindex="-1" aria-labelledby="eBikeImageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="eBikeImageModalLabel">E-Bikes Revolution</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <img loading="lazy" src="{{ asset('assets/images/ebikes-rental-buy-london-banner.jpg') }}" class="img-fluid" alt="E-Bikes in London" loading="lazy">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bike PNGs -->
  <img loading="lazy" src="{{ asset('assets/images/bike-left.png') }}" class="parallax-bike bike-left" alt="Electric Bike Left">
  <img loading="lazy" src="{{ asset('assets/images/bike-right.png') }}" class="parallax-bike bike-right" alt="Electric Bike Right">
</section>

<script>
document.addEventListener("mousemove", (e) => {
  const x = e.clientX / window.innerWidth - 0.5;
  const y = e.clientY / window.innerHeight - 0.5;

  document.querySelector(".bike-left").style.transform = `translate(${x * 30}px, ${y * 20}px)`;
  document.querySelector(".bike-right").style.transform = `translate(${x * -30}px, ${y * -20}px)`;
});
</script>